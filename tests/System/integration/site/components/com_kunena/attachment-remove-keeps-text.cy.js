// Removing an uploaded file in the posting form ("Remove File" or "Remove All") keeps the text of the message (issue #7880): when
// the file is not in the text, the editor is not touched; when it was inserted, only its [attachment=ID] tag goes and every line,
// blank lines included, stays. Tested with the CKEditor and with the SCEditor in its WYSIWYG view and its source view. The spec
// switches the editor of the template Agora for its run and puts the file of the options back. The form is not sent.
describe('Kunena attachments: the text of the message after removing an upload', () => {
  const create = '/index.php?option=com_kunena&view=topic&layout=create&catid=3';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const folder = `media/kunena/attachments/${user.id}`;
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/agora/config/params.ini`;
  const text = 'First line of the message.\nSecond line.\n\nThird line, after a blank line.\nLast line.';
  const old = {};

  // The lines of an editor value: the paragraphs and <br> of an HTML value become line breaks, the spaces around each line (also
  // a non-breaking one) are not compared.
  const lines = (value) => value
    .replace(/\r\n/g, '\n')
    .replace(/<br\s*\/?>/gi, '\n')
    .replace(/<\/p>\s*<p[^>]*>/gi, '\n\n')
    .replace(/<\/?p[^>]*>/gi, '')
    .replace(/&nbsp;/g, ' ')
    .split('\n')
    .map((line) => line.trim());

  // The lines of the text before are all still there, in their order (a line which the insert added may come between them).
  const keepsLines = (after, expected) => {
    let next = 0;

    lines(after).forEach((line) => {
      if (next < expected.length && line === expected[next]) {
        next += 1;
      }
    });

    expect(next, `the lines of the text in ${JSON.stringify(after)}`).to.equal(expected.length);
  };

  const tagOf = (value) => /\[attachment=[0-9]+\][^[\]]+\[\/attachment\]/.exec(value);

  // The editor of the template Agora: the ini file gets the value, the cache of the template is removed.
  const useEditor = (editor) => {
    const line = `editorType="${editor}"`;
    const content = /^editorType=.*$/m.test(old.params) ? old.params.replace(/^editorType=.*$/m, line) : `${old.params.trimEnd()}\n${line}\n`;

    return cy.writeFile(params, content).then(() => cy.task('deleteRelativePath', 'cache/com_kunena_template'));
  };

  // How the test waits for each editor, reads its value and sets the text in its view.
  const views = {
    CKEditor: {
      editor: 'ckeditor',
      ready: (win) => win.CKEDITOR?.instances?.message?.status === 'ready',
      read: (win) => win.CKEDITOR.instances.message.getData(),
      set: (win) => new Cypress.Promise((resolve) => {
        win.CKEDITOR.instances.message.setData(text, { callback: resolve });
      }),
    },
    'SCEditor WYSIWYG': {
      editor: 'sceditor',
      ready: (win) => Boolean(win.sceditor?.instance(win.document.getElementById('message'))),
      read: (win) => win.sceditor.instance(win.document.getElementById('message')).val(),
      set: (win) => {
        const instance = win.sceditor.instance(win.document.getElementById('message'));

        instance.sourceMode(false);
        instance.val(text);
      },
    },
    'SCEditor source': {
      editor: 'sceditor',
      ready: (win) => Boolean(win.sceditor?.instance(win.document.getElementById('message'))),
      read: (win) => win.sceditor.instance(win.document.getElementById('message')).val(),
      set: (win) => {
        const instance = win.sceditor.instance(win.document.getElementById('message'));

        instance.sourceMode(true);
        instance.val(text);
      },
    },
  };

  before(() => {
    cy.kunena_setOption('template', 'agora').then((value) => {
      old.template = value;
    });
    cy.readFile(params).then((content) => {
      old.params = content;
    });
  });

  after(() => {
    cy.then(() => {
      if (old.params !== undefined) {
        cy.writeFile(params, old.params);
      }
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
    cy.then(() => cy.kunena_setOption('template', old.template ?? null));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE userid = ${user.id} AND mesid = 0`);
    cy.task('deleteRelativePath', folder);
  });

  Object.entries(views).forEach(([name, view]) => {
    describe(name, () => {
      let original;

      // Opens the create-topic form, sets the text and uploads a file; keeps the editor value before the upload.
      beforeEach(() => {
        useEditor(view.editor);
        cy.doFrontendLogin(user.name, user.password, false);
        cy.visit(create);
        cy.window().should((win) => {
          expect(view.ready(win), name).to.equal(true);
        });
        cy.window().then((win) => view.set(win));
        cy.window().then((win) => {
          original = view.read(win);
          expect(lines(original), 'the lines of the text').to.include('').and.include('Last line.');
        });
        cy.intercept({ method: 'POST', url: /task=removeattachments/ }).as('remove');
        cy.get('#fileupload').selectFile({
          contents: Cypress.Buffer.from('A small file.'),
          fileName: `cypress-keeps-text-${stamp}.txt`,
          mimeType: 'text/plain',
        }, { force: true });
        cy.contains('#files button', 'Remove File').should('be.visible');
      });

      // Removes the file with the button, after the server confirmed the removal.
      const remove = (button) => {
        if (button === 'all') {
          cy.get('#remove-all').should('be.visible').click();
        } else {
          cy.contains('#files button', 'Remove File').click();
        }

        cy.wait('@remove');
        cy.get('#files button').should('not.exist');
      };

      // Inserts the file and gives the editor value with its tag.
      const insert = () => {
        cy.contains('#files button', /^\s*Insert\s*$/).click();

        return cy.window().then((win) => {
          const inserted = view.read(win);

          expect(tagOf(inserted), 'the tag in the text').to.not.equal(null);

          return inserted;
        });
      };

      it('keeps the text when the file was not inserted, with "Remove File"', () => {
        remove('file');
        cy.window().then((win) => {
          const after = view.read(win);

          expect(lines(after), 'the lines of the text').to.deep.equal(lines(original));
        });
      });

      ['file', 'all'].forEach((button) => {
        it(`removes only the tag of an inserted file, with "Remove ${button === 'all' ? 'All' : 'File'}"`, () => {
          insert().then((inserted) => {
            const tag = tagOf(inserted)[0];

            remove(button);
            cy.window().then((win) => {
              const after = view.read(win);

              expect(after, 'the text after the removal').to.not.include(tag);
              expect(lines(after), 'the lines of the text').to.deep.equal(lines(inserted.replace(tag, '')));
              keepsLines(after, lines(original));

              if (name === 'SCEditor source') {
                // The source view keeps the text as it is: only the tag and the space which "Insert" put before it are gone.
                expect(after, 'the source text after the removal').to.equal(inserted.replace(tag, ''));
                expect(inserted.replace(` ${tag}`, ''), 'the source text without the tag and the space of the insert').to.equal(original);
              }
            });
          });
        });
      });
    });
  });
});
