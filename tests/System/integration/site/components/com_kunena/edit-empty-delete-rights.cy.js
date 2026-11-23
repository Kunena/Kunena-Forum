// A message whose text is made empty in the edit form is deleted instead. That is a delete: it may happen only when the user may
// delete the message (option userDeleteMessage). The check was the other way round: only the setting "when there are no replies"
// refused, so with "never" (the default) or "all except the first message" a user deleted his message, or the first message of his
// topic, by emptying its text.
describe('Kunena edit: emptying the text of a message', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const ids = {};
  const old = {};

  const addMessage = (parent, author, subject) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, 3, 'author', ${author}, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A text.')`).then(() => message.insertId));

  // The edit form, sent with an empty text (only a tag without content).
  const empty = (mesid) => cy.request(`${base}&view=topic&catid=3&id=${ids.topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=edit`,
      form: true,
      body: {
        catid: 3,
        id: ids.topic,
        mesid,
        subject: `Cypress empty edit ${stamp}`,
        message: '[b][/b]',
        [token]: '1',
      },
      failOnStatusCode: false,
      followRedirect: false,
    });
  });

  const holdOf = (mesid) => cy.task('queryDB', `SELECT hold FROM #__kunena_messages WHERE id = ${mesid}`).then((rows) => rows[0].hold);

  before(() => {
    cy.kunena_setOption('userEdit', '1').then((value) => {
      old.userEdit = value ?? null;
    });
    cy.kunena_getOption('userDeleteMessage').then((value) => {
      old.userDeleteMessage = value ?? null;
    });
  });

  after(() => {
    cy.kunena_setOption('userEdit', old.userEdit);
    cy.then(() => cy.kunena_setOption('userDeleteMessage', old.userDeleteMessage));
  });

  beforeEach(() => {
    // A topic of the user with a reply of the administrator and a reply of the user (the last message).
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress empty edit ${stamp}', 0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return addMessage(0, user.id, `Cypress empty edit ${stamp}`);
    }).then((first) => {
      ids.first = first;

      return addMessage(first, admin, 'Re: empty edit');
    }).then(() => addMessage(ids.first, user.id, 'Re: empty edit')).then((reply) => {
      ids.reply = reply;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.first}, last_post_id = ${reply} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${user.id}, ${ids.topic}, 3, 2, ${reply}, 1, 0, 0, ''), (${admin}, ${ids.topic}, 3, 1, 0, 0, 0, 0, '')`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('does not delete the message when the user may never delete his messages', () => {
    cy.kunena_setOption('userDeleteMessage', '0');
    cy.doFrontendLogin(user.name, user.password, false);
    empty(ids.reply);
    holdOf(ids.reply).then((hold) => expect(hold, 'the reply is not deleted').to.eq(0));
  });

  it('does not delete the first message when the user may delete all messages except the first one', () => {
    cy.kunena_setOption('userDeleteMessage', '3');
    cy.doFrontendLogin(user.name, user.password, false);
    empty(ids.first);
    holdOf(ids.first).then((hold) => expect(hold, 'the first message is not deleted').to.eq(0));
  });
});
