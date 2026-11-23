// The edit form sends the files which the message has already (attachments[id] and attachment[id]): they stay in the message. The
// number of files of the topic (the paperclip of the lists) went up by one for each of them on every edit, so a topic with one file
// counted two after one edit, three after two, and kept the paperclip when its file was removed.
describe('Kunena edit: the number of files of the topic', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const file = `cypress-count-${stamp}.txt`;
  const root = 'E:/www/apache/htdocs/tests';
  const folder = `media/kunena/attachments/cypress-${stamp}`;
  const ids = {};
  const old = {};

  const edit = () => cy.request(`${base}&view=topic&catid=3&id=${ids.topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=edit`,
      form: true,
      body: {
        catid: 3,
        id: ids.topic,
        mesid: ids.message,
        subject: `Cypress files ${stamp}`,
        message: `The text, changed. [attachment=${ids.attachment}]${file}[/attachment]`,
        [`attachments[${ids.attachment}]`]: '1',
        [`attachment[${ids.attachment}]`]: file,
        [token]: '1',
      },
      failOnStatusCode: false,
      followRedirect: false,
    });
  });

  const count = () => cy.task('queryDB', `SELECT attachments FROM #__kunena_topics WHERE id = ${ids.topic}`).then((rows) => rows[0].attachments);

  before(() => {
    cy.kunena_setOption('userEdit', '1').then((value) => {
      old.userEdit = value ?? null;
    });
  });

  after(() => {
    cy.kunena_setOption('userEdit', old.userEdit);
  });

  beforeEach(() => {
    // A topic of the user whose message has one file.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress files ${stamp}', 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-user', ${user.id}, '', 'Cypress files ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${user.id}, ${ids.topic}, 3, 1, ${message.insertId}, 1, 0, 0, '')`);

      cy.writeFile(`${root}/${folder}/${file}`, `the file ${file}`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${message.insertId}, ${user.id}, 0, '', 10, '${folder}', 'text/plain', '${file}', '${file}', '', 0, 0)`,
      );
    }).then((attachment) => {
      ids.attachment = attachment.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${ids.message}, 'The text. [attachment=${attachment.insertId}]${file}[/attachment]')`);
    });
  });

  afterEach(() => {
    cy.task('deleteRelativePath', folder);
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE mesid = ${ids.message} OR filename = '${file}'`);
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('keeps one file after two edits which keep the file', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    edit();
    edit();
    cy.task('queryDB', `SELECT message FROM #__kunena_messages_text WHERE mesid = ${ids.message}`).then((rows) => {
      expect(rows[0].message, 'the edit is saved').to.contain('The text, changed.');
    });
    count().then((value) => expect(value, 'the files of the topic').to.eq(1));
  });
});
