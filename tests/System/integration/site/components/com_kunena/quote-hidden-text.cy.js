// A quote of a message (the reply form with quote=1) puts the text of the message in the form. The text in [confidential] was left
// out, not the text in [private] (which the forum shows to nobody): the form of the reply gave it to the user who quoted the message.
describe('Kunena reply form: the quote of a message with hidden text', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x' };
  const words = { open: `OPENTEXT${stamp}`, priv: `PRIVATETEXT${stamp}`, conf: `CONFTEXT${stamp}`, hide: `HIDETEXT${stamp}` };
  const text = `${words.open} [private]${words.priv}[/private] [confidential]${words.conf}[/confidential] [hide]${words.hide}[/hide]`;
  const ids = {};

  const form = () => cy.request(`${base}&view=topic&layout=reply&catid=3&id=${ids.topic}&mesid=${ids.message}&quote=1`).its('body');

  before(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress quote ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;
      cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', 'Cypress quote ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
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

  it('gives a user the open text and the text in [hide], not the text in [private] or [confidential]', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    form().then((body) => {
      expect(body, 'the open text').to.contain(words.open);
      expect(body, 'the text in [hide]').to.contain(words.hide);
      expect(body, 'the text in [private]').not.to.contain(words.priv);
      expect(body, 'the text in [confidential]').not.to.contain(words.conf);
    });
  });
});
