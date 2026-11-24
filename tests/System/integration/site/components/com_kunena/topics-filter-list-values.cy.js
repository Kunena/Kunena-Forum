// The filter bar of the lists of topics (fq, fauthor, fstatus, ftag, flang) and of the moderation log (fop, fcat, fuser) reads one
// value of each: a list in the address (fauthor[]=x, from a crawler or a hand-made link) gave an error page to any visitor. A list
// is no value of a filter now, as for the other values of one value. (The Itemid: without it the page redirects first, and the
// redirect leaves the list out.)
describe('Kunena filters: lists in the address', () => {
  const lists = ['default', 'user', 'moderator'];
  const filters = ['fq', 'fauthor', 'fstatus', 'ftag', 'flang'];

  lists.forEach((layout) => {
    it(`answers the filters of topics/${layout} as lists without a server error`, () => {
      filters.forEach((name) => {
        cy.request({ url: `/index.php?option=com_kunena&Itemid=138&view=topics&layout=${layout}&${name}[]=x`, failOnStatusCode: false })
          .its('status').should('be.lessThan', 500);
      });
    });
  });

  it('answers the filters of the moderation log as lists without a server error', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    ['fop', 'fcat', 'fuser'].forEach((name) => {
      cy.request({ url: `/index.php?option=com_kunena&Itemid=138&view=topic&layout=modlog&${name}[]=x`, failOnStatusCode: false })
        .its('status').should('be.lessThan', 500);
    });
  });
});
