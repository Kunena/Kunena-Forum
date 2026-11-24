// "Published" and "Show date" of an announcement are yes/no selects of the form, but the save took any number of the request: a
// value outside the small columns (published=200) was an error of the database and the announcement was lost; another number
// (published=5) was stored and treated as unpublished. A value outside the choices is the default of the field (yes) now.
describe('Kunena announcements: the yes/no fields of the form', () => {
  const title = `Cypress announcement choices ${Date.now()}`;

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_announcement WHERE title = '${title}'`);
  });

  it('saves the announcement with yes for values which are no choice', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.request('/index.php?option=com_kunena&view=announcement&layout=create').then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: '/index.php?option=com_kunena&view=announcement&task=save',
        form: true,
        body: { title, sdescription: 'A short text.', description: '', published: 200, showdate: 300, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      }).its('status').should('be.lessThan', 500);
    });
    cy.task('queryDB', `SELECT published, showdate FROM #__kunena_announcement WHERE title = '${title}'`).then((rows) => {
      expect(rows, 'the announcement').to.have.length(1);
      expect(Number(rows[0].published), 'published').to.eq(1);
      expect(Number(rows[0].showdate), 'show the date').to.eq(1);
    });
  });
});
