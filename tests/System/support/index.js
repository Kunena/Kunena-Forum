import('./commands.mjs');
import('joomla-cypress');

// The tour "Welcome to Joomla" starts by itself for the administrator and takes the page of the backend to the dashboard (now
// and then before a form is saved): it is switched off while the tests run, the parameters of the user are put back after them.
let adminParams = null;

before(() => {
  cy.task('startMailServer');
  cy.task('clearLogs');
  cy.task('queryDB', `SELECT id, params FROM #__users WHERE username = '${Cypress.expose('username')}'`).then((rows) => {
    adminParams = rows[0] ?? null;

    if (adminParams) {
      cy.task('queryDB', `UPDATE #__users SET params = JSON_SET(params, '$.allowTourAutoStart', '0') WHERE id = ${adminParams.id}`);
    }
  });
});

afterEach(() => {
  cy.checkForPhpNoticesOrWarnings();
  cy.task('checkForLogs');
  cy.task('cleanupDB');
});

after(() => {
  cy.kunena_fixCategoryCounters();

  if (adminParams) {
    cy.task('queryDB', `UPDATE #__users SET params = '${adminParams.params.replaceAll('\\', '\\\\').replaceAll("'", "\\'")}' WHERE id = ${adminParams.id}`);
  }
});
