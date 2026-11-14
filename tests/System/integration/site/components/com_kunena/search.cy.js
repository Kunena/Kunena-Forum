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

  it('exposes the count of the results as a status, without the pagination', () => {
    cy.visit(form);

    cy.get('input[name="query"]').type(word, { parseSpecialCharSequences: false });
    cy.get('input[name="query"]').closest('form').submit();

    cy.get('.kunena-search-result').first().parent().find('[role="status"]').should('have.length', 1).then(($status) => {
      expect($status.text()).to.match(/1 - \d+/);
      expect($status.find('nav, .pagination')).to.have.length(0);
    });
  });

  it('says nothing is found for a word which is not there', () => {
    cy.visit(form);

    cy.get('input[name="query"]').type('zzqqxxnotthere', { parseSpecialCharSequences: false });
    cy.get('input[name="query"]').closest('form').submit();

    cy.contains(topic.subject).should('not.exist');
    cy.get('body').should('contain.html', 'kunena');
  });

  // The test site runs Agora: the template is switched to Aurelia for these tests, and its compiled stylesheet is made again from
  // the SCSS of the checkout.
  describe('in Aurelia', () => {
    let oldTemplate;

    before(() => {
      cy.kunena_setOption('template', 'aurelia').then((value) => {
        oldTemplate = value;
      });
      cy.task('deleteRelativePath', 'cache/com_kunena_template');
    });

    after(() => {
      cy.task('deleteRelativePath', 'cache/com_kunena_template');
      cy.then(() => cy.kunena_setOption('template', oldTemplate ?? null));
    });

    // The stylesheet hides every radio and shows those of the search panel again through its class kunena-search-panel.
    it('shows the radios of what to show to a moderator', () => {
      cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
      cy.visit(form);

      cy.get('#search').should('have.class', 'kunena-search-panel');
      cy.get('#search input[name="show"]').should('have.length', 3).each((radio) => {
        cy.wrap(radio).should('be.visible');
      });
    });
  });
});
