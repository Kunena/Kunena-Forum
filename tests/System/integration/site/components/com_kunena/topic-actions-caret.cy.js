// The split dropdowns of the topic page (Action, Lock, Moderation) had a Bootstrap 2/3 <span class="caret"> in their toggle half:
// Bootstrap 5 does not style it, so no caret was shown. The toggles now use the Bootstrap 5 split markup, whose caret is drawn by
// .dropdown-toggle::after. The test only reads the database: it opens a topic which already exists.
describe('Kunena topic actions: the caret of the split dropdowns', () => {
  const base = '/index.php?option=com_kunena';

  beforeEach(() => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  it('shows a caret in the toggles of the topic actions, which opens the menu', () => {
    cy.task(
      'queryDB',
      'SELECT t.id, t.category_id FROM #__kunena_topics AS t INNER JOIN #__kunena_categories AS c ON c.id = t.category_id WHERE t.hold = 0 AND t.moved_id = 0 AND c.published = 1 ORDER BY t.id LIMIT 1',
    ).then((rows) => {
      expect(rows.length, 'a published topic').to.equal(1);
      cy.visit(`${base}&view=topic&catid=${rows[0].category_id}&id=${rows[0].id}`);
    });

    // Whichever variant the template renders: #topic-actions (compact) or #topic-actions-toolbar (full actions).
    cy.get('#topic-actions, #topic-actions-toolbar').find('.dropdown-toggle-split').should('have.length.greaterThan', 0);
    cy.get('#topic-actions, #topic-actions-toolbar, .topic-item-messages').find('.caret').should('not.exist');

    cy.get('#topic-actions, #topic-actions-toolbar').find('.dropdown-toggle-split').first().then(($toggle) => {
      const after = $toggle[0].ownerDocument.defaultView.getComputedStyle($toggle[0], '::after');

      expect(parseFloat(after.borderTopWidth), 'the border which draws the caret').to.be.greaterThan(0);

      cy.wrap($toggle).click();
      cy.wrap($toggle).siblings('.dropdown-menu').should('have.class', 'show').and('be.visible');
    });
  });
});
