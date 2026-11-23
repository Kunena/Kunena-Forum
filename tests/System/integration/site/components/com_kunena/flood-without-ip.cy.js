// The flood protection makes a user wait between two posts. It found the last post by the IP address only: without IP tracking every
// address is empty, so the last post of anybody counted, and a member was refused because another member had just posted.
describe('Kunena flood protection without IP tracking', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const old = {};
  const ids = {};

  before(() => {
    cy.kunena_setOption('floodProtection', '60').then((value) => {
      old.floodProtection = value ?? null;
    });
    cy.kunena_setOption('ipTracking', '0').then((value) => {
      old.ipTracking = value ?? null;
    });
  });

  after(() => {
    cy.kunena_setOption('floodProtection', old.floodProtection);
    cy.then(() => cy.kunena_setOption('ipTracking', old.ipTracking));
  });

  beforeEach(() => {
    // A topic of the administrator, posted a moment ago (without an address, as without IP tracking).
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress flood ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', 'Cypress flood ${stamp}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.first = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'The first post.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('lets a member reply although another member has just posted', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=topic&layout=reply&catid=3&id=${ids.topic}`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: {
          catid: 3,
          id: ids.topic,
          parentid: ids.first,
          subject: `Re: Cypress flood ${stamp}`,
          message: `A reply of the member ${stamp}.`,
          [token]: '1',
        },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_messages WHERE thread = ${ids.topic} AND userid = ${user.id}`).then((rows) => {
      expect(rows[0].n, 'the reply of the member').to.eq(1);
    });
  });
});
