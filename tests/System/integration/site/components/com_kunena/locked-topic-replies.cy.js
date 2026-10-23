// A topic which a moderator locked takes no reply of a user; a moderator can still reply.
describe('Kunena topics: replies to a locked topic', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const ids = {};

  const reply = (text) => cy.request(`${base}&view=topic&catid=3&id=${ids.topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=post`,
      form: true,
      body: { catid: 3, id: ids.topic, parentid: ids.message, subject: `Cypress locked ${stamp}`, message: text, [token]: '1' },
      failOnStatusCode: false,
      followRedirect: false,
    });
  });
  const replies = () => cy.task('queryDB', `SELECT userid FROM #__kunena_messages WHERE thread = ${ids.topic} AND parent > 0`).then((rows) => rows.map((row) => Number(row.userid)));

  before(() => {
    cy.kunena_setOption('floodProtection', '0');
  });

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress locked ${stamp}', 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', ${admin}, '', 'Cypress locked ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A locked topic.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.kunena_fixUserCounters();
    cy.task('queryDB', "UPDATE #__kunena_categories c SET numTopics = (SELECT COUNT(*) FROM #__kunena_topics t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0), numPosts = (SELECT COALESCE(SUM(t.posts), 0) FROM #__kunena_topics t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0) WHERE c.id = 3");
  });

  it('refuses the reply of a user and takes the reply of a moderator', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    reply('A reply of a user.');
    replies().should('deep.eq', []);

    cy.clearAllCookies();
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    reply('A reply of the moderator.');
    replies().should('deep.eq', [admin]);
  });
});
