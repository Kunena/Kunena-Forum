// The list of the files of a user (view=user&layout=attachments&userid=X) shows the files of the messages which the reader may
// see. A file of a message in a category which he may not read stays out: not its name, not the subject of its topic.
describe('Kunena attachments of a user: the files of hidden messages', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const stamp = Date.now();
  const folder = `media/kunena/attachments/cypress-${stamp}`;
  const root = 'E:/www/apache/htdocs/tests';
  const ids = {};

  const addMessage = (parent, thread, category, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${thread}, ${category}, 'ci-admin', ${admin}, '', 'Re: files ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`).then(() => message.insertId));

  const addAttachment = (message, name) => {
    cy.writeFile(`${root}/${folder}/${name}`, `the file ${name}`);

    return cy.task(
      'queryDB',
      `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${message}, ${admin}, 0, '${name}', 20, '${folder}', 'text/plain', '${name}', '${name}', '', 0, 0)`,
    ).then((attachment) => attachment.insertId);
  };

  const list = (userId) => cy.request({ url: `${base}&view=user&layout=attachments&userid=${userId}`, failOnStatusCode: false }).then((response) => {
    expect(response.status, 'the status').to.eq(200);

    return String(response.body);
  });

  beforeEach(() => {
    // A public message of the administrator with a file, in a topic of the test site.
    addMessage(10, 9, 3, 'A message with a public file.').then((message) => {
      ids.public = message;

      return addAttachment(message, `publicfile${stamp}.txt`);
    }).then(() => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress files hidden ${stamp}', 'cypress-files-${stamp}', '', '', '', '', '', 'joomla.level', 3, 3, 1, 8, 1, 90, 1, '{}', 'default')`,
    )).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-files-${stamp}', 'catid', ${ids.category}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, 'Cypress secret topic ${stamp}', 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return addMessage(0, topic.insertId, ids.category, 'A hidden message with a file.');
    }).then((message) => {
      ids.hidden = message;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message}, last_post_id = ${message} WHERE id = ${ids.topic}`);

      return addAttachment(message, `secretfile${stamp}.txt`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid IN (${ids.public}, ${ids.hidden})`);
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE folder = '${folder}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id IN (${ids.public}, ${ids.hidden})`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-files-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.task('deleteRelativePath', folder);
  });

  it('shows a user the public file, and nothing of the hidden one', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    list(admin).then((body) => {
      expect(body, 'the public file').to.include(`publicfile${stamp}`);
      expect(body, 'the hidden file').to.not.include(`secretfile${stamp}`);
      expect(body, 'the topic of the hidden file').to.not.include(`Cypress secret topic ${stamp}`);
    });
  });

  it('shows a moderator both files', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    list(admin).then((body) => {
      expect(body, 'the public file').to.include(`publicfile${stamp}`);
      expect(body, 'the hidden file').to.include(`secretfile${stamp}`);
    });
  });
});
