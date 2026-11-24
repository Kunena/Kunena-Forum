// The search reads its values from the address too (the page links of the results, a link to a search). A list where the search
// expects one value (searchuser[]=x, searchatdate[]=x) or a list of the categories in the address (catids[]=3, as the form sends them
// by POST) gave an error page to any visitor: the values went to rtrim() and explode() as lists. They are read as one value, the
// categories as the list they are.
describe('Kunena search: lists in the address', () => {
  const base = '/index.php?option=com_kunena&view=search&query=test';

  ['searchuser[]=x', 'searchatdate[]=x', 'catids[]=3', 'catids[]=3&catids[]=4', 'catids[]=x'].forEach((value) => {
    it(`answers ${value} without a server error`, () => {
      cy.request({ url: `${base}&${value}`, failOnStatusCode: false }).its('status').should('be.lessThan', 500);
    });
  });

  it('searches the categories of a list in the address', () => {
    cy.request(`${base}&catids[]=3`).its('body').should('contain', 'kunena');
  });
});
