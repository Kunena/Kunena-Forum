Cypress.on('uncaught:exception', (err, runnable) => {
  // returning false here prevents Cypress from
  // failing the test
  return false
})

describe('Test Kunena installer', () => {
    beforeEach(() => {
        cy.doAdministratorLogin();
        cy.visit('/administrator/index.php?option=com_installer&view=install');
    });

    it('has a title', () => {
        cy.get('h1.page-title').should('contain.text', 'Extensions: Install');
    });

    it('can install from URL tab', () => {
        cy.get('joomla-tab-element#url').should('exist');
        cy.get('joomla-tab-element#url').click({ force: true });
        cy.get('[role="tablist"] > [aria-controls="url"]').click({ force: true });
        cy.get('#install_url').type('https://github.com/Kunena/Kunena-Forum/releases/download/6.4.2/pkg_kunena_v6.4.2_2025-04-22.zip');
        cy.get('#installbutton_url').click({ force: true });
        // Check if the installation was successful
        cy.contains('Installation of the package was successful.');
    });
});
