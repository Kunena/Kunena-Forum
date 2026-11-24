// The list of the users, searched (the search box posts the words; a typed address has them in search=): the links to the other
// pages lost the search, so page 2 was a page of the whole list. The address of the links keeps only the values of the request
// made of letters, digits and spaces ("ci-" was dropped), and nothing that was posted.
describe('Kunena user list: the pages of a search', () => {
  const base = '/index.php?option=com_kunena&view=user&layout=list&limit=1';
  const link = (body) => {
    const text = String(body).replaceAll('&amp;', '&');

    return (text.match(/href="[^"]*[?&](limit)?start=1\b[^"]*"/) || [''])[0];
  };

  it('keeps a typed search in the links', () => {
    cy.request(`${base}&search=ci-`).then((page) => {
      expect(link(page.body), 'the link to page 2').to.match(/search=ci-/);
    });
  });

  it('keeps a posted search in the links', () => {
    cy.request({ method: 'POST', url: base, form: true, body: { search: 'ci-' } }).then((page) => {
      expect(link(page.body), 'the link to page 2').to.match(/search=ci-/);
    });
  });
});
