// The cookie kunena_template chooses the template of a visitor. It is a name of a template, never a path: a path (../) led out
// of the folder of the templates, and the file template.php of the folder it found was loaded.
describe('Kunena templates: the template of the cookie', () => {
  const page = '/index.php?option=com_kunena&view=category&layout=list';
  const templateOf = (cookie) => {
    cy.clearCookie('kunena_template');

    if (cookie) {
      cy.setCookie('kunena_template', cookie);
    }

    return cy.request(page).then((response) => /id="kunena" class="layout ?([a-z]*)/.exec(response.body)[1] || 'aurelia');
  };
  let oldTemplate;

  before(() => {
    cy.kunena_setOption('template', 'agora').then((old) => {
      oldTemplate = old;
    });
  });

  after(() => {
    cy.kunena_setOption('template', oldTemplate ?? null);
  });

  it('uses the template of the options without a cookie, and the template named in the cookie', () => {
    templateOf('').should('eq', 'agora');
    templateOf('aurelia').should('eq', 'aurelia');
  });

  it('does not take a path from the cookie', () => {
    ['../template/aurelia', '..%2Ftemplate%2Faurelia', './aurelia', 'aurelia/../aurelia', '/aurelia'].forEach((cookie) => {
      templateOf(cookie).should('eq', 'agora');
    });
  });
});
