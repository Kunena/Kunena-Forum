// A file removed in the edit form of a message ("Remove File" or "Remove All", CKEditor) takes only its [attachment=ID] tag out of
// the text: the blank lines between the paragraphs stay, in the editor and in the saved message. The files are not on disk (the
// attachment rows only). The site of the tests runs SCEditor (and may run the template Agora): the test switches to Aurelia with
// CKEditor and puts both back.
describe('Kunena attachments: the blank lines of a message after removing a file in the editor', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress blank lines ${stamp}`;
  const folder = `media/kunena/attachments/cypress-blank-${stamp}`;
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/aurelia/config/params.ini`;
  const ids = {};
  let original;
  let oldTemplate;
  let text;

  // CRLF of the posted form becomes LF, the spaces at the end of a line go; the line breaks stay as they are.
  const normalise = (value) => value.replace(/\r\n/g, '\n').replace(/[ \t]+$/gm, '');
  const tag = (name) => `[attachment=${ids[name]}]${name}-${stamp}.txt[/attachment]`;
  const attach = (name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${ids.message}, 797, 0, '', 10, '${folder}', 'text/plain', '${name}-${stamp}.txt', '${name}-${stamp}.txt', '', 0, 0)`,
  ).then((row) => {
    ids[name] = row.insertId;
    ids.attachments.push(row.insertId);
  });
  const editorText = () => cy.window().then((win) => win.CKEDITOR.instances.message.getData());

  before(() => {
    // The form of the template Aurelia: the site may run another template.
    cy.kunena_setOption('template', 'aurelia').then((value) => {
      oldTemplate = value;
    });
    cy.readFile(params).then((content) => {
      original = content;
      const line = 'editorType="ckeditor"';
      const changed = /^editorType=.*$/m.test(content) ? content.replace(/^editorType=.*$/m, line) : `${content.trimEnd()}\n${line}\n`;

      cy.writeFile(params, changed);
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  after(() => {
    cy.then(() => {
      if (original !== undefined) {
        cy.writeFile(params, original);
      }
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
    cy.then(() => cy.kunena_setOption('template', oldTemplate === undefined ? null : oldTemplate));
  });

  // The topic with its message by the administrator, the attachment rows of the message, then the text which names them: three
  // paragraphs and more, blank lines between them (once a double one), the tags inside the paragraphs.
  const setUp = (names) => {
    ids.attachments = [];
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 1, 0, ${names.length}, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      names.forEach((name) => attach(name));
    }).then(() => {
      // A tag right after a colon: its removal leaves no double space, which CKEditor (HTML) makes one.
      const third = names.includes('second') ? `, a file:${tag('second')} and more` : '';

      text = `First paragraph.\n\nSecond paragraph, a file:${tag('first')} and more.\n\n\nThird paragraph${third}.\n\nLast paragraph.`;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${ids.message}, '${text}')`);
    });
    cy.intercept({ method: 'POST', url: /removeattachments/ }).as('remove');
  };

  afterEach(() => {
    cy.then(() => {
      if (ids.attachments.length) {
        cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE id IN (${ids.attachments.join(', ')})`);
      }
    });
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.kunena_fixUserCounters();
    cy.kunena_fixCategoryCounters();
  });

  // Opens the edit form of the message and waits for CKEditor and the "Remove File" buttons of the files of the message; the editor
  // must show the stored text.
  const openEditForm = () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.visit(`${base}&view=topic&layout=edit&catid=3&id=${ids.topic}&mesid=${ids.message}`);
    cy.window().should((win) => {
      expect(win.CKEDITOR?.instances?.message?.status, 'CKEditor').to.equal('ready');
    });
    cy.get('#files button').filter(':contains("Remove File")').should('have.length', ids.attachments.length);
    editorText().then((value) => {
      expect(normalise(value), 'the editor text on load').to.equal(normalise(text));
    });
  };

  // Saves the form and compares the stored message with the expected text: the removed attachment rows are gone.
  const saveAndCheck = (expected) => {
    cy.get('#form_submit_button').should('not.be.disabled').click();
    cy.location('href').should('not.contain', 'layout=edit');
    cy.then(() => cy.task('queryDB', `SELECT message FROM #__kunena_messages_text WHERE mesid = ${ids.message}`)).then((rows) => {
      expect(normalise(rows[0].message), 'the saved message').to.equal(normalise(expected));
    });
    cy.then(() => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_attachments WHERE id IN (${ids.attachments.join(', ')})`))
      .its('0.n').then((n) => expect(Number(n), 'the removed attachment rows').to.equal(0));
  };

  describe('one file', () => {
    beforeEach(() => setUp(['first']));

    it('keeps the blank lines when the file is removed with "Remove File"', () => {
      openEditForm();
      editorText().then((before) => {
        const expected = before.replace(tag('first'), '');

        cy.get('#files button').filter(':contains("Remove File")').click();
        cy.wait('@remove');
        cy.window().should((win) => {
          expect(normalise(win.CKEDITOR.instances.message.getData()), 'the editor after removal').to.equal(normalise(expected));
        });
      });
      cy.then(() => saveAndCheck(text.replace(tag('first'), '')));
    });
  });

  describe('two files', () => {
    beforeEach(() => setUp(['first', 'second']));

    it('keeps the blank lines when the files are removed with "Remove All"', () => {
      openEditForm();
      editorText().then((before) => {
        const expected = before.replace(tag('first'), '').replace(tag('second'), '');

        cy.get('#remove-all').should('be.visible').click();
        cy.wait(['@remove', '@remove']);
        cy.window().should((win) => {
          expect(normalise(win.CKEDITOR.instances.message.getData()), 'the editor after removal').to.equal(normalise(expected));
        });
      });
      cy.then(() => saveAndCheck(text.replace(tag('first'), '').replace(tag('second'), '')));
    });
  });
});
