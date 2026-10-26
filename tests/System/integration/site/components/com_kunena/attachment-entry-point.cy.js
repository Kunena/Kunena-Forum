// The old light application of the attachments (src/Application/attachment.php) was still in the component: it can be opened
// directly, without the router of Joomla, and it looked for Joomla in the wrong folder, so it stopped with an error which showed
// the paths of the server.
describe('Kunena: the old entry point of the attachments', () => {
  it('is not in the component', () => {
    cy.request({ url: '/components/com_kunena/src/Application/attachment.php?query=1', failOnStatusCode: false }).then((response) => {
      expect(response.status, 'the status').to.eq(404);
      expect(String(response.body), 'no error of PHP').not.to.contain('Fatal error');
    });
  });
});
