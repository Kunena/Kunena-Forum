// A notification in the inbox shows the subject of the topic and a link: only while the user may read the message. When the
// topic is gone or out of his reach (deleted, moved into a closed category, access taken away), the notification stays, without
// the subject and the link. (Before, the inbox showed the subject of a topic which the user could not read.)
describe('Kunena inbox: the notifications of messages which the user can not read', () => {
  const base = '/index.php?option=com_ajax&plugin=inbox&group=kunena&format=json';
  const stamp = Date.now();
  const words = { closed: `CLOSEDNOTE${stamp}`, open: `OPENNOTE${stamp}` };
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};

  const notify = (topic, message, subject) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_inbox (user_id, sender_id, type, conversation_id, subject, body, topic_id, message_id, created_time, read_time, deleted_recipient, deleted_sender) VALUES (${user.id}, 797, 'subscription', 0, '${subject}', '', ${topic}, ${message}, UNIX_TIMESTAMP(), 0, 0, 0)`,
  );

  beforeEach(() => {
    // A category for the access level Special (3): ci-user does not read it.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Closed ${stamp}', 'closed-note-${stamp}', '', '', '', '', '', 'joomla.level', 3, 1, 1, 8, 1, 95, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, '${words.closed}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, ${ids.category}, 'ci-admin', 797, '', '${words.closed}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${ids.message}, 'x')`);
      notify(ids.topic, ids.message, words.closed);
      // Message 11 of topic 9 in the open category 3.
      notify(9, 11, words.open);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_inbox WHERE user_id = ${user.id} AND subject IN ('${words.closed}', '${words.open}')`);
  });

  it('shows the subject of a readable topic, and not that of a topic in a category which he can not read', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request({ url: `${base}&task=list`, failOnStatusCode: false }).its('body').then((body) => {
      const list = JSON.stringify(body);

      expect(list, 'the readable topic').to.include(words.open);
      expect(list, 'the closed topic').not.to.include(words.closed);
    });
  });
});
