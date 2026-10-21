// Addresses with a missing or a wrong value: a page is shown, or it is not found (a 404), but never an error page (a 500).
describe('Kunena addresses with a missing or wrong value', () => {
  const base = '/index.php?option=com_kunena';
  let topic;

  const status = (query) => cy.request({ url: `${base}&${query}`, failOnStatusCode: false, timeout: 40000 }).its('status');

  before(() => {
    cy.task(
      'queryDB',
      'SELECT t.id, t.category_id FROM #__kunena_topics AS t INNER JOIN #__kunena_categories AS c ON c.id = t.category_id WHERE t.hold = 0 AND c.published = 1 AND c.pubAccess = 1 ORDER BY t.id ASC LIMIT 1',
    ).then((rows) => {
      expect(rows, 'a topic which a guest can read').not.to.be.empty;
      topic = rows[0];
    });
  });

  it('shows a topic without the id of its category (it was a 500: null for an int)', () => {
    status(`view=topic&id=${topic.id}`).should('eq', 200);
  });

  it('shows a topic with a category id which is no number', () => {
    ['abc', '', '0x10'].forEach((catid) => {
      status(`view=topic&catid=${encodeURIComponent(catid)}&id=${topic.id}`).should('eq', 200);
    });
  });

  it('does not find a topic which is not there', () => {
    ['99999999', '0', '-1', 'abc'].forEach((id) => {
      status(`view=topic&id=${encodeURIComponent(id)}`).should('be.oneOf', [400, 404]);
    });
  });

  it('does not find a topic in a category which is not there (1e9 is a number)', () => {
    status(`view=topic&catid=1e9&id=${topic.id}`).should('eq', 404);
  });

  it('does not find a category which is not there', () => {
    ['99999999', '-1', 'abc'].forEach((catid) => {
      status(`view=category&catid=${encodeURIComponent(catid)}`).should('be.oneOf', [200, 404]);
    });
  });
});
