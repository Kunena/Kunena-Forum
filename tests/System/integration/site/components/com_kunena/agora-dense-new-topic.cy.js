// The layout "Dense" of Agora puts a button "New topic" above the filters of the lists of topics. It was there for every visitor:
// a guest (or a member who may not start a topic anywhere) got the button and then a page which refused him. It shows now only
// when the visitor may start a topic in a category.
describe('Kunena Agora layout Dense: the button for a new topic', () => {
  const params = () => `${Cypress.expose('cmsPath')}/components/com_kunena/template/agora/config/params.ini`;
  const url = '/index.php?option=com_kunena&view=topics&layout=default';
  const old = {};

  before(() => {
    cy.kunena_setOption('template', 'agora').then((value) => {
      old.template = value ?? null;
    });
    // Guests may not post (the default of a new install).
    cy.kunena_setOption('pubWrite', '0').then((value) => {
      old.pubWrite = value ?? null;
    });
    cy.readFile(params()).then((text) => {
      old.params = text;

      return cy.writeFile(params(), /^layoutSet=.*$/m.test(text) ? text.replace(/^layoutSet=.*$/m, 'layoutSet="dense"') : `${text.trimEnd()}\nlayoutSet="dense"\n`);
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  after(() => {
    cy.then(() => cy.writeFile(params(), old.params));
    cy.kunena_setOption('pubWrite', old.pubWrite);
    cy.kunena_setOption('template', old.template);
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  it('has no button for a guest', () => {
    cy.request(url).its('body').then((body) => {
      expect(String(body), 'the filters of the layout').to.contain('agora-list-filters-side');
      expect(String(body), 'the button').not.to.contain('agora-side-create');
    });
  });

  it('has the button for a member who may start a topic', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(url).its('body').should('contain', 'agora-side-create');
  });
});
