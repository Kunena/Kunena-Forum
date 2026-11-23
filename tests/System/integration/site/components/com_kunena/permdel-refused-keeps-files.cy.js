// The bulk delete of topics for good (task topics.permdel) collected the files of all the topics of the request before it checked
// the right to delete each topic. The files of a topic whose delete was refused were deleted too (their rows stayed): any member
// with the token of a page deleted the files of the posts of others, which then showed broken images and downloads.
describe('Kunena topics: a refused delete for good keeps the files', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x' };
  const root = 'E:/www/apache/htdocs/tests';
  const file = `cypress-permdel-${stamp}.txt`;
  const folder = `media/kunena/attachments/cypress-permdel-${stamp}`;
  const subject = `Cypress permdel files ${stamp}`;
  const ids = {};

  beforeEach(() => {
    cy.writeFile(`${root}/${folder}/${file}`, 'The file of a post.');
    // A topic of the administrator with a file in its post.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', '${subject}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A post with a file ${stamp}.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${message.insertId}, 797, 0, '', 19, '${folder}', 'text/plain', '${file}', '${file}', '', 0, 0)`);
    });
  });

  afterEach(() => {
    cy.task('deleteRelativePath', folder);
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE filename = '${file}'`);
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('keeps the file of the topic of another user when a member may not delete it', () => {
    cy.doFrontendLogin(user.name, user.password, false);
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
      expect(Number(rows[0].n), 'the topic is not deleted').to.eq(1);
    });
    cy.readFile(`${root}/${folder}/${file}`).should('eq', 'The file of a post.');
  });
});
