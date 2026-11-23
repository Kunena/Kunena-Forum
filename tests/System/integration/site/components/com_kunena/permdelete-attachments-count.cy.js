// Deleting a message for ever took one file off the number of files of the topic (the paperclip of the lists): before the rights
// were checked, so also when the delete was refused, and whether the message had a file or not.
describe('Kunena delete for ever: the number of files of the topic', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const ids = {};

  const addMessage = (parent, subject) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, 3, 'ci-admin', ${admin}, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A text.')`).then(() => message.insertId));

  // The task with the token of the page of the topic.
  const permdelete = () => cy.request(`${base}&view=topic&catid=3&id=${ids.topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({
      url: `${base}&view=topic&catid=3&id=${ids.topic}&mesid=${ids.reply}&task=permdelete&${token}=1`,
      failOnStatusCode: false,
      followRedirect: false,
    });
  });

  const count = () => cy.task('queryDB', `SELECT attachments FROM #__kunena_topics WHERE id = ${ids.topic}`).then((rows) => rows[0].attachments);
  const replyExists = () => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_messages WHERE id = ${ids.reply}`).then((rows) => rows[0].n);

  beforeEach(() => {
    // A topic of the administrator: the first message has a file, the reply has none.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress permdelete ${stamp}', 0, 0, 0, 0, 0, 2, 0, 1, 0, 0, 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return addMessage(0, `Cypress permdelete ${stamp}`);
    }).then((first) => {
      ids.first = first;
      cy.task('queryDB', `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${first}, ${admin}, 0, '', 10, 'media/kunena/attachments/cypress-${stamp}', 'text/plain', 'cypress-${stamp}.txt', 'cypress-${stamp}.txt', '', 0, 0)`);

      return addMessage(first, 'Re: permdelete');
    }).then((reply) => {
      ids.reply = reply;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.first}, last_post_id = ${reply} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${admin}, ${ids.topic}, 3, 2, ${reply}, 1, 0, 0, '')`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE mesid = ${ids.first}`);
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('keeps the count when a user who may not delete asks for it', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    permdelete();
    replyExists().then((n) => expect(n, 'the reply is not deleted').to.eq(1));
    count().then((value) => expect(value, 'the files of the topic').to.eq(1));
  });

  it('keeps the count when the deleted message had no file', () => {
    cy.doFrontendLogin();
    permdelete();
    replyExists().then((n) => expect(n, 'the reply is deleted').to.eq(0));
    count().then((value) => expect(value, 'the files of the topic').to.eq(1));
  });
});
