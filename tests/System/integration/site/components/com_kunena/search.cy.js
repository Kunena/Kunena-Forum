// The search of the forum, as a visitor uses it: the form with a word, and the page with the results.
describe('Kunena search', () => {
  const form = '/index.php?option=com_kunena&view=search';

  // A topic of the test site, and a word from its subject which the search finds.
  let topic;
  let word;

  before(() => {
    cy.task('queryDB', 'SELECT t.id, t.subject FROM #__kunena_topics AS t INNER JOIN #__kunena_categories AS c ON c.id = t.category_id WHERE t.hold = 0 AND c.published = 1 AND c.pubAccess = 1 ORDER BY t.id DESC LIMIT 1').then((topics) => {
      expect(topics, 'a topic which a guest can read').not.to.be.empty;
      topic = topics[0];
      word = topic.subject.split(/\s+/).sort((a, b) => b.length - a.length)[0];
    });
  });

  it('shows the form with a field for the words', () => {
    cy.visit(form);

    cy.get('input[name="query"]').should('exist');
    cy.get('form button[type="submit"]').should('exist');
  });

  it('finds a topic by a word of its subject', () => {
    cy.visit(form);

    cy.get('input[name="query"]').type(word, { parseSpecialCharSequences: false });
    cy.get('input[name="query"]').closest('form').submit();

    cy.contains(topic.subject).should('exist');
  });

  it('says nothing is found for a word which is not there', () => {
    cy.visit(form);

    cy.get('input[name="query"]').type('zzqqxxnotthere', { parseSpecialCharSequences: false });
    cy.get('input[name="query"]').closest('form').submit();

    cy.contains(topic.subject).should('not.exist');
    cy.get('body').should('contain.html', 'kunena');
  });
});
