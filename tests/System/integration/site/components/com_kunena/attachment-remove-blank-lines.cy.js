// A file removed in the edit form of a message ("Remove File" or "Remove All") takes only its [attachment=ID] tag out of the text:
// the blank lines between the paragraphs stay, in the editor and in the saved message. The tag is inside a paragraph, alone on its
// line between blank lines, or at the start or the end of the message. Both editors of the template Aurelia (CKEditor, SCEditor)
// are tested. The files are not on disk (the attachment rows only). The forum template and the editor of the site are put back.
describe('Kunena attachments: the blank lines of a message after removing a file in the editor', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress blank lines ${stamp}`;
  const folder = `media/kunena/attachments/cypress-blank-${stamp}`;
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/aurelia/config/params.ini`;
  let ids = {};
  let original;
  let oldTemplate;
  let templateSaved = false;
  let text;

  // How the test reads the text of each editor, and when it is ready.
  const editors = {
    ckeditor: {
      ready: (win) => win.CKEDITOR?.instances?.message?.status === 'ready',
      read: (win) => win.CKEDITOR.instances.message.getData(),
    },
    sceditor: {
      ready: (win) => Boolean(win.sceditor?.instance(win.document.getElementById('message'))),
      read: (win) => win.sceditor.instance(win.document.getElementById('message')).val(),
    },
  };

  // CRLF of the posted form becomes LF, the spaces at the end of a line go, and so do the line breaks before the first and after the
  // last text of the message: every save trims the ends of a message (KunenaMessage::updateAttachments()), and CKEditor drops empty
  // paragraphs at the end. The line breaks between the paragraphs are compared as they are.
  const normalise = (value) => value.replace(/\r\n/g, '\n').replace(/[ \t]+$/gm, '').replace(/^\n+|\n+$/g, '');
  const tag = (name) => `[attachment=${ids[name]}]${name}-${stamp}.txt[/attachment]`;
  const attach = (name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${ids.message}, 797, 0, '', 10, '${folder}', 'text/plain', '${name}-${stamp}.txt', '${name}-${stamp}.txt', '', 0, 0)`,
  ).then((row) => {
    ids[name] = row.insertId;
    ids.attachments.push(row.insertId);
  });

  // The texts, with blank lines between the paragraphs (once a double one). A tag inside a paragraph follows a colon: its removal
  // leaves no double space, which an HTML editor makes one (that space is not what this test is about).
  const texts = {
    inside: () => `First paragraph.\n\nSecond paragraph, a file:${tag('first')} and more.\n\n\nThird paragraph.\n\nLast paragraph.`,
    alone: () => `First paragraph.\n\n${tag('first')}\n\nSecond paragraph.\n\n\nLast paragraph.`,
    start: () => `${tag('first')}\n\nFirst paragraph.\n\n\nSecond paragraph.\n\nLast paragraph.`,
    end: () => `First paragraph.\n\n\nSecond paragraph.\n\nLast paragraph.\n\n${tag('first')}`,
    all: () => `${tag('first')}\n\nFirst paragraph, a file:${tag('second')} and more.\n\n\n${tag('third')}\n\nLast paragraph.\n\n${tag('fourth')}`,
  };

  // Sets the editor of the template Aurelia (the original content of the file is kept by the first call).
  const setEditor = (editor) => {
    cy.readFile(params).then((content) => {
      if (original === undefined) {
        original = content;
      }

      const line = `editorType="${editor}"`;
      const changed = /^editorType=.*$/m.test(original) ? original.replace(/^editorType=.*$/m, line) : `${original.trimEnd()}\n${line}\n`;

      cy.writeFile(params, changed);
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  };

  before(() => {
    // The form of the template Aurelia: the site may run another template.
    cy.kunena_getOption('template').then((value) => {
      oldTemplate = value;
      templateSaved = true;
    });
    cy.kunena_setOption('template', 'aurelia');
  });

  after(() => {
    cy.then(() => {
      if (original !== undefined) {
        cy.writeFile(params, original);
      }
    });
    cy.then(() => {
      if (templateSaved) {
        cy.kunena_setOption('template', oldTemplate === undefined ? null : oldTemplate);
      }
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  // The topic with its message by the administrator, the attachment rows of the message, then the text which names them.
  const setUp = (names, build) => {
    ids = { attachments: [] };
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
      text = build();
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${ids.message}, '${text}')`);
    });
    cy.intercept({ method: 'POST', url: /removeattachments/ }).as('remove');
  };

  afterEach(() => {
    cy.then(() => {
      if (ids.attachments?.length) {
        cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE id IN (${ids.attachments.join(', ')})`);
      }

      if (ids.message) {
        cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
      }

      if (ids.topic) {
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
        cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE topic_id = ${ids.topic}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
      }
    });
    cy.kunena_fixUserCounters();
    cy.kunena_fixCategoryCounters();
  });

  // Opens the edit form of the message and waits for the editor and the "Remove File" buttons of the files of the message; the
  // editor must show the stored text.
  const openEditForm = (editor) => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.visit(`${base}&view=topic&layout=edit&catid=3&id=${ids.topic}&mesid=${ids.message}`);
    cy.window().should((win) => {
      expect(editors[editor].ready(win), editor).to.equal(true);
    });
    cy.get('#files button').filter(':contains("Remove File")').should('have.length', ids.attachments.length);
    cy.window().then((win) => {
      expect(normalise(editors[editor].read(win)), 'the editor text on load').to.equal(normalise(text));
    });
  };

  // Removes the files (the button which is clicked, the number of requests to wait for), then compares the editor with the text
  // it had before, without the tags, and the saved message with the stored text, without the tags: the rows are gone.
  const removeAndSave = (editor, names, click) => {
    cy.window().then((win) => {
      const expected = names.reduce((value, name) => value.replace(tag(name), ''), editors[editor].read(win));

      click();
      cy.wait(names.map(() => '@remove'));
      cy.window().should((current) => {
        expect(normalise(editors[editor].read(current)), 'the editor after removal').to.equal(normalise(expected));
      });
    });
    cy.get('#form_submit_button').should('not.be.disabled').click();
    cy.location('href').should('not.contain', 'layout=edit');
    cy.then(() => cy.task('queryDB', `SELECT message FROM #__kunena_messages_text WHERE mesid = ${ids.message}`)).then((rows) => {
      expect(rows.length, 'the saved message row').to.equal(1);

      const expected = names.reduce((value, name) => value.replace(tag(name), ''), text);

      expect(normalise(rows[0].message), 'the saved message').to.equal(normalise(expected));
    });
    cy.then(() => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_attachments WHERE id IN (${ids.attachments.join(', ')})`))
      .its('0.n').then((n) => expect(Number(n), 'the removed attachment rows').to.equal(0));
  };

  Object.keys(editors).forEach((editor) => {
    describe(editor, () => {
      before(() => setEditor(editor));

      ['inside', 'alone', 'start', 'end'].forEach((place) => {
        describe(`one file, its tag ${place}`, () => {
          beforeEach(() => setUp(['first'], texts[place]));

          it('keeps the blank lines when the file is removed with "Remove File"', () => {
            openEditForm(editor);
            removeAndSave(editor, ['first'], () => cy.get('#files button').filter(':contains("Remove File")').click());
          });
        });
      });

      describe('four files', () => {
        beforeEach(() => setUp(['first', 'second', 'third', 'fourth'], texts.all));

        it('keeps the blank lines when the files are removed with "Remove All"', () => {
          openEditForm(editor);
          removeAndSave(editor, ['first', 'second', 'third', 'fourth'], () => cy.get('#remove-all').should('be.visible').click());
        });
      });
    });
  });
});
