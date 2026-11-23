// After a delete of topics for good (task topics.permdel), the rows of the files whose message is gone are removed. The query took
// every file without a message, also the uploads which wait for their message (mesid 0): the uploads of every user who was writing
// a post were lost (KunenaAttachmentHelper::cleanup() and the diagnostics leave them, mesid > 0).
describe('Kunena topics: a delete for good keeps the pending uploads of the users', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const admin = { name: Cypress.expose('username'), password: Cypress.expose('password'), id: 797 };
  const writer = 798;
  const subject = `Cypress permdel pending ${stamp}`;
  const pending = `pending-${stamp}.txt`;
  const ids = {};

  beforeEach(() => {
    // An upload of another member which waits for its message.
    cy.task('queryDB', `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (0, ${writer}, 0, '', 1, 'media/kunena/attachments/cypress-${stamp}', 'text/plain', '${pending}', '${pending}', '', 0, 0)`).then((result) => {
      ids.pending = result.insertId;
    });
    // A topic of the administrator, which he deletes for good.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${admin.id}, 'x', '', 0, UNIX_TIMESTAMP(), ${admin.id}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', ${admin.id}, '', '${subject}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'To be deleted ${stamp}.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      // A file of the post: the rows of the files are cleaned up only when the deleted topics had files.
      cy.task('queryDB', `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${message.insertId}, ${admin.id}, 0, '', 1, 'media/kunena/attachments/cypress-${stamp}', 'text/plain', 'posted-${stamp}.txt', 'posted-${stamp}.txt', '', 0, 1)`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE filename IN ('${pending}', 'posted-${stamp}.txt')`);
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.kunena_fixUserCounters();
  });

  it('keeps an upload which waits for its message', () => {
    cy.doFrontendLogin(admin.name, admin.password, false);
    cy.request(`${base}&view=topic&catid=3&id=${ids.topic}`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topics&task=topics.permdel`,
        form: true,
        body: { [`topics[${ids.topic}]`]: 1, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_topics WHERE id = ${ids.topic}`).then((rows) => {
      expect(Number(rows[0].n), 'the topic is deleted').to.eq(0);
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_attachments WHERE id = ${ids.pending}`).then((rows) => {
      expect(Number(rows[0].n), 'the pending upload').to.eq(1);
    });
  });
});
