// The page of the edit history of a message had its subject escaped twice in the title of the browser: "Q&A" read "Q&amp;A" (the
// subject was HTML, Joomla escapes the title again).
describe('Kunena edit history: the subject in the title', () => {
  const stamp = Date.now();
  const subject = `Q&A history ${stamp}`;
  const ids = {};

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'The text now')`);
      cy.task('queryDB', `INSERT INTO #__kunena_messages_history (message_id, edited_by, edited_time, reason, subject, message) VALUES (${message.insertId}, 797, UNIX_TIMESTAMP(), '', '${subject}', 'The text before')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
    });
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_history WHERE message_id = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('has the subject once escaped in the title', () => {
    cy.then(() => cy.request(`/index.php?option=com_kunena&view=topic&layout=history&catid=3&id=${ids.topic}&mesid=${ids.message}`)).then((page) => {
      const title = /<title>([^<]*)<\/title>/.exec(String(page.body));

      expect(title && title[1], 'the title').to.include(`Q&amp;A history ${stamp}`);
      expect(title && title[1], 'the title').not.to.include('Q&amp;amp;A');
    });
  });
});
