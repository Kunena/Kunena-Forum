// The ignore list: a user who ignores another user does not see the topics of that user in the lists of topics (the category
// too), finds the messages of that user collapsed in a topic, and nobody can ignore himself, a moderator or an administrator.
// Stop ignoring takes the user off the list, and ignoring somebody ends following him.
describe('Kunena ignore list', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const ids = {};

  const addMessage = (topic, userid, name, subject, text, age) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic}, ${ids.category}, '${name}', ${userid}, '', '${subject}', UNIX_TIMESTAMP() - ${age}, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`).then(() => message.insertId));

  const addTopic = (userid, name, subject, age) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - ${age}, ${userid}, 'x', '', 0, UNIX_TIMESTAMP() - ${age}, ${userid}, 'x', '', 0, '')`,
  ).then((topic) => addMessage(topic.insertId, userid, name, subject, `First post of ${subject}`, age).then((message) => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message}, last_post_id = ${message} WHERE id = ${topic.insertId}`,
  )).then(() => topic.insertId));

  const token = (page) => /name="([0-9a-f]{32})" value="1"/.exec(page)?.[1] ?? 'x';
  const userTask = (task, userid, withToken = true) => cy.request(`${base}&view=home`).then((page) => cy.request({
    url: `${base}&view=user&task=${task}&userid=${userid}${withToken ? `&${token(page.body)}=1` : ''}`,
    failOnStatusCode: false,
    followRedirect: false,
  }));
  const ignored = () => cy.task('queryDB', `SELECT ignored_id FROM #__kunena_user_ignore WHERE user_id = ${user.id}`).then((rows) => rows.map((row) => Number(row.ignored_id)));

  beforeEach(() => {
    // A second user of the forum (a normal registered user), who started a topic and replied in a topic of the administrator.
    cy.task(
      'queryDB',
      "INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Ignored', 'ci-ignored', 'ci-ignored@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798",
    ).then((result) => {
      ids.second = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${ids.second}, 2)`);
      // Who wrote in the forum has a forum profile.
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts) VALUES (${ids.second}, 2)`);
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress ignore ${stamp}', 'cypress-ignore-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 91, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-ignore-${stamp}', 'catid', ${category.insertId}, 0)`);

      return addTopic(ids.second, 'ci-ignored', `Cypress ignored topic ${stamp}`, 600);
    }).then((topic) => {
      ids.ignoredTopic = topic;

      return addTopic(admin, 'ci-admin', `Cypress admin topic ${stamp}`, 300);
    }).then((topic) => {
      ids.adminTopic = topic;

      return addMessage(topic, ids.second, 'ci-ignored', `Re: Cypress admin topic ${stamp}`, `Reply of the ignored user ${stamp}`, 120);
    }).then((reply) => {
      cy.task('queryDB', `UPDATE #__kunena_topics SET posts = 2, last_post_id = ${reply}, last_post_time = UNIX_TIMESTAMP() - 120, last_post_userid = ${ids.second} WHERE id = ${ids.adminTopic}`);
      cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 2, numPosts = 3, last_topic_id = ${ids.adminTopic}, last_post_time = UNIX_TIMESTAMP() - 120 WHERE id = ${ids.category}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${ids.category}`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-ignore-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_ignore WHERE user_id IN (${user.id}, ${ids.second}) OR ignored_id IN (${user.id}, ${ids.second})`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_follow WHERE user_id IN (${user.id}, ${ids.second}) OR followed_id IN (${user.id}, ${ids.second})`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.second}`);
  });

  it('leaves the topics of an ignored user out of the lists of the user who ignores him, and only of his', () => {
    const pages = () => [`${base}&view=category&catid=${ids.category}`, `${base}&view=topics&layout=default&mode=replies`];

    cy.doFrontendLogin(user.name, user.password, false);
    userTask('ignore', ids.second);
    ignored().should('deep.eq', [ids.second]);
    pages().forEach((url) => cy.request(url).its('body').then((body) => {
      expect(String(body), `${url}: the topic of the administrator`).to.include(`Cypress admin topic ${stamp}`);
      expect(String(body), `${url}: the topic of the ignored user`).to.not.include(`Cypress ignored topic ${stamp}`);
    }));

    // A visitor (and every other user) still sees it.
    cy.clearCookies();
    pages().forEach((url) => cy.request(url).its('body').should('include', `Cypress ignored topic ${stamp}`));
  });

  it('collapses the messages of an ignored user in a topic', () => {
    cy.task('queryDB', `INSERT INTO #__kunena_user_ignore (user_id, ignored_id, created_time) VALUES (${user.id}, ${ids.second}, UNIX_TIMESTAMP())`);
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&catid=${ids.category}&id=${ids.adminTopic}`);
    cy.get('details.kignored-message').should('have.length', 1).and('not.have.attr', 'open');
    cy.get('details.kignored-message summary').should('contain', 'a user you ignore');
    cy.get('details.kignored-message').should('contain', `Reply of the ignored user ${stamp}`);
    cy.contains(`First post of Cypress admin topic ${stamp}`).parents('details').should('have.length', 0);
  });

  it('refuses to ignore oneself, an administrator, a moderator, and a request without the token', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    userTask('ignore', user.id);
    userTask('ignore', admin);
    userTask('ignore', ids.second, false);
    // A moderator of the whole forum.
    cy.task('queryDB', `UPDATE #__kunena_users SET moderator = 1 WHERE userid = ${ids.second}`);
    cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${ids.second}, 0, 1, 0, 0, '')`);
    userTask('ignore', ids.second);
    ignored().should('deep.eq', []);
  });

  it('stops ignoring on request, and ignoring a user ends following him', () => {
    cy.task('queryDB', `INSERT INTO #__kunena_user_follow (user_id, followed_id, created_time) VALUES (${user.id}, ${ids.second}, UNIX_TIMESTAMP())`);
    cy.doFrontendLogin(user.name, user.password, false);
    userTask('ignore', ids.second);
    ignored().should('deep.eq', [ids.second]);
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_user_follow WHERE user_id = ${user.id} AND followed_id = ${ids.second}`).its('0.n').should('eq', 0);

    // Following an ignored user is refused.
    userTask('follow', ids.second);
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_user_follow WHERE user_id = ${user.id} AND followed_id = ${ids.second}`).its('0.n').should('eq', 0);

    // Without the token the user stays on the list; with it he is taken off.
    userTask('unignore', ids.second, false);
    ignored().should('deep.eq', [ids.second]);
    userTask('unignore', ids.second);
    ignored().should('deep.eq', []);
    cy.request(`${base}&view=category&catid=${ids.category}`).its('body').should('include', `Cypress ignored topic ${stamp}`);
  });
});
