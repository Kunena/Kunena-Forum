// The Quote button of a message puts its [quote] block into the reply form. The name of the block was the name of the user of the
// message (userid): a message of a visitor has none, so the quote of a visitor's message was "anonymous wrote:", where Multi-quote
// and the topic gave the name of the visitor.
describe('Kunena reply form: the quote of the message of a visitor', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x' };
  const subject = `Cypress quote visitor ${stamp}`;
  const visitor = `Visitor${stamp}`;
  const ids = {};

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 0, 'x', '${visitor}', 0, UNIX_TIMESTAMP(), 0, 'x', '${visitor}', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, '${visitor}', 0, '', '${subject}', UNIX_TIMESTAMP() - 3600, '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.first = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A post of a visitor ${stamp}.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('names the visitor in the quote', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=topic&layout=reply&catid=3&id=${ids.topic}&mesid=${ids.first}&quote=1`).then((page) => {
      expect(page.body, 'the quote is in the editor').to.include(`A post of a visitor ${stamp}.`);
      expect(page.body, 'the name in the quote').to.include(`[quote=&quot;${visitor} post=${ids.first}`);
    });
  });
});
