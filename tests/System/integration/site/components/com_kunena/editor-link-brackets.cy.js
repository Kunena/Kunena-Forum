// The link button of the editor writes [url=...]: a [ or ] in the URL (?filters[term]=...) must not end the tag too early, so the
// brackets become %5B and %5D. The option debug loads the readable copy of the BBCode plugin of the editor (dev/sceditor/bbcode.js)
// instead of the minified one (sceditor/bbcode.min.js): both copies are tried, in the source mode and in the WYSIWYG mode.
describe('Kunena editor: brackets in the URL of the link button', () => {
  const create = '/index.php?option=com_kunena&view=topic&layout=create&catid=3';
  const url = 'http://translate.limesurvey.org/projects/limesurvey2/el/default?filters[term]=Only+integer+values';
  const encoded = 'http://translate.limesurvey.org/projects/limesurvey2/el/default?filters%5Bterm%5D=Only+integer+values';
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/aurelia/config/params.ini`;
  let oldDebug;
  let oldTemplate;
  let templateSaved = false;
  let original;

  before(() => {
    // Read here rather than with kunena_getOption: a missing option gives undefined, and a then() which gives undefined yields the
    // rows of the query instead.
    cy.task('queryDB', "SELECT params FROM #__extensions WHERE element = 'com_kunena' AND type = 'component'").then((rows) => {
      const options = JSON.parse(rows[0].params || '{}');

      oldDebug = options.debug;
      oldTemplate = options.template;
      templateSaved = true;
    });

    // The form of the template Aurelia with the SCEditor: the site may run another template (Agora by default) or editor.
    cy.kunena_setOption('template', 'aurelia');
    cy.readFile(params).then((content) => {
      original = content;

      const line = 'editorType="sceditor"';

      cy.writeFile(params, /^editorType=.*$/m.test(content) ? content.replace(/^editorType=.*$/m, line) : `${content.trimEnd()}\n${line}\n`);
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  after(() => {
    cy.kunena_setOption('debug', oldDebug === undefined ? null : oldDebug);
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

  const editor = () => cy.window().its('sceditor').then((sceditor) => sceditor.instance(Cypress.$('#message')[0]));

  // Opens the create-topic page and leaves the editor empty, in the source mode or in the WYSIWYG mode.
  const openEditor = (sourceMode) => {
    cy.visit(create);
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty');
    editor().then((instance) => {
      instance.sourceMode(sourceMode);
      instance.val('');
    });
  };

  // Types in the drop-down of the link button and inserts the link; gives the value of the editor.
  const insertLink = (link, description) => {
    cy.get('#postform .sceditor-button-link').first().click();
    cy.get('.sceditor-insertlink #link').type(link, { parseSpecialCharSequences: false });

    if (description) {
      cy.get('.sceditor-insertlink #des').type(description);
    }

    cy.get('.sceditor-insertlink .button').click();

    return editor().then((instance) => instance.val());
  };

  [
    { debug: '0', script: '/sceditor/bbcode.min.js' },
    { debug: '1', script: '/dev/sceditor/bbcode.js' },
  ].forEach(({ debug, script }) => {
    context(`with debug ${debug}`, () => {
      before(() => {
        cy.kunena_setOption('debug', debug);
      });

      beforeEach(() => {
        cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
      });

      [
        { mode: 'source', sourceMode: true },
        { mode: 'WYSIWYG', sourceMode: false },
      ].forEach(({ mode, sourceMode }) => {
        it(`encodes the brackets of the URL with a description in the ${mode} mode`, () => {
          openEditor(sourceMode);
          cy.get(`script[src*="${script}"]`).should('exist');
          insertLink(url, 'Some link').should('include', `[url=${encoded}]Some link[/url]`);
        });

        it(`uses the encoded URL as the text without a description in the ${mode} mode`, () => {
          openEditor(sourceMode);
          insertLink(url).should('include', `[url=${encoded}]${encoded}[/url]`).and('not.include', '[term]');
        });
      });

      it('keeps an encoded URL as it is in the source mode', () => {
        openEditor(true);
        insertLink('http://example.com/?a%5Bb%5D=1').should('include', '[url=http://example.com/?a%5Bb%5D=1]').and('not.include', '%255B');
      });

      it('keeps a URL without brackets as it is typed in the WYSIWYG mode', () => {
        openEditor(false);
        insertLink('https://www.kunena.org/forum?x=1&y=2').should('include', '[url=https://www.kunena.org/forum?x=1&y=2]');
      });
    });
  });
});
