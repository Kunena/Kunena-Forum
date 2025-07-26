Cypress.on('uncaught:exception', (err, runnable) => {
  // returning false here prevents Cypress from
  // failing the test
  return false
})

describe('Test Kunena installer', () => {
    beforeEach(() => {
        cy.doAdministratorLogin();
        cy.visit('/administrator/index.php?option=com_installer&view=manage');
    });

    it('can uninstall a component from URL tab', () => {
        // Uninstall the component
        cy.searchForItem('Kunena');
        cy.checkAllResults();
        cy.clickToolbarButton('Action');
        cy.contains('Uninstall').click();
        cy.clickDialogConfirm(true);
        // Check if the uninstallation was successful
        cy.contains('Uninstalling the component was successful');
    });
});
