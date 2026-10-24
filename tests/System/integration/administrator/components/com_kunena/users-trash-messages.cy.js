// The backend user list moves all the messages of the chosen users to the trash (task users.trashusermessages, for spammers):
// every message, not only one page of them (the list of the messages used the number of messages of a page).
describe('Kunena backend users: trash the messages of a user', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const total = 10;
  const ids = {};
  const published = () => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_messages WHERE userid = ${ids.user} AND hold = 0`).then((rows) => Number(rows[0].n));

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Spammer', 'ci-spammer-${stamp}', 'ci-spammer-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      ids.user = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts) VALUES (${result.insertId}, ${total})`);
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress spam ${stamp}', 'cypress-spam-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-spam-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Spam ${stamp}', 0, 0, 0, 0, 0, ${total}, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - 1000, 0, 'x', '', 0, UNIX_TIMESTAMP(), 0, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      // More messages of the user than one page shows (6 on the test site).
      for (let i = 0; i < total; i += 1) {
        cy.task(
          'queryDB',
          `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, 'ci-spammer', ${ids.user}, '', 'Spam ${stamp}', UNIX_TIMESTAMP() - ${1000 - i}, '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
        ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Spam ${i}.')`));
      }
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-spam-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category} OR user_id = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.user}`);
    cy.kunena_fixUserCounters();
  });

  it('moves all the messages of the user to the trash', () => {
    cy.then(() => published()).should('eq', total);
    cy.doAdministratorLogin();
    cy.request(`${admin}&view=users`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({ method: 'POST', url: `${admin}&task=users.trashusermessages`, form: true, body: { 'cid[]': ids.user, [token]: '1' }, followRedirect: false });
    });
    cy.then(() => published()).should('eq', 0);
  });
});
