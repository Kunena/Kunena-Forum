// The text in [private] is shown to nobody in the message (the tag shows a note in its place). The feeds left out the text in
// [confidential] and [hide], not the one in [private]: a feed without HTML (option rssAllowHtml off) gave the text of the message as
// it is stored, the text in [private] with it, to every visitor.
describe('Kunena feeds: the text in [private]', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const secret = `PRIVATETEXT${stamp}`;
  const text = `An open start and [private]${secret}[/private] the end.`;
  const old = {};

  before(() => {
    cy.kunena_setOption('enableRss', '1').then((value) => {
      old.enableRss = value ?? null;
    });
    cy.kunena_setOption('rssAllowHtml', '0').then((value) => {
      old.rssAllowHtml = value ?? null;
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress private ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, '${text}', '', 0, UNIX_TIMESTAMP(), 797, '${text}', '', 0, '')`,
    ).then((topic) => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', 'Cypress private ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`);
    }));
  });

  after(() => {
    cy.kunena_setOption('enableRss', old.enableRss);
    cy.kunena_setOption('rssAllowHtml', old.rssAllowHtml);
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = 'Cypress private ${stamp}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
  });

  it('gives the open text of the message, without the text in [private]', () => {
    // The feed of the topics with the first messages, of the messages, and of the last messages of the topics.
    ['topics', 'posts', 'replies'].map((mode) => `${base}&view=topics&format=feed&layout=feed&type=rss&mode=${mode}`).forEach((url) => {
      cy.request(url).its('body').then((body) => {
        expect(body, `the topic in ${url}`).to.contain(`Cypress private ${stamp}`);
        expect(body, `the open text in ${url}`).to.contain('An open start');
        expect(body, `the private text in ${url}`).not.to.contain(secret);
      });
    });
  });
});
