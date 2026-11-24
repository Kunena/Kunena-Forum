// The list "Topics needing attention" (view=topics&layout=moderator) showed the first page on every page: the topics were read
// without the start and the length of the page, and the page links went to the same topics again.
describe('Kunena topics needing attention: the pages of the list', () => {
  const base = '/index.php?option=com_kunena';
  const old = {};

  // The ids of the topics in the list of a page.
  const topicIds = (body) => {
    const ids = new Set();
    const list = String(body).replace(/<script[\s\S]*?<\/script>/gi, '');

    for (const match of list.matchAll(/data-topic-id="(\d+)"/g)) {
      ids.add(match[1]);
    }

    return [...ids];
  };

  beforeEach(() => {
    cy.kunena_setOption('threadsPerPage', '1').then((value) => {
      old.threadsPerPage = value ?? null;
    });
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  afterEach(() => {
    cy.kunena_setOption('threadsPerPage', old.threadsPerPage);
  });

  it('shows other topics on the second page', () => {
    cy.request(`${base}&view=topics&layout=moderator`).then((first) => {
      const page1 = topicIds(first.body);

      expect(page1, 'the topics of the first page').to.have.length(1);

      cy.request(`${base}&view=topics&layout=moderator&limitstart=1`).then((second) => {
        const page2 = topicIds(second.body);

        expect(page2, 'the topics of the second page').to.have.length(1);
        expect(page2[0], 'the topic of the second page').not.to.equal(page1[0]);
      });
    });
  });
});
