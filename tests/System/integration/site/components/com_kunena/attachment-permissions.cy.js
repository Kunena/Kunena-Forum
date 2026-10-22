// A protected attachment (the option "attachment protection") is sent by the forum, after a check of the right to read the message it
// belongs to. The attachments of a message in a category for the access level Special, of a message which waits for approval and of
// a deleted message must not be sent to a guest or a user. The administrator gets them all (so the data of the test is right).
describe('Kunena permissions: attachments', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const folder = `media/kunena/attachments/cypress-${stamp}`;
  const root = 'E:/www/apache/htdocs/tests';
  const ids = {};
  const old = {};

  const addMessage = (category, hold) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category}, 'Cypress attachment ${stamp}', 0, 0, 0, ${hold}, 0, 1, 0, 1, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${category}, 'ci-admin', 797, '', 'Cypress attachment ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, ${hold}, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
  )).then(() => message.insertId)));

  const addAttachment = (name, category, hold) => addMessage(category, hold).then((message) => {
    cy.writeFile(`${root}/${folder}/${name}`, `attachment ${name}`);

    return cy.task(
      'queryDB',
      `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${message}, 797, 1, '${name}', 20, '${folder}', 'text/plain', '${name}', '${name}.txt', '', 0, 0)`,
    ).then((attachment) => attachment.insertId);
  });

  const download = (id) => cy.request({ url: `${base}&view=attachment&id=${id}&format=raw`, failOnStatusCode: false }).its('status');

  before(() => {
    cy.kunena_setOption('attachmentProtection', '1').then((value) => {
      old.protection = value;
    });
    cy.kunena_setOption('showFileForGuest', '2').then((value) => {
      old.guest = value;
    });
  });

  after(() => {
    cy.kunena_setOption('attachmentProtection', old.protection ?? '0');
    cy.kunena_setOption('showFileForGuest', old.guest ?? '2');
  });

  // The queryDB task removes what is inserted (with an id) after each test: the data is made again for every test.
  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress attachment ${stamp}', 'cypress-attachment-${stamp}', '', '', '', '', '', 'joomla.level', 3, 3, 1, 8, 1, 92, 1, '{}', 'default')`,
    ).then((category) => {
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-attachment-${stamp}', 'catid', ${category.insertId}, 0)`);

      return addAttachment(`restricted${stamp}`, category.insertId, 0);
    }).then((restricted) => {
      ids.restricted = restricted;

      return addAttachment(`waiting${stamp}`, 3, 1);
    }).then((waiting) => {
      ids.waiting = waiting;

      return addAttachment(`deleted${stamp}`, 3, 2);
    }).then((deleted) => {
      ids.deleted = deleted;

      return addAttachment(`public${stamp}`, 3, 0);
    }).then((visible) => {
      ids.visible = visible;
    });
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = 'Cypress attachment ${stamp}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE hash LIKE '%${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-attachment-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE name = 'Cypress attachment ${stamp}'`);
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
    cy.task('deleteRelativePath', folder);
  });

  it('sends nothing of it to a guest', () => {
    ['restricted', 'waiting', 'deleted'].forEach((name) => download(ids[name]).should('be.gte', 400));
  });

  it('sends nothing of it to a user, but the attachment of a public message', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    ['restricted', 'waiting', 'deleted'].forEach((name) => download(ids[name]).should('be.gte', 400));
    download(ids.visible).should('eq', 200);
  });

  it('sends all of it to the administrator (the data of the test is right)', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    ['restricted', 'waiting', 'deleted', 'visible'].forEach((name) => download(ids[name]).should('eq', 200));
  });
});
