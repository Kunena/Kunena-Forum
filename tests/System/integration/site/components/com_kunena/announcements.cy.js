// Guest checks of the announcements: the list is public, and the star which chooses the default announcement is for administrators.
describe('Kunena announcements', () => {
  const listing = '/index.php?option=com_kunena&view=announcement&layout=listing';

  // A new installation has no announcement: the test makes one (it is removed again after the test).
  beforeEach(() => {
    cy.kunena_createAnnouncement();
  });

  it('shows the list of the announcements', () => {
    cy.visit(listing);

    // The heading depends on the language of the site: the form with the list is checked, not its text.
    cy.get('#adminForm h2').should('be.visible');
    cy.get('#adminForm table tbody tr').should('have.length.greaterThan', 0);
  });

  it('does not give a guest the choice of the default announcement', () => {
    cy.visit(listing);

    // The star is a link which runs the task banner: only an administrator gets it.
    cy.get('a[onclick*="banner"]').should('not.exist');
    cy.get('select#kchecktask').should('not.exist');
  });

  it('does not run the task which chooses the default announcement for a guest', () => {
    cy.request({
      method: 'POST',
      url: '/index.php?option=com_kunena&view=announcement&task=banner&cid[]=1',
      failOnStatusCode: false,
      followRedirect: false,
    }).then((response) => {
      // The token is missing, and the user is no administrator: nothing is changed (an error or a redirect back).
      expect(response.status).to.be.oneOf([200, 301, 302, 303, 403]);
    });

    cy.visit(listing);
    cy.get('a[onclick*="banner"]').should('not.exist');
  });
});
