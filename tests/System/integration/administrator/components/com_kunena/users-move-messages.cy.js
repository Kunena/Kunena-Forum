// The backend user list moves the topics of the messages of the chosen users to another category (task users.move, then
// user.moveMessages on the page that chooses the category): every topic, not only those of one page of messages.
describe('Kunena backend users: move the messages of a user', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const total = 10;
  const ids = { topics: [] };
  const addCategory = (name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress ${name} ${stamp}', 'cypress-${name}-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
  ).then((category) => {
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-${name}-${stamp}', 'catid', ${category.insertId}, 0)`);

    return cy.wrap(category.insertId);
  });
  const inCategory = (catid) => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_topics WHERE category_id = ${catid}`).then((rows) => Number(rows[0].n));
  const token = (url) => cy.request(url).then((page) => /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1]);

  beforeEach(() => {
    ids.topics = [];
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Mover', 'ci-mover-${stamp}', 'ci-mover-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      ids.user = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts) VALUES (${result.insertId}, ${total})`);
    });
    addCategory('target').then((catid) => {
      ids.target = catid;
    });
    addCategory('source').then((catid) => {
      ids.source = catid;

      // One topic per message, and more messages than one page shows (6 on the test site).
      for (let i = 0; i < total; i += 1) {
        cy.task(
          'queryDB',
          `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${catid}, 'Move ${stamp} ${i}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - 1000, ${ids.user}, 'x', '', 0, UNIX_TIMESTAMP() - 1000, ${ids.user}, 'x', '', 0, '')`,
        ).then((topic) => {
          ids.topics.push(topic.insertId);

          return cy.task(
            'queryDB',
            `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${catid}, 'ci-mover', ${ids.user}, '', 'Move ${stamp} ${i}', UNIX_TIMESTAMP() - ${1000 - i}, '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
          ).then((message) => {
            cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`);
            cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Move ${i}.')`);
          });
        });
      }
    });
  });

  afterEach(() => {
    const topics = ids.topics.join(',') || '0';
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread IN (${topics})`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread IN (${topics})`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id IN (${topics})`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id IN (${topics}) OR category_id IN (${ids.source}, ${ids.target})`);
    cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE category_id IN (${ids.source}, ${ids.target})`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias IN ('cypress-source-${stamp}', 'cypress-target-${stamp}')`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id IN (${ids.source}, ${ids.target}) OR user_id = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id IN (${ids.source}, ${ids.target})`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.user}`);
    cy.kunena_fixUserCounters();
  });

  it('moves the topics of all the messages of the user', () => {
    cy.then(() => inCategory(ids.source)).should('eq', total);
    cy.doAdministratorLogin();
    cy.then(() => token(`${admin}&view=users`)).then((t) => cy.request({ method: 'POST', url: `${admin}&task=users.move`, form: true, body: { 'cid[]': ids.user, [t]: '1' }, followRedirect: false }));
    cy.then(() => token(`${admin}&view=user&layout=move`)).then((t) => cy.request({ method: 'POST', url: `${admin}&task=user.moveMessages`, form: true, body: { catid: ids.target, [t]: '1' }, followRedirect: false }));
    cy.then(() => inCategory(ids.target)).should('eq', total);
    cy.then(() => inCategory(ids.source)).should('eq', 0);
  });
});
