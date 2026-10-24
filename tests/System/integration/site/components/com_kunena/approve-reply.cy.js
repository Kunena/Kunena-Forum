// A moderator approves one reply which waits for approval (task topic.approve with the number of the message): the reply is
// published and the moderator goes back to the topic - no error page. (The notification of the approval went through
// KunenaMessage::sendNotification(), a method which was removed.)
describe('Kunena moderation: approving a reply', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};

  const addMessage = (parent, hold, userid, name, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, ${ids.category}, '${name}', ${userid}, '', 'Cypress approve ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, ${hold}, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`).then(() => message.insertId));

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, review, params, iconset) VALUES (1, 'Cypress approve ${stamp}', 'cypress-approve-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-approve-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Cypress approve ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return addMessage(0, 0, 797, 'ci-admin', 'The question.');
    }).then((first) => {
      ids.first = first;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${first}, last_post_id = ${first} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 1, numPosts = 1 WHERE id = ${ids.category}`);

      // A reply of ci-user which waits for approval.
      return addMessage(first, 1, 798, 'ci-user', `The waiting reply ${stamp}.`);
    }).then((reply) => {
      ids.reply = reply;
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_inbox WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-approve-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.kunena_fixUserCounters();
  });

  it('publishes the reply and goes back to the topic', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.then(() => cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`)).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({ url: `${base}&view=topic&catid=${ids.category}&id=${ids.topic}&mesid=${ids.reply}&task=approve&${token}=1`, failOnStatusCode: false, followRedirect: false });
    }).then((response) => {
      expect(response.status, 'the answer of the approval').to.be.oneOf([301, 302, 303]);
    });
    cy.then(() => cy.task('queryDB', `SELECT hold FROM #__kunena_messages WHERE id = ${ids.reply}`)).its('0.hold').should('eq', 0);
  });
});
