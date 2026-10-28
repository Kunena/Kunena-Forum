// The profile of the forum comes from a plugin (Kunena - Kunena, its option "profile", or another integration). With none, the
// forum uses the base class KunenaProfile. Its names were empty: every name of the forum (the authors of the posts) was an empty
// text. And the pages of the profiles stayed, though "if profile integration is disabled, this view doesn't exist": the views
// asked for a class KunenaProfileNone which is gone.
describe('Kunena profile integration switched off', () => {
  const base = '/index.php?option=com_kunena';
  const plugin = "type = 'plugin' AND folder = 'kunena' AND element = 'kunena'";
  let params;

  beforeEach(() => {
    cy.task('queryDB', `SELECT params FROM #__extensions WHERE ${plugin}`).then((rows) => {
      params = rows[0].params;
      const value = JSON.parse(params || '{}');

      value.profile = '0';

      return cy.task('queryDB', `UPDATE #__extensions SET params = '${JSON.stringify(value)}' WHERE ${plugin}`);
    });
  });

  afterEach(() => {
    cy.then(() => {
      if (params !== undefined) {
        cy.task('queryDB', `UPDATE #__extensions SET params = '${params}' WHERE ${plugin}`);
      }
    });
  });

  it('shows the names of the authors', () => {
    // Topic 9 of the test site: the posts of ci-user.
    cy.request(`${base}&view=topic&catid=3&id=9`).its('body').then((body) => {
      expect(body, 'the name of the author').to.match(/>\s*ci-user\s*</);
    });
  });

  it('has no profile page', () => {
    cy.request({ url: `${base}&view=user&userid=798`, failOnStatusCode: false }).its('status').should('eq', 404);
  });
});
