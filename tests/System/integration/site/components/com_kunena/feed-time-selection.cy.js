// The feed of the topics took the period (sel) of the address as it was and gave it to new DateTime("<sel> ago"): an address with
// another value (sel=abc, sel=24) gave an error page (500) instead of the feed. Only the periods of the setting are used now.
describe('Kunena feed of the topics: the period of the address', () => {
  const feed = '/index.php?option=com_kunena&view=topics&layout=feed&format=feed&type=rss&mode=topics';

  it('gives the feed for an unknown period', () => {
    ['abc', '24', '1 week'].forEach((sel) => {
      cy.request({ url: `${feed}&sel=${encodeURIComponent(sel)}`, failOnStatusCode: false }).then((response) => {
        expect(response.status, `sel=${sel}`).to.eq(200);
        expect(String(response.body), `sel=${sel}`).to.include('<rss');
      });
    });
  });
});
