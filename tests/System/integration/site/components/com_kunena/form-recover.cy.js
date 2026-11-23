// "Recover the form" of the template (formRecover): what is written in the form of a new topic is kept in the storage of the browser
// (media/kunena/core/js/form-recover.js, Kunena's own code in place of Sisyphus) and is back after a reload, in the fields and in the
// editor (CKEditor 5 and SCEditor). A reset of the form removes it, and the hidden fields (the token of the form) are never kept.
// The spec switches the options of the template Aurelia in params.ini and puts the file back; nothing is posted.
describe('Kunena posting form: the text is recovered after a reload', () => {
  const base = '/index.php?option=com_kunena';
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/aurelia/config/params.ini`;
  const stamp = Date.now();
  const old = {};

  // The options of the template Aurelia: the ini file gets the values, the cache of the template is removed.
  const useOptions = (values) => {
    let text = old.params;

    Object.entries(values).forEach(([name, value]) => {
      const line = `${name}="${value}"`;
      const pattern = new RegExp(`^${name}=.*$`, 'm');

      text = pattern.test(text) ? text.replace(pattern, line) : `${text.trimEnd()}\n${line}\n`;
    });

    return cy.writeFile(params, text).then(() => cy.task('deleteRelativePath', 'cache/com_kunena_template'));
  };

  // The keys which the page keeps for the form of the topic, checked again until the check passes (the text is kept a moment after
  // the change).
  const keptKeys = (check) => cy.window().should((win) => {
    check(Object.keys(win.localStorage).filter((key) => key.includes('[id=postform]')));
  });
  const kept = (field) => cy.window().then((win) => {
    const key = Object.keys(win.localStorage).find((name) => name.includes('[id=postform]') && name.endsWith(field));

    return key ? win.localStorage.getItem(key) : null;
  });
  const openForm = () => {
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    cy.window().should('have.property', 'KunenaFormRecover');
  };

  before(() => {
    cy.kunena_setOption('template', 'aurelia').then((value) => {
      old.template = value;
    });
    cy.readFile(params).then((value) => {
      old.params = value;
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

  beforeEach(() => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
    cy.window().then((win) => win.localStorage.clear());
  });

  it('brings back the subject and the text of CKEditor 5 after a reload', () => {
    useOptions({ formRecover: 1, editorType: 'ckeditor' });
    openForm();
    cy.editor_ready();
    cy.get('#subject').type(`Recovered subject ${stamp}`);
    cy.editor_instance().then((editor) => {
      editor.focus();
      editor.insertText(`A text which survives a reload ${stamp}`);
    });
    // The text is kept half a second after the last change.
    keptKeys((keys) => expect(keys.some((key) => key.endsWith('[id=message][name=message]')), 'the text is kept').to.eq(true));
    cy.wait(800);
    kept('[id=message][name=message]').should('eq', `A text which survives a reload ${stamp}`);

    cy.reload();
    cy.editor_ready();
    cy.get('#subject').should('have.value', `Recovered subject ${stamp}`);
    cy.get('#message').should('have.value', `A text which survives a reload ${stamp}`);
    cy.editor_data().should('eq', `A text which survives a reload ${stamp}`);
  });

  it('brings back the text of SCEditor after a reload', () => {
    useOptions({ formRecover: 1, editorType: 'sceditor' });
    openForm();
    cy.window().should((win) => {
      expect(win.sceditor && win.sceditor.instance(win.document.getElementById('message')), 'the SCEditor').to.be.ok;
    });
    // The template sets the text of SCEditor once more just after the page is ready (also without the recovery): a text put in
    // before that is lost. A user does not type that fast.
    cy.wait(1000);
    cy.window().then((win) => {
      const editor = win.sceditor.instance(win.document.getElementById('message'));

      editor.focus();
      editor.insert(`SCEditor text ${stamp}`);
    });
    // SCEditor writes its iframe, not the textarea: the text is kept by the save of every 5 seconds.
    cy.wait(6000);
    kept('[id=message][name=message]').should('contain', `SCEditor text ${stamp}`);

    cy.reload();
    cy.window().should((win) => {
      const editor = win.sceditor && win.sceditor.instance(win.document.getElementById('message'));

      expect(editor, 'the SCEditor').to.be.ok;
      expect(editor.val(), 'the text of the editor').to.contain(`SCEditor text ${stamp}`);
    });
  });

  it('removes the kept text on a reset, and never keeps the token of the form', () => {
    useOptions({ formRecover: 1, editorType: 'sceditor' });
    openForm();
    cy.get('#subject').type(`To be reset ${stamp}`);
    keptKeys((keys) => {
      expect(keys.some((key) => key.endsWith('[id=subject][name=subject]')), 'the subject is kept').to.eq(true);
      // The hidden fields (the token of Joomla, the ids of the form) are set by the server.
      expect(keys.some((key) => /\[id=[^\]]*\]\[name=[0-9a-f]{32}\]$/.test(key)), 'the token is not kept').to.eq(false);
    });

    cy.get('#postform').then(($form) => $form[0].reset());
    keptKeys((keys) => expect(keys, 'the kept fields').to.have.length(0));
    cy.reload();
    cy.get('#subject').should('have.value', '');
  });

  // The radio buttons of a name (the topic icons) have an id each: the one chosen last is brought back, also when it comes before
  // a button chosen earlier (each checked button was kept under its own key, and the last one of the page won).
  it('brings back the topic icon chosen last', () => {
    useOptions({ formRecover: 1, editorType: 'sceditor' });
    openForm();
    cy.get('input[name="topic_emoticon"]').should('have.length.greaterThan', 2);
    cy.get('input[name="topic_emoticon"]').eq(2).check({ force: true });
    cy.wait(800);
    cy.get('input[name="topic_emoticon"]').eq(1).check({ force: true });
    cy.wait(800);

    cy.reload();
    cy.window().should('have.property', 'KunenaFormRecover');
    cy.get('input[name="topic_emoticon"]').eq(1).should('be.checked');
    cy.get('input[name="topic_emoticon"]').eq(2).should('not.be.checked');
  });

  it('keeps nothing when the option is off', () => {
    useOptions({ formRecover: 0, editorType: 'sceditor' });
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    cy.get('#subject').type(`Not kept ${stamp}`);
    cy.wait(800);
    cy.window().should('not.have.property', 'KunenaFormRecover');
    keptKeys((keys) => expect(keys, 'the kept fields').to.have.length(0));
  });
});
