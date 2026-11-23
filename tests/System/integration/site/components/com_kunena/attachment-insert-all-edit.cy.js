// "Insert all" in the form to edit a message which has a file not yet in its text: the tag of the file goes into the text once. The
// files of the edited message are in the list of the form (kattach-list) and were added a second time from the files of the message,
// so each file came twice into the text. Nothing is posted; the rows and the file of the test are removed after it.
describe('Kunena edit: Insert all puts each file of the message once in the text', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const file = `cypress-insert-${stamp}.txt`;
  const folder = `media/kunena/attachments/cypress-${stamp}`;
  const ids = {};
  const old = {};

  before(() => {
    cy.kunena_setOption('userEdit', '1').then((value) => {
      old.userEdit = value ?? null;
    });
  });

  after(() => {
    cy.kunena_setOption('userEdit', old.userEdit);
  });

  beforeEach(() => {
    // A topic of the user whose message has one file, which is not in its text.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress insert ${stamp}', 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-user', ${user.id}, '', 'Cypress insert ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${user.id}, ${ids.topic}, 3, 1, ${message.insertId}, 1, 0, 0, '')`);
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'The text without its file.')`);
      cy.task('writeRelativeFile', { path: `${folder}/${file}`, content: `the file ${file}` });

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${message.insertId}, ${user.id}, 0, '', 10, '${folder}', 'text/plain', '${file}', '${file}', '', 0, 0)`,
      );
    }).then((attachment) => {
      ids.attachment = attachment.insertId;
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

  it('inserts the file of the edited message once', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&layout=edit&catid=3&id=${ids.topic}&mesid=${ids.message}`);
    cy.editor_ready();
    // The file of the message is in the list of the form (preload of upload.main.js).
    cy.get(`#kattach-${ids.attachment}`).should('exist');
    cy.get('#insert-all').should('be.visible').click();
    cy.editor_data().then((text) => {
      const tags = text.match(new RegExp(`\\[attachment=${ids.attachment}\\]`, 'g')) || [];

      expect(tags, 'the tags of the file in the text').to.have.length(1);
    });
  });
});
