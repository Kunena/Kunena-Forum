// The toolbar of SCEditor follows the buttons which the template keeps (editorButtons, saved by the backend). The list was filtered as a
// text: the last button (maximize) was never taken out, and the groups without buttons stayed as empty groups. The backend kept the
// buttons table and horizontalrule only for settings which the templates did not have, so they were gone once the settings were
// saved: the templates have them now.
describe('Kunena SCEditor: the buttons of the toolbar', () => {
  const base = '/index.php?option=com_kunena';
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/agora/config/params.ini`;
  const old = {};

  const set = (text, name, value) => {
    const line = `${name}="${value}"`;
    const pattern = new RegExp(`^${name}=.*$`, 'm');

    return pattern.test(text) ? text.replace(pattern, line) : `${text.trimEnd()}\n${line}\n`;
  };

  // The form of a new topic with SCEditor and the buttons which the template keeps.
  const openWith = (buttons) => {
    cy.then(() => cy.writeFile(params, set(set(old.params, 'editorType', 'sceditor'), 'editorButtons', buttons)));
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty');
  };
  const button = (name) => cy.get('#postform .sceditor-toolbar').first().find(`.sceditor-button-${name}`);

  before(() => {
    cy.kunena_setOption('template', 'agora').then((value) => {
      old.template = value;
    });
    cy.readFile(params).then((text) => {
      old.params = text;
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

  it('takes out every button which is not kept, also the last one, and leaves no empty group', () => {
    openWith('cut,copy,pastetext,source,undo,redo,bold,italic,quote,code');
    button('bold').should('exist');
    button('code').should('exist');
    button('maximize').should('not.exist');
    button('table').should('not.exist');
    button('underline').should('not.exist');
    cy.get('#postform .sceditor-toolbar').first().find('.sceditor-group').each(($group) => {
      expect($group.find('.sceditor-button').length, 'the buttons of a group').to.be.greaterThan(0);
    });
  });

  it('shows the buttons table, horizontal rule and maximize when they are kept', () => {
    openWith('cut,copy,pastetext,source,undo,redo,bold,table,horizontalrule,maximize');
    button('table').should('exist');
    button('horizontalrule').should('exist');
    button('maximize').should('exist');
    // A name in another name is no button: link is not kept by unlink.
    button('link').should('not.exist');
  });

  it('has the settings of the buttons table and horizontal rule in the backend', () => {
    cy.doAdministratorLogin();
    cy.visit('/administrator/index.php?option=com_kunena&view=template&layout=edit&name=agora');
    cy.get('select[name="jform[Table]"]').should('exist');
    cy.get('select[name="jform[Horizontalrule]"]').should('exist');
    // Translated: no key of a language string in the labels.
    cy.get('label[for="jform_Table"], label[for="jform_Horizontalrule"]').each(($label) => {
      expect($label.text(), 'the label').to.not.contain('COM_KUNENA_');
    });
  });
});
