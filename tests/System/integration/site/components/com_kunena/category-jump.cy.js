// The box "jump to a category" (task category.jump) sends the visitor to the chosen category; without a category (or with a list
// in the request) it sends him to the start of the forum, without an error.
describe('Kunena categories: the jump box', () => {
  const base = '/index.php?option=com_kunena';

  const jump = (query) => cy.request({ url: `${base}&view=category&task=jump&${query}`, failOnStatusCode: false, followRedirect: false });

  it('sends the visitor to the chosen category', () => {
    jump('catid=3').then((response) => {
      expect(response.status, 'a redirect').to.be.within(300, 399);
      cy.request(response.redirectedToUrl).its('body').should('include', 'General Discussion');
    });
  });

  it('sends the visitor to the start of the forum without a category, and with a list in the request', () => {
    ['catid=0', 'catid[]=3'].forEach((query) => {
      jump(query).then((response) => {
        expect(response.status, `a redirect for ${query}`).to.be.within(300, 399);
        expect(response.redirectedToUrl, `the address for ${query}`).to.not.include('catid=3');
      });
    });
  });
});
