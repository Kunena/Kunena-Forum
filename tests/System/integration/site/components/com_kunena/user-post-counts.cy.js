// The number of posts of a user (shown in the profile and used for the rank) changes with his messages: +1 for a reply, -1 when it is
// deleted, +1 when it is brought back, and nothing more when a deleted message is removed for ever. A deleted topic takes the posts
// of its messages with it.
describe('Kunena counters: the posts of a user', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};

  const typeInEditor = (text) => {
    cy.window().should('have.property', 'sceditor');
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty')
      .then(($body) => cy.wrap($body).click().type(text, { parseSpecialCharSequences: false }));
  };

  const posts = () => cy.task('queryDB', `SELECT posts FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => Number(rows[0].posts));
  // The messages which count: published messages in topics which are published.
  const realPosts = () => cy.task(
    'queryDB',
    `SELECT COUNT(*) AS total FROM #__kunena_messages AS m INNER JOIN #__kunena_topics AS t ON t.id = m.thread WHERE m.userid = ${user.id} AND m.hold = 0 AND t.hold = 0`,
  ).then((rows) => Number(rows[0].total));

  const adminTask = (task, query) => cy.clearAllCookies().then(() => cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false).then(() => cy.request(`${base}&view=topic&catid=3&id=${ids.topic}`)).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];
    const mes = query.mesid ? `&mesid=${query.mesid}` : '';

    return cy.request({ url: `${base}&view=topic&catid=3&id=${ids.topic}${mes}&task=${task}&${token}=1`, failOnStatusCode: false, followRedirect: false });
  }));

  beforeEach(() => {
    // A topic of the administrator, in the category General Discussion.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress postcount ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', 'Cypress postcount ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.first = message.insertId;

      return cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`);
    }).then(() => cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.first}, last_post_id = ${ids.first} WHERE id = ${ids.topic}`));

    // The counter of the test site is old: it starts as the count of the messages.
    posts().then((value) => {
      ids.start = value;
    });
    realPosts().then((real) => cy.task('queryDB', `UPDATE #__kunena_users SET posts = ${real} WHERE userid = ${user.id}`));
    realPosts().then((real) => {
      ids.base = real;
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
    // The number of posts as it was (the counter is not part of the data which the task removes).
    cy.task('queryDB', `UPDATE #__kunena_users SET posts = ${ids.start} WHERE userid = ${user.id}`);
    // The counters of the users and the rows of the topics which are gone.
    cy.kunena_fixUserCounters();
  });

  it('counts a reply, a delete, an undelete and a removal for ever', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&layout=reply&catid=3&id=${ids.topic}`);
    typeInEditor('A reply which counts.');
    cy.get('#form_submit_button').should('not.be.disabled').click();
    posts().then((after) => expect(after, 'a reply').to.eq(ids.base + 1));

    cy.task('queryDB', `SELECT id FROM #__kunena_messages WHERE thread = ${ids.topic} AND userid = ${user.id}`).then((rows) => {
      const reply = rows[0].id;

      adminTask('delete', { mesid: reply });
      posts().then((after) => expect(after, 'the reply deleted').to.eq(ids.base));
      adminTask('undelete', { mesid: reply });
      posts().then((after) => expect(after, 'the reply brought back').to.eq(ids.base + 1));
      adminTask('delete', { mesid: reply });
      adminTask('permdelete', { mesid: reply });
      posts().then((after) => expect(after, 'the reply removed for ever').to.eq(ids.base));
    });
  });

  it('takes the posts of a deleted topic away, and gives them back with the topic', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&layout=reply&catid=3&id=${ids.topic}`);
    typeInEditor('A reply in a topic which is deleted.');
    cy.get('#form_submit_button').should('not.be.disabled').click();
    posts().then((after) => expect(after, 'a reply').to.eq(ids.base + 1));

    adminTask('delete', {});
    posts().then((after) => expect(after, 'the topic deleted').to.eq(ids.base));
    adminTask('undelete', {});
    posts().then((after) => expect(after, 'the topic brought back').to.eq(ids.base + 1));
  });
});
