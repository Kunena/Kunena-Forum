// With the option "Allow moderators to permdelete" a moderator removed one message for ever with its button (which asks the
// topic), but not the messages he ticked in a list of posts: that task asks the message, and the category wanted an
// administrator for it. The category asks for a moderator now, as it does for a topic; the option still decides.
describe('Kunena posts: a moderator removes ticked messages for ever', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const moderator = 798;
  const ids = {};
  // The option moderatorPermDelete when a test changed it (put back after the test, also after a failure).
  let permDelete;

  const message = (parent) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, 3, 'CI Author', ${ids.user}, '', 'Cypress permdel posts ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((row) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${row.insertId}, 'Text ${stamp}')`).then(() => row.insertId));

  // The task of the list of posts with the reply ticked, as the user who is logged in.
  const removeTicked = () => cy.request(`${base}&view=topic&catid=3&id=${ids.topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({
      method: 'POST',
      url: `${base}&view=topics`,
      form: true,
      body: { task: 'permdel_posts', [`posts[${ids.reply}]`]: 1, [token]: 1 },
      failOnStatusCode: false,
      followRedirect: false,
    });
  });

  const messages = () => cy.task('queryDB', `SELECT id FROM #__kunena_messages WHERE thread = ${ids.topic} ORDER BY id`).then((rows) => rows.map((row) => Number(row.id)));

  const makeModerator = () => {
    cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${moderator}, 0, 1, UNIX_TIMESTAMP(), 0, '') ON DUPLICATE KEY UPDATE role = 1`);
    cy.task('queryDB', `UPDATE #__kunena_users SET moderator = 1 WHERE userid = ${moderator}`);
  };

  const setOption = (value) => cy.kunena_setOption('moderatorPermDelete', value).then((old) => {
    permDelete = old ?? null;
  });

  beforeEach(() => {
    // A user of the forum with one topic of two messages.
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) VALUES ('CI Author', 'ci-permdel-${stamp}', 'ci-permdel-${stamp}@example.com', 'x', 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '')`,
    ).then((user) => {
      ids.user = user.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${ids.user}, 2)`);
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts) VALUES (${ids.user}, 2)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress permdel posts ${stamp}', 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${ids.user}, 'x', '', 0, UNIX_TIMESTAMP(), ${ids.user}, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return message(0);
    }).then((first) => {
      ids.first = first;

      return message(first);
    }).then((reply) => {
      ids.reply = reply;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.first}, last_post_id = ${ids.reply} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${ids.user}, ${ids.topic}, 3, 2, ${ids.reply}, 1, 0, 0, '')`);
    });
  });

  afterEach(() => {
    if (permDelete !== undefined) {
      cy.kunena_setOption('moderatorPermDelete', permDelete);
      permDelete = undefined;
    }

    cy.task('queryDB', `UPDATE #__kunena_users SET moderator = 0 WHERE userid = ${moderator}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${moderator} AND category_id = 0`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid IN (${ids.first}, ${ids.reply})`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.user}`);
    cy.kunena_fixUserCounters();
    cy.task('queryDB', "UPDATE #__kunena_categories c SET numTopics = (SELECT COUNT(*) FROM #__kunena_topics t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0), numPosts = (SELECT COALESCE(SUM(t.posts), 0) FROM #__kunena_topics t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0) WHERE c.id = 3");
  });

  it('removes the ticked message when the option allows it to the moderators', () => {
    setOption('1');
    makeModerator();
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    removeTicked();

    messages().should('deep.equal', [ids.first]);
  });

  it('keeps the message when the option does not allow it to the moderators', () => {
    setOption('0');
    makeModerator();
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    removeTicked();

    messages().then((list) => expect(list, 'the messages of the topic').to.deep.equal([ids.first, ids.reply]));
  });

  it('keeps the message for a member who is no moderator', () => {
    setOption('1');
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    removeTicked();

    messages().then((list) => expect(list, 'the messages of the topic').to.deep.equal([ids.first, ids.reply]));
  });
});
