// The text of [private] is shown to nobody, in any case of the name of the tag ([PRIVATE] too). The plain text of a message (the
// description in the head of the page, the index of the search of the site, the tooltips) took out the opening tag [PRIVATE] only:
// the text in the block and the closing tag were left, and every visitor and the search engines read them in the head of the page.
describe('Kunena topic page: the text in [PRIVATE] in the head of the page', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const words = { open: `OPENTEXT${stamp}`, priv: `PRIVATETEXT${stamp}` };
  const text = `${words.open} [PRIVATE]${words.priv}[/PRIVATE] and the end`;
  const ids = {};

  before(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress private upper ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, '${text}', '', 0, UNIX_TIMESTAMP(), 797, '${text}', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;
      cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', 'Cypress private upper ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      ).then((message) => {
        ids.message = message.insertId;
        cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`);
        cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`);
      });
    });
  });

  after(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('gives a visitor the open text, not the text in [PRIVATE]', () => {
    cy.request(`${base}&view=topic&catid=3&id=${ids.topic}`).then((page) => {
      const description = /<meta name="description" content="([^"]*)"/.exec(page.body);

      expect(description, 'the description of the page').not.to.equal(null);
      expect(description[1], 'the open text in the description').to.contain(words.open);
      expect(page.body, 'the text in [PRIVATE]').not.to.contain(words.priv);
    });
  });
});
