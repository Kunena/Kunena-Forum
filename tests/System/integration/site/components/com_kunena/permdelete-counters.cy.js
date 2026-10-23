// A topic which a moderator removes for ever takes the posts and the thanks of its author with it: the counters of a user whose
// only topic it was go back to 0. (The recounts only set the users who still had posts or thanks: the last ones kept the old
// numbers - and a rank, or the access of the rank plugin, which comes from the number of posts.)
describe('Kunena counters: a topic removed for ever', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const admin = 797;
  const ids = {};

  const counters = () => cy.task('queryDB', `SELECT posts, thankyou FROM #__kunena_users WHERE userid = ${ids.user}`).then((rows) => rows[0]);

  const adminTask = (task) => cy.request(`${base}&view=topic&catid=3&id=${ids.topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ url: `${base}&view=topic&catid=3&id=${ids.topic}&task=${task}&${token}=1`, failOnStatusCode: false, followRedirect: false });
  });

  beforeEach(() => {
    // A user of the forum with one topic (one post), which the administrator has thanked.
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) VALUES ('CI Spammer', 'ci-spammer-${stamp}', 'ci-spammer-${stamp}@example.com', 'x', 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '')`,
    ).then((user) => {
      ids.user = user.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${ids.user}, 2)`);
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts, thankyou) VALUES (${ids.user}, 1, 1)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress spam ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${ids.user}, 'x', '', 0, UNIX_TIMESTAMP(), ${ids.user}, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, 3, 'CI Spammer', ${ids.user}, '', 'Cypress spam ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Buy things.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${ids.user}, ${ids.topic}, 3, 1, ${message.insertId}, 1, 0, 0, '')`);
      cy.task('queryDB', `INSERT INTO #__kunena_thankyou (postid, userid, targetuserid, time) VALUES (${message.insertId}, ${admin}, ${ids.user}, NOW())`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_thankyou WHERE targetuserid = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.user}`);
    cy.kunena_fixUserCounters();
    cy.task('queryDB', "UPDATE #__kunena_categories c SET numTopics = (SELECT COUNT(*) FROM #__kunena_topics t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0), numPosts = (SELECT COALESCE(SUM(t.posts), 0) FROM #__kunena_topics t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0) WHERE c.id = 3");
  });

  it('sets the posts and the thanks of the author back to 0', () => {
    counters().then((row) => {
      expect(Number(row.posts), 'the posts before').to.eq(1);
      expect(Number(row.thankyou), 'the thanks before').to.eq(1);
    });

    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    adminTask('delete');
    adminTask('permdelete');

    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE id = ${ids.topic}`).then((rows) => expect(rows, 'the topic').to.have.length(0));
    counters().then((row) => {
      expect(Number(row.posts), 'the posts of the author').to.eq(0);
      expect(Number(row.thankyou), 'the thanks of the author').to.eq(0);
    });
  });

  it('sets the posts back to 0 also when the topic is removed for ever without being deleted first', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    adminTask('permdelete');

    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE id = ${ids.topic}`).then((rows) => expect(rows, 'the topic').to.have.length(0));
    counters().then((row) => {
      expect(Number(row.posts), 'the posts of the author').to.eq(0);
      expect(Number(row.thankyou), 'the thanks of the author').to.eq(0);
    });
  });
});
