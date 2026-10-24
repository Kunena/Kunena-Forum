// The popups (task user.notifications) leave out the messages of the users whom the user ignores, like the e-mails of mentions:
// a reply or a mention of an ignored user gives no popup.
describe('Kunena popup notifications: not for ignored users', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};

  const popups = () => cy.request(`${base}&view=user&task=notifications&format=json&after=${ids.reply - 1}`).its('body.items').then((items) => items.map((item) => item.id));

  beforeEach(() => {
    // A second user of the forum, who replies in a topic of ci-user and mentions him.
    cy.task(
      'queryDB',
      "INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Noisy', 'ci-noisy', 'ci-noisy@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798",
    ).then((result) => {
      ids.second = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${ids.second}, 2)`);
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts) VALUES (${ids.second}, 1)`);
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress popup ${stamp}', 'cypress-popup-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 98, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-popup-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Popup ${stamp}', 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - 120, ${user.id}, 'x', '', 0, UNIX_TIMESTAMP() - 60, ${user.id}, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, 'ci-user', ${user.id}, '', 'Popup ${stamp}', UNIX_TIMESTAMP() - 120, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((first) => {
      ids.first = first.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${first.insertId}, 'My topic.')`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${first.insertId}, ${ids.topic}, ${ids.category}, 'ci-noisy', ${ids.second}, '', 'Re: Popup ${stamp}', UNIX_TIMESTAMP() - 60, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((reply) => {
      ids.reply = reply.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${reply.insertId}, 'Hello [mention userid=${user.id}]ci-user[/mention], a reply.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.first}, last_post_id = ${reply.insertId}, last_post_userid = ${ids.second} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_user_ignore WHERE user_id = ${user.id} AND ignored_id = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid IN (${ids.first}, ${ids.reply})`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id IN (${ids.first}, ${ids.reply})`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-popup-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.second}`);
  });

  it('gives a popup for the reply of a user who is not ignored', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => popups()).should('include', ids.reply);
  });

  it('gives no popup for the reply of an ignored user', () => {
    cy.then(() => cy.task('queryDB', `INSERT INTO #__kunena_user_ignore (user_id, ignored_id, created_time) VALUES (${user.id}, ${ids.second}, UNIX_TIMESTAMP())`));
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => popups()).should('not.include', ids.reply);
  });
});
