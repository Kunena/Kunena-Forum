// The sample data of Kunena (plugin Sample Data - Kunena) is installed by com_ajax, which runs on the site too. The step checked
// the token of the form only, which every visitor has: a guest ran it, and it switched the system plugin of Kunena on again (an
// administrator had switched it off), filled the empty tables of the forum and gave the user one post. The step is for the
// administrators of the forum in the backend now.
describe('Kunena sample data: who may install it', () => {
  const step = 'option=com_ajax&group=sampledata&plugin=SampledataApplyStep1&type=kunena&format=json';
  const plugin = "type = 'plugin' AND folder = 'system' AND element = 'kunena'";
  let enabled;
  let posts;

  const systemPlugin = () => cy.task('queryDB', `SELECT enabled FROM #__extensions WHERE ${plugin}`).its('0.enabled').then(Number);

  before(() => {
    systemPlugin().then((value) => {
      enabled = value;
    });
    // The step gives the user who runs it one post: the administrator gets his number back.
    cy.task('queryDB', 'SELECT posts FROM #__kunena_users WHERE userid = 797').then((rows) => {
      posts = rows.length ? Number(rows[0].posts) : undefined;
    });
  });

  beforeEach(() => {
    // The system plugin switched off: the step switches it on.
    cy.task('queryDB', `UPDATE #__extensions SET enabled = 0 WHERE ${plugin}`);
  });

  afterEach(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__extensions SET enabled = ${enabled === undefined ? 1 : enabled} WHERE ${plugin}`));
    cy.then(() => {
      if (posts !== undefined) {
        cy.task('queryDB', `UPDATE #__kunena_users SET posts = ${posts} WHERE userid = 797`);
      }
    });
  });

  it('does not run for a guest on the site', () => {
    cy.request('/index.php?option=com_users&view=login').then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({ url: `/index.php?${step}&${token}=1`, failOnStatusCode: false });
    }).then((response) => {
      expect(JSON.stringify(response.body), 'the answer of the step').not.to.contain('"success":true,"message":"');
    });
    systemPlugin().should('eq', 0);
  });

  it('runs for an administrator in the backend', () => {
    cy.doAdministratorLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.request('/administrator/index.php?option=com_kunena&view=categories').then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({ url: `/administrator/index.php?${step}&${token}=1`, failOnStatusCode: false });
    });
    systemPlugin().should('eq', 1);
  });
});
