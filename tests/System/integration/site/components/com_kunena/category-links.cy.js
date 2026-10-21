// Guest checks of the category pages: the header of a category has an id and a link like #<id> goes to the category.
describe('Kunena category links', () => {
  const list = '/index.php?option=com_kunena&view=category&layout=list';

  it('gives the header of each section an id with the id of the category', () => {
    cy.visit(list);

    cy.get('h1[id^="category-header"]')
      .should('have.length.greaterThan', 0)
      .each(($header) => {
        expect($header.attr('id')).to.match(/^category-header\d+$/);
      });
  });

  it('goes to a category with a link like #<id>', () => {
    cy.visit(list);

    cy.get('tr[id^="category"]').first().invoke('attr', 'id').then((rowId) => {
      const id = rowId.replace('category', '');

      // The address of the list is redirected to its SEF address: a second visit with only another #hash is no page load for
      // Cypress. Another page comes first, so the list is loaded again.
      cy.visit('/index.php?option=com_kunena&view=search');
      cy.visit(`${list}#${id}`);

      cy.location('hash').should('eq', `#${id}`);
      cy.get(`#${rowId}`).should('be.visible').should(($row) => {
        // The script scrolls to the category when the page is loaded: the row is in the window.
        const top = $row[0].getBoundingClientRect().top;

        expect(top, 'the category is in the window').to.be.within(-5, Cypress.config('viewportHeight'));
      });
    });
  });

  it('does not show IP addresses to a guest', () => {
    cy.visit(list);
    cy.get('tr[id^="category"] h3 a').first().click();
    cy.get('#categoryactions .category-item a.topictitle').first().click();

    cy.get('.topic-item-messages').should('be.visible');
    cy.get('.topic-item-messages').should('not.contain.text', 'IP:');
  });
});
