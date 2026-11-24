// The option "Module id for the Webauthn login" of the template puts a Joomla login module (with the passkey button) into the
// login menu of the forum. The test was the wrong way round: an existing module was never shown, and only a module id which
// does not exist showed the first login module of the site, between two apostrophes.
describe('Kunena login menu: the login module of the option moduleIdWebauthn', () => {
  const params = () => `${Cypress.expose('cmsPath')}/components/com_kunena/template/agora/config/params.ini`;
  const old = {};

  before(() => {
    cy.kunena_setOption('template', 'agora').then((value) => {
      old.template = value ?? null;
    });
    cy.task('queryDB', "SELECT id FROM #__modules WHERE module = 'mod_login' AND client_id = 0 AND published = 1 ORDER BY id LIMIT 1").then((rows) => {
      old.module = rows[0].id;
    });
    cy.readFile(params()).then((text) => {
      old.params = text;

      return cy.then(() => cy.writeFile(params(), text.replace(/^moduleIdWebauthn=.*$/m, `moduleIdWebauthn="${old.module}"`)));
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  after(() => {
    cy.then(() => cy.writeFile(params(), old.params));
    cy.kunena_setOption('template', old.template);
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  it('shows the login module in the login menu', () => {
    cy.request('/index.php?option=com_kunena&view=category&layout=list').then((page) => {
      const body = String(page.body);
      const menu = body.slice(body.indexOf('id="kdesktop-userdropdown"'), body.indexOf('id="kdesktop-loginform"'));

      expect(menu, 'the login module in the menu').to.include(`login-form-${old.module}`);
      expect(menu, 'the apostrophes around it').not.to.match(/^[^<]*'\s*</);
    });
  });
});
