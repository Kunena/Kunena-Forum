// The author of a topic which waits for approval (a category with review) can open it and read his message, styled as
// unapproved and without the approve button; another user and a guest cannot open it. After an edit of the first message the
// author is on his topic, not on the category (issue #5381).
describe('Kunena moderation: the author reads his own unapproved topic', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const other = `ci-other-${stamp}`;
  const text = `My waiting question ${stamp}.`;
  const ids = {};

  const topicUrl = () => `${base}&view=topic&catid=${ids.category}&id=${ids.topic}`;

  beforeEach(() => {
    // A second registered user, no moderator.
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Other', '${other}', '${other}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = ${user.id}`,
    ).then((result) => {
      ids.other = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, review, params, iconset) VALUES (1, 'Cypress own unapproved ${stamp}', 'cypress-own-unapproved-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-own-unapproved-${stamp}', 'catid', ${category.insertId}, 0)`);

      // The topic of ci-user and its first message, both waiting for the approval (hold 1).
      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Cypress own unapproved ${stamp}', 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, ${ids.category}, '${user.name}', ${user.id}, '', 'Cypress own unapproved ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 1, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.first = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${ids.first}, '${text}')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.first}, last_post_id = ${ids.first} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-own-unapproved-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.other}`);
    cy.task('queryDB', `DELETE FROM #__kunena_sessions WHERE userid = ${ids.other}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${ids.other}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE user_id = ${ids.other}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.other}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.other}`);
    cy.kunena_fixUserCounters();
  });

  it('shows the author his message as unapproved, without the approve button', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => cy.request(topicUrl())).then((response) => {
      expect(response.status, 'the status').to.eq(200);
      expect(response.body, 'the page').not.to.include('task=approve');
    });
    cy.then(() => cy.visit(topicUrl()));
    cy.get('.message-unapproved').should('contain', text);
  });

  it('does not show the topic to another user', () => {
    cy.doFrontendLogin(other, user.password, false);
    cy.then(() => cy.request({ url: topicUrl(), failOnStatusCode: false })).then((response) => {
      expect(response.status, 'the status').to.eq(403);
      expect(response.body, 'the page').not.to.include(text);
    });
  });

  it('does not show the topic to a guest', () => {
    cy.then(() => cy.request({ url: topicUrl(), failOnStatusCode: false })).then((response) => {
      expect(response.status, 'the status').to.eq(401);
      expect(response.body, 'the page').not.to.include(text);
    });
  });

  it('brings the author back to his topic after an edit of its first message', () => {
    const edited = `My edited question ${stamp}.`;

    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=home`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=edit`,
        form: true,
        body: { mesid: ids.first, catid: ids.category, id: ids.topic, subject: `Cypress own unapproved ${stamp}`, message: edited, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    }).then((response) => {
      expect(response.status, 'the answer of the edit').to.be.oneOf([301, 302, 303]);
      ids.location = response.redirectedToUrl || response.headers.location;
    });

    // The message waits for the approval still.
    cy.then(() => cy.task('queryDB', `SELECT hold FROM #__kunena_messages WHERE id = ${ids.first}`)).its('0.hold').should('eq', 1);

    // The address of the redirect is the topic page with the edited message (the category does not list the unapproved topic).
    cy.then(() => cy.visit(ids.location));
    cy.get('.message-unapproved').should('contain', edited);
  });
});
