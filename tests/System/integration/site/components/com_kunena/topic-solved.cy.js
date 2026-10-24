// The button Mark as solved (task topic.solved): the author of a topic and the moderators switch the topic icon "solved" on and
// off; a visitor, the author of a locked topic, a banned author and a request without the token change nothing.
describe('Kunena topics: mark as solved', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const solved = 8;
  const ids = {};
  let oldBan;

  const icon = () => cy.task('queryDB', `SELECT icon_id FROM #__kunena_topics WHERE id = ${ids.topic}`).then((rows) => Number(rows[0].icon_id));
  const markSolved = (withToken = true) => cy.request(`${base}&view=home`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({
      url: `${base}&view=topic&task=solved&catid=${ids.category}&id=${ids.topic}${withToken ? `&${token}=1` : ''}`,
      failOnStatusCode: false,
      followRedirect: false,
    });
  });

  beforeEach(() => {
    cy.task('queryDB', `SELECT CAST(banned AS CHAR) AS banned FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      oldBan = rows[0].banned;
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress solved ${stamp}', 'cypress-solved-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 94, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-solved-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Cypress solved ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - 60, ${user.id}, 'x', '', 0, UNIX_TIMESTAMP() - 60, ${user.id}, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, 'ci-user', ${user.id}, '', 'Cypress solved ${stamp}', UNIX_TIMESTAMP() - 60, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Is this solved?')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 1, numPosts = 1, last_topic_id = ${ids.topic}, last_post_time = UNIX_TIMESTAMP() - 60 WHERE id = ${ids.category}`);
    });
  });

  afterEach(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET banned = '${oldBan}' WHERE userid = ${user.id}`));
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${ids.category}`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-solved-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('lets the author mark his topic as solved and back, with the token only', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    markSolved(false);
    icon().should('eq', 0);
    markSolved();
    icon().should('eq', solved);
    markSolved();
    icon().should('eq', 0);
  });

  it('lets a moderator mark the topic of another user as solved', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    markSolved();
    icon().should('eq', solved);
  });

  it('refuses a visitor and the author of a locked topic', () => {
    markSolved();
    icon().should('eq', 0);
    cy.task('queryDB', `UPDATE #__kunena_topics SET locked = 1 WHERE id = ${ids.topic}`);
    cy.doFrontendLogin(user.name, user.password, false);
    markSolved();
    icon().should('eq', 0);
  });

  it('refuses a banned author', () => {
    cy.task('queryDB', `UPDATE #__kunena_users SET banned = DATE_ADD(NOW(), INTERVAL 1 DAY) WHERE userid = ${user.id}`);
    cy.doFrontendLogin(user.name, user.password, false);
    markSolved();
    icon().should('eq', 0);
  });
});
