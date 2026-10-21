// The popups for mentions and replies: the answer of the server, for a guest and for a logged-in user.
describe('Kunena popup notifications', () => {
  const url = '/index.php?option=com_kunena&view=user&task=notifications&format=json';

  it('gives a guest no popups', () => {
    cy.request({ url: `${url}&after=1`, failOnStatusCode: false }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.headers['content-type']).to.include('application/json');
      expect(response.body).to.deep.include({ enabled: false, items: [] });
    });
  });

  it('does not load the script of the popups for a guest', () => {
    cy.visit('/index.php?option=com_kunena&view=category&layout=list');

    cy.get('script[src*="notifications.js"]').should('not.exist');
    cy.get('#kunena-notify').should('not.exist');
  });
});
