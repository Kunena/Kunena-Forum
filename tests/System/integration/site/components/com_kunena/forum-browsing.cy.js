describe('Kunena forum browsing', () => {
  it('displays the forum categories', () => {
    cy.visit('/index.php?option=com_kunena&view=category&layout=list');

    cy.get('#kcategoryform tbody tr').should('have.length.greaterThan', 0);
    cy.get('#kcategoryform tbody tr h3 a')
      .first()
      .should('have.attr', 'href')
      .and('include', 'view=category');
  });

  it('opens a topic from its category', () => {
    cy.task(
      'queryDB',
      'SELECT t.id, t.category_id, t.subject FROM #__kunena_topics AS t INNER JOIN #__kunena_categories AS c ON c.id = t.category_id WHERE t.hold = 0 AND c.published = 1 AND c.pubAccess = 1 ORDER BY t.id ASC LIMIT 1',
    ).then((topics) => {
      expect(topics, 'a published forum topic is available').not.to.be.empty;

      const topic = topics[0];

      cy.visit(`/index.php?option=com_kunena&view=category&catid=${topic.category_id}`);
      cy.contains('#ktopicsform .topic-list a.topictitle', topic.subject).click();

      cy.get('.kunena-topic-item > h1').should('contain.text', topic.subject);
      cy.get('.topic-item-messages').should('be.visible');
      cy.get('.kunena-topic-item .pagination .page-item.active').should('contain.text', '1');
    });
  });

  it('searches for a topic by its subject', () => {
    cy.task(
      'queryDB',
      'SELECT t.subject FROM #__kunena_topics AS t INNER JOIN #__kunena_categories AS c ON c.id = t.category_id WHERE t.hold = 0 AND c.published = 1 AND c.pubAccess = 1 ORDER BY t.id ASC LIMIT 1',
    ).then((topics) => {
      expect(topics, 'a published forum topic is available').not.to.be.empty;

      const subject = topics[0].subject;

      cy.visit('/index.php?option=com_kunena&view=search');
      cy.get('#main-search input[name="query"]').type(subject, { parseSpecialCharSequences: false });
      cy.get('#main-search').closest('form').submit();

      cy.url().should('include', 'view=search');
      cy.contains(subject).should('be.visible');
    });
  });
});
