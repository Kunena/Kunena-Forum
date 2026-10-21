// Every view of the administration of Kunena for the administrator of the test site. Cypress fails a test by itself when a page has
// an error in its JavaScript, and the afterEach of the support file fails it for a PHP notice or warning on the page, so opening a
// view is also a check of its code and its scripts.
describe('Kunena administration', () => {
  const base = '/administrator/index.php?option=com_kunena';

  const phpErrors = ['Fatal error', 'Parse error', 'Call to undefined', 'Uncaught ', 'Array to string', 'Unknown column', "doesn't exist"];

  beforeEach(() => {
    cy.doAdministratorLogin();
  });

  // view => something which is on the page
  const views = {
    cpanel: '#kunena',
    categories: 'form#adminForm',
    users: 'form#adminForm',
    ranks: 'form#adminForm',
    smilies: 'form#adminForm',
    templates: 'form#adminForm',
    tools: '#kunena',
    trashs: 'form#adminForm',
    logs: 'form#adminForm',
    stats: '.statistics',
    plugins: 'form#adminForm',
    attachments: 'form#adminForm',
    mailsqueues: 'form#adminForm',
    socials: 'form#adminForm',
  };

  Object.entries(views).forEach(([view, selector]) => {
    it(`opens the view ${view}`, () => {
      cy.visit(`${base}&view=${view}`);

      cy.get(selector).should('exist');
      cy.document().then((doc) => {
        const text = doc.body.innerText;

        phpErrors.forEach((error) => {
          expect(text, `the view ${view} shows "${error}"`).not.to.include(error);
        });
      });
    });
  });

  it('opens the form of a new category', () => {
    cy.visit(`${base}&view=category&layout=create`);

    cy.get('form#adminForm').should('exist');
    cy.get('input[name="name"]').should('exist');
  });

  it('opens the form to edit a category', () => {
    cy.task('queryDB', 'SELECT id FROM #__kunena_categories WHERE parentid > 0 ORDER BY id ASC LIMIT 1').then((categories) => {
      cy.visit(`${base}&view=category&layout=edit&catid=${categories[0].id}`);

      cy.get('form#adminForm').should('exist');
      cy.get('input[name="name"]').should('exist');
    });
  });

  it('opens the form to edit a user', () => {
    cy.task('queryDB', 'SELECT userid FROM #__kunena_users ORDER BY userid ASC LIMIT 1').then((users) => {
      cy.visit(`${base}&view=user&layout=edit&userid=${users[0].userid}`);

      cy.get('form#adminForm').should('exist');
    });
  });
});
