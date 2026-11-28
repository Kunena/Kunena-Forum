// The lists of topics (recent topics, the topics and posts of a user...) open on their first page when the address has no page: the
// page which the visitor opened last was kept in the session and came back when he opened the list again (from the menu, the tabs of
// the forum, a filter of the list, which leaves out the page).
describe('Kunena topic lists: the page of the address', () => {
  const list = '/index.php?option=com_kunena&view=topics&mode=replies&sel=-1&limit=2';
  const activePage = (body) => {
    const page = /page-item active[^>]*>\s*<[^>]*>\s*(\d+)/.exec(body);

    return page ? Number(page[1]) : 1;
  };

  it('opens on the first page after another page was opened', () => {
    cy.request(`${list}&limitstart=2`).its('body').then((body) => {
      expect(activePage(body), 'the second page').to.eq(2);
    });
    cy.request(list).its('body').then((body) => {
      expect(activePage(body), 'the list again, without a page').to.eq(1);
    });
  });
});
