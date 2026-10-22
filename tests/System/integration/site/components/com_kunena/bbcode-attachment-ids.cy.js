// [attachment=ID] in a message shows an attachment of that message only. The number of an attachment of another message (one in
// a category which the reader may not see, for example) shows nothing of it: not its name, not its link.
describe('Kunena BBCode: the attachment numbers of a message', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const folder = `media/kunena/attachments/cypress-${stamp}`;
  const root = 'E:/www/apache/htdocs/tests';
  const ids = {};

  const addMessage = (parent, thread, category, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${thread}, ${category}, 'ci-admin', 797, '', 'Re: attachment ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text.replaceAll("'", "\\'")}')`).then(() => message.insertId));

  const addAttachment = (message, name) => {
    cy.writeFile(`${root}/${folder}/${name}`, `the file ${name}`);

    return cy.task(
      'queryDB',
      `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${message}, 797, 0, '${name}', 20, '${folder}', 'text/plain', '${name}', '${name}', '', 0, 0)`,
    ).then((attachment) => attachment.insertId);
  };

  beforeEach(() => {
    // A message of the administrator with an attachment, in a topic of the test site.
    addMessage(10, 9, 3, 'A message with its own file.').then((message) => {
      ids.own = message;

      return addAttachment(message, `ownfile${stamp}.txt`);
    }).then((attachment) => {
      ids.ownAttachment = attachment;

      // A message in a category for the access level Special, with an attachment.
      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress attachment ids ${stamp}', 'cypress-attids-${stamp}', '', '', '', '', '', 'joomla.level', 3, 3, 1, 8, 1, 90, 1, '{}', 'default')`,
      );
    }).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-attids-${stamp}', 'catid', ${ids.category}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, 'Cypress attachment ids ${stamp}', 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return addMessage(0, topic.insertId, ids.category, 'A hidden message with a file.');
    }).then((message) => {
      ids.hidden = message;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message}, last_post_id = ${message} WHERE id = ${ids.topic}`);

      return addAttachment(message, `secretfile${stamp}.txt`);
    }).then((attachment) => {
      ids.hiddenAttachment = attachment;

      // A public message which names the file of the hidden message, and its own file.
      return addMessage(10, 9, 3, `Look: [attachment=${attachment}]a file[/attachment] and [attachment=${ids.ownAttachment}]mine[/attachment] and [attachment=99999999]none[/attachment]`);
    }).then((message) => {
      ids.borrowing = message;
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid IN (${ids.own}, ${ids.hidden}, ${ids.borrowing})`);
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE folder = '${folder}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id IN (${ids.own}, ${ids.hidden}, ${ids.borrowing})`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-attids-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.task('deleteRelativePath', folder);
  });

  it('shows nothing of the file of another message, and the own file as always', () => {
    cy.request(`${base}&view=topic&catid=3&id=9`).its('body').then((body) => {
      expect(String(body), 'the file of the hidden message').to.not.include(`secretfile${stamp}`);
      expect(String(body), 'the own file of the message').to.include(`ownfile${stamp}`);
    });

    // Also for the administrator, who may read the hidden message: its file belongs to that message, not to this one.
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.request(`${base}&view=topic&catid=3&id=9&mesid=${ids.borrowing}`).its('body').then((body) => {
      const message = String(body).slice(String(body).indexOf('Look:'));

      expect(message, 'the text of the message which borrows').to.not.include(`secretfile${stamp}`);
    });
  });
});
