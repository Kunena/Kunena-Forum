// A reply with a quote puts the text of the post in the form of the reply, without the confidential text (only the author and
// the moderators see it). The text was taken out with a regular expression which was case sensitive and stopped at the first
// closing tag: [CONFIDENTIAL]...[/CONFIDENTIAL], or the text after a block in a block, came in the form of another user.
describe('Kunena reply with a quote: the confidential text of the post', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const words = { open: `zqxopen${stamp}`, capitals: `zqxcaps${stamp}`, nested: `zqxnest${stamp}` };
  const text = `${words.open} [CONFIDENTIAL]${words.capitals}[/CONFIDENTIAL] [confidential]a[confidential]b[/confidential]${words.nested}[/confidential]`;
  const ids = {};

  before(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress quote ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, '${text}', '', 0, UNIX_TIMESTAMP(), 797, '${text}', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, 3, 'ci-admin', 797, '', 'Cypress quote ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${ids.message}, '${text}')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.message}, last_post_id = ${ids.message} WHERE id = ${ids.topic}`);
    });
  });

  after(() => {
    if (!ids.topic) {
      return;
    }
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.kunena_fixCategoryCounters();
  });

  it('keeps the confidential text out of the form of another user', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(`${base}&view=topic&layout=reply&catid=3&id=${ids.topic}&mesid=${ids.message}&quote=1`).then((page) => {
      expect(page.body, 'the open text').to.include(words.open);
      expect(page.body, 'the confidential text in capitals').not.to.include(words.capitals);
      expect(page.body, 'the confidential text after a block in a block').not.to.include(words.nested);
    });
  });
});
