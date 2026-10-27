// The image of the page of a topic (the meta tag og:image) was the first [img] of the first message, read in the text of the
// message with its BBCode: also an image in [confidential] (for the author and the moderators) or in [hide] (for the users who are
// logged in). Every visitor, and the search engines, got the address of that image. The hidden parts are left out now.
describe('Kunena topic page: the image in the head of the page', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const image = (name) => `https://example.com/${name}-${stamp}.png`;

  const addTopic = (name, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress image ${name} ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, '${text}', '', 0, UNIX_TIMESTAMP(), 797, '${text}', '', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', 'Cypress image ${name} ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => {
    ids[name] = topic.insertId;
    cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`);
    cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`);
  }));

  const ogImage = (name) => cy.request(`${base}&view=topic&catid=3&id=${ids[name]}`).then((page) => {
    const tag = /<meta property="og:image" content="([^"]*)"/.exec(page.body);

    return tag ? tag[1] : '';
  });

  beforeEach(() => {
    addTopic('open', `An open image [img]${image('open')}[/img]`);
    addTopic('confidential', `[confidential][img]${image('confidential')}[/img][/confidential] and some text`);
    addTopic('hidden', `[hide][img]${image('hidden')}[/img][/hide] and some text`);
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE 'Cypress image % ${stamp}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
  });

  it('takes an open image, and not one of a hidden part', () => {
    ogImage('open').should('eq', image('open'));
    ogImage('confidential').should('not.contain', `confidential-${stamp}`);
    ogImage('hidden').should('not.contain', `hidden-${stamp}`);
  });
});
