// A layout of an address which the template has no page for (view=topic&layout=message, layout=threadhistory, view=common...)
// went to the old views of Kunena 5, whose display methods are helpers of the old templates: they need data which such a request
// does not give, and the page was an error 500 (a fatal error for every visitor and every crawler). It is a page which does not
// exist: 404.
describe('Kunena: the layouts of the old views', () => {
  const base = '/index.php?option=com_kunena';

  it('answers 404 for a layout which is no page', () => {
    [
      'view=topic&layout=message',
      'view=topic&layout=threadhistory',
      'view=topic&layout=messagefield',
      'view=topic&layout=topicfield',
      'view=common&layout=breadcrumb',
      'view=user&layout=user',
    ].forEach((query) => {
      cy.request({ url: `${base}&${query}`, failOnStatusCode: false }).then((response) => {
        expect(response.status, query).to.eq(404);
      });
    });
  });
});
