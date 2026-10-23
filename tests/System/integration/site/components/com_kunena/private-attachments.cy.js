// A file of the private part of a message ([PRIVATE], protection 32) is for the users of that private part and the moderators.
// With "Protect Attachments" the files go through the forum (view=attachment): a user who may read the message, but is not one
// of the users of its private part, gets no private file. A normal file of the same message stays readable for him.
describe('Kunena attachments: the files of the private part of a message', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const folder = `media/kunena/attachments/cypress-private-${stamp}`;
  const root = 'E:/www/apache/htdocs/tests';
  const ids = {};
  let protection;

  const addAttachment = (name, level) => {
    cy.writeFile(`${root}/${folder}/${name}`, `the content of ${name}`);

    return cy.task(
      'queryDB',
      `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${ids.message}, ${admin}, ${level}, '${name}', 30, '${folder}', 'text/plain', '${name}', '${name}', '', 0, 0)`,
    ).then((attachment) => attachment.insertId);
  };

  const file = (id) => cy.request({ url: `${base}&view=attachment&id=${id}&format=raw`, failOnStatusCode: false })
    .then((response) => ({ status: response.status, body: String(response.body) }));

  before(() => {
    cy.kunena_setOption('attachmentProtection', '1').then((value) => {
      protection = value;
    });
  });

  after(() => {
    cy.kunena_setOption('attachmentProtection', protection ?? '0');
  });

  beforeEach(() => {
    // A reply of the administrator in a public topic, with a private part for himself only (and the moderators).
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (10, 9, 3, 'ci-admin', ${admin}, '', 'Re: private files ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A reply with files.')`);

      return cy.task('queryDB', `INSERT INTO #__kunena_private (parent_id, author_id, created_at, attachments, subject, body, params) VALUES (0, ${admin}, NOW(), 1, '', 'A private part.', '')`);
    }).then((privatePart) => {
      ids.private = privatePart.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_private_user_map (private_id, user_id) VALUES (${ids.private}, ${admin})`);
      cy.task('queryDB', `INSERT INTO #__kunena_private_post_map (private_id, message_id) VALUES (${ids.private}, ${ids.message})`);

      return addAttachment(`privatefile${stamp}.txt`, 32);
    }).then((attachment) => {
      ids.privateFile = attachment;
      cy.task('queryDB', `INSERT INTO #__kunena_private_attachment_map (private_id, attachment_id) VALUES (${ids.private}, ${attachment})`);

      return addAttachment(`normalfile${stamp}.txt`, 1);
    }).then((attachment) => {
      ids.normalFile = attachment;
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_private_attachment_map WHERE private_id = ${ids.private}`);
    cy.task('queryDB', `DELETE FROM #__kunena_private_post_map WHERE private_id = ${ids.private}`);
    cy.task('queryDB', `DELETE FROM #__kunena_private_user_map WHERE private_id = ${ids.private}`);
    cy.task('queryDB', `DELETE FROM #__kunena_private WHERE id = ${ids.private}`);
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE folder = '${folder}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('deleteRelativePath', folder);
  });

  it('gives a user the normal file of the message, and not the private one', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    file(ids.normalFile).then(({ status, body }) => {
      expect(status, 'the status of the normal file').to.eq(200);
      expect(body, 'the normal file').to.include(`the content of normalfile${stamp}`);
    });
    file(ids.privateFile).then(({ body }) => expect(body, 'the private file').to.not.include(`the content of privatefile${stamp}`));
  });

  it('does not name the private file in the list of the files of the message (for the editor)', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=home`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request(`${base}&view=topic&task=loadattachments&format=json&mes_id=${ids.message}&${token}=1`);
    }).then((response) => {
      const text = typeof response.body === 'string' ? response.body : JSON.stringify(response.body);

      expect(text, 'the normal file').to.include(`normalfile${stamp}`);
      expect(text, 'the private file').to.not.include(`privatefile${stamp}`);
    });
  });

  it('gives a visitor no private file', () => {
    file(ids.privateFile).then(({ body }) => expect(body, 'the private file for a visitor').to.not.include(`the content of privatefile${stamp}`));
  });

  it('gives the private file to a user who is one of the users of the private part', () => {
    cy.task('queryDB', `INSERT INTO #__kunena_private_user_map (private_id, user_id) VALUES (${ids.private}, ${user.id})`);
    cy.doFrontendLogin(user.name, user.password, false);
    file(ids.privateFile).then(({ body }) => expect(body, 'the private file for a user of the private part').to.include(`the content of privatefile${stamp}`));
  });

  it('gives the private file to a user of the private part (here the administrator)', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    file(ids.privateFile).then(({ status, body }) => {
      expect(status, 'the status').to.eq(200);
      expect(body, 'the private file').to.include(`the content of privatefile${stamp}`);
    });
  });
});
