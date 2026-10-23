// What the administrator does with the announcements: writes one with the form, and chooses which one is the default (the banner,
// marked with a filled star on the list). It needs the login of the administrator of the test site.
describe('Kunena announcements for an administrator', () => {
  const listing = '/index.php?option=com_kunena&view=announcement&layout=listing';
  const title = `Cypress announcement ${Date.now()}`;

  let chosenBefore;

  before(() => {
    cy.kunena_getOption('bannerAnnouncement').then((value) => {
      chosenBefore = value;
    });
  });

  beforeEach(() => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  after(() => {
    // What the tests wrote with the form, and the choice of the default announcement, are put back.
    cy.task('queryDB', `DELETE FROM #__kunena_announcement WHERE title LIKE 'Cypress announcement %'`);
    cy.kunena_setOption('bannerAnnouncement', chosenBefore ?? '0');
  });

  it('writes an announcement with the form and shows it on the list', () => {
    cy.visit('/index.php?option=com_kunena&view=announcement&layout=create');

    cy.get('input[name="title"]').type(title, { parseSpecialCharSequences: false });
    cy.get('textarea[name="sdescription"]').type('The short text of the announcement.');
    cy.get('select[name="published"]').select('1');
    cy.get('input[name="submit"]').click();

    cy.visit(listing);
    cy.contains('tr', title).should('exist');
  });

  it('saves an announcement with a title which is longer than its column, shortened', () => {
    // The title is TINYTEXT (255 bytes): a longer one was an error of the database and the announcement was not saved.
    cy.request('/index.php?option=com_kunena&view=announcement&layout=create').then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: '/index.php?option=com_kunena&view=announcement&task=save',
        form: true,
        body: { title: `${title} long ${'x'.repeat(300)}`, sdescription: 'A short text.', description: '', published: 1, showdate: 1, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      }).its('status').should('be.lessThan', 500);
    });
    cy.task('queryDB', `SELECT title FROM #__kunena_announcement WHERE title LIKE '${title} long%'`).then((rows) => {
      expect(rows, 'the announcement').to.have.length(1);
      expect(rows[0].title.length, 'the length of the title').to.be.at.most(255);
    });
  });

  it('chooses an older announcement as the default one, with the star', () => {
    cy.kunena_createAnnouncement({ title: 'Cypress announcement older' });
    cy.kunena_createAnnouncement({ title: 'Cypress announcement newer' });

    cy.visit(listing);

    // The newest one is the default as long as no other one is chosen.
    cy.contains('tr', 'Cypress announcement newer').find('.text-warning').should('exist');
    cy.contains('tr', 'Cypress announcement older').find('.text-warning').should('not.exist');

    cy.contains('tr', 'Cypress announcement older').find('a[onclick*="banner"]').click();

    cy.contains('tr', 'Cypress announcement older').find('.text-warning').should('exist');
    cy.contains('tr', 'Cypress announcement newer').find('.text-warning').should('not.exist');
  });
});
