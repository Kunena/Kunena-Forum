// An announcement is seen when it is published and inside its dates. One which is not published, one which starts later and one
// which has ended are for the moderators: not in the list, not on their own page, not in the box of the announcements on the pages
// of the forum. The administrator (a moderator) sees them all on their pages.
describe('Kunena announcements: which ones are seen', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const rows = {
    visible: { published: 1, up: '1000-01-01 00:00:00', down: '1000-01-01 00:00:00' },
    unpublished: { published: 0, up: '1000-01-01 00:00:00', down: '1000-01-01 00:00:00' },
    future: { published: 1, up: '2099-01-01 00:00:00', down: '1000-01-01 00:00:00' },
    expired: { published: 1, up: '1000-01-01 00:00:00', down: '2001-01-01 00:00:00' },
  };

  const text = (name) => `ANNOUNCE${name.toUpperCase()}${stamp}`;

  beforeEach(() => {
    Object.entries(rows).forEach(([name, row]) => {
      cy.task(
        'queryDB',
        `INSERT INTO #__kunena_announcement (title, sdescription, description, created, published, showdate, publish_up, publish_down, ordering, created_by) VALUES ('Cypress announcement ${name} ${stamp}', '${text(name)} short', '${text(name)} long', NOW(), ${row.published}, 1, '${row.up}', '${row.down}', 0, 797)`,
      ).then((result) => {
        ids[name] = result.insertId;
      });
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_announcement WHERE title LIKE 'Cypress announcement % ${stamp}'`);
  });

  const pagesOf = (name) => [
    `${base}&view=announcement&layout=listing`,
    `${base}&view=announcement&layout=default&id=${ids[name]}`,
    `${base}&view=announcement&id=${ids[name]}`,
    `${base}&view=home`,
    `${base}&view=category&layout=list`,
    `${base}&view=topics`,
  ];

  const seen = (name) => {
    const found = [];

    pagesOf(name).forEach((url) => {
      cy.request({ url, failOnStatusCode: false }).then((response) => {
        if (String(response.body).includes(text(name))) {
          found.push(url.replace(base, ''));
        }
      });
    });

    return cy.then(() => found);
  };

  it('shows the published announcement to a guest, and nothing of the others', () => {
    seen('visible').then((found) => expect(found, 'the pages with the published announcement').to.include('&view=announcement&layout=listing').and.include(`&view=announcement&layout=default&id=${ids.visible}`));
    ['unpublished', 'future', 'expired'].forEach((name) => {
      seen(name).then((found) => expect(found, `the pages which show the ${name} announcement to a guest`).to.deep.eq([]));
    });
  });

  it('shows nothing of the others to a user', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    ['unpublished', 'future', 'expired'].forEach((name) => {
      seen(name).then((found) => expect(found, `the pages which show the ${name} announcement to a user`).to.deep.eq([]));
    });
  });

  it('shows them all on their pages to the administrator', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    Object.keys(rows).forEach((name) => {
      cy.request(`${base}&view=announcement&layout=default&id=${ids[name]}`).its('body').should('include', text(name));
    });
  });
});
