// The login form of the forum posts to the current page: its address was written in the page without escaping, and a link with a
// quote in its query put HTML of the attacker in the page of each visitor who was not logged in.
describe('Kunena login form: the address of the form', () => {
  const payload = '"><svg/onload=alert(1)>';

  it('escapes the query of the current page', () => {
    cy.request(`/index.php?option=com_kunena&view=home&x=${encodeURIComponent(payload)}`).then((page) => {
      expect(page.body, 'the login form').to.contain('loginform');
      expect(page.body, 'no HTML of the query').not.to.contain(payload);
    });
  });

  it('still logs in through the form', () => {
    cy.visit('/index.php?option=com_kunena&view=home');
    // The form is in a menu of the page: its fields are filled and sent without opening the menu. Its button is named "submit",
    // which hides the submit() of the form, so the button is clicked.
    cy.get('#kdesktop-username').invoke('val', 'ci-user');
    cy.get('#klogin-desktop-passwd').invoke('val', 'ci-user-17082005-x');
    cy.get('#kdesktop-loginform button[type="submit"]').click({ force: true });
    cy.get('#kdesktop-loginform').should('not.exist');
    cy.request('/index.php?option=com_kunena&view=user&task=logout').its('status').should('be.lessThan', 500);
  });
});
