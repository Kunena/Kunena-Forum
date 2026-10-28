// A quote can name the message it comes from: [quote="name" post=id]. The link to that message was made for any id, also for a
// message which the reader may not see: its address carries the alias of the category and of the topic (the words of its
// subject), so a quote with the id of a message of a closed category told every reader the subject of that topic. The link is
// made only to a message which the reader may read now.
describe('Kunena BBCode: the link of a quote to a message which the reader may not see', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const secret = `hiddensubject${stamp}`;
  const ids = {};

  const addTopic = (catid, subject, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${catid}, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${catid}, 'ci-admin', 797, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => {
    cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`);
    cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`);

    return cy.wrap({ topic: topic.insertId, message: message.insertId });
  }));

  before(() => {
    // A category for the managers only (Special) with a topic.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress closed ${stamp}', 'cypress-closed-${stamp}', '', '', '', '', '', 'joomla.level', 3, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-closed-${stamp}', 'catid', ${category.insertId}, 0)`);

      return addTopic(category.insertId, `Hiddensubject${stamp}`, 'The closed text.');
    }).then((hidden) => {
      ids.hidden = hidden;

      // A public topic which quotes the closed message, and message 11 of the test site (public).
      return addTopic(3, `Cypress quotes ${stamp}`, `[quote="Someone post=${hidden.message} userid=797"]Quoted from the closed topic.[/quote] [quote="Someone post=11 userid=798"]Quoted from the public topic.[/quote]`);
    }).then((open) => {
      ids.open = open;
    });
  });

  after(() => {
    [ids.open, ids.hidden].filter(Boolean).forEach(({ topic, message }) => {
      cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${message}`);
      cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${message}`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${topic}`);
      cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${topic}`);
    });
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-closed-${stamp}'`);
    cy.then(() => cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`));
  });

  it('gives a visitor the link to the public message only, nothing of the closed topic', () => {
    cy.request(`${base}&view=topic&catid=3&id=${ids.open.topic}`).its('body').then((body) => {
      expect(body, 'the quotes').to.contain('Quoted from the closed topic.').and.contain('Quoted from the public topic.');
      expect(body, 'the link to the public message').to.contain('which-template-do-you-use');
      expect(body.toLowerCase(), 'the subject of the closed topic').not.to.contain(secret);
      expect(body, 'the alias of the closed category').not.to.contain(`cypress-closed-${stamp}`);
    });
  });
});
