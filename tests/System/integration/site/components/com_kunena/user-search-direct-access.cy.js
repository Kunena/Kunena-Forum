// The autocomplete of the names (search.js, in the search form and in the user list) asked an address without a menu item. With
// "Direct Component Access" off, Kunena refuses a request without its menu item: the autocomplete got a 404 "Component not found".
// The layouts now give search.js the address made by the router, with the menu item of the forum.
describe('Kunena user search: the autocomplete works with direct component access off', () => {
  const base = '/index.php?option=com_kunena';
  const old = {};
  let menuId;

  const setOption = (name, value) => cy.kunena_setOption(name, value).then((previous) => {
    old[name] = previous ?? null;
  });

  // The address which the page gives to search.js, with the search text added as search.js adds it.
  const searchUrl = (text) => cy.window().then((win) => {
    const url = win.Joomla.getOptions('com_kunena.usersearch_url');

    expect(url, 'the address of the user search').to.be.a('string').and.not.be.empty;

    // The address starts with the path of the site, as the browser reads it: cy.request would put the baseUrl in front of it again.
    return new URL(`${url}${url.includes('?') ? '&' : '?'}usersearch=${encodeURIComponent(text)}`, win.location.href).href;
  });

  const names = (body) => (Array.isArray(body) ? body : JSON.parse(body)).map((user) => user.name);

  beforeEach(() => {
    setOption('accessComponent', '0');
    // The forum shows the usernames, and the super users are in the list of the users.
    setOption('username', '1');
    setOption('superAdminUserlist', '0');
    cy.task('queryDB', "SELECT id FROM #__menu WHERE client_id = 0 AND published = 1 AND link LIKE '%option=com_kunena&view=home%' ORDER BY id ASC LIMIT 1").then((items) => {
      expect(items.length, 'the Kunena menu item').to.eq(1);
      menuId = items[0].id;
    });
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
  });

  afterEach(() => {
    cy.then(() => Object.entries(old).forEach(([name, value]) => cy.kunena_setOption(name, value)));
    cy.then(() => Object.keys(old).forEach((name) => delete old[name]));
  });

  it('gives the names from the address of the search page', () => {
    cy.visit(`${base}&view=search&Itemid=${menuId}`);
    searchUrl('ci-').then((url) => {
      cy.request({ url, failOnStatusCode: false }).then((response) => {
        expect(response.status, 'the answer of the user search').to.eq(200);
        expect(names(response.body), 'the names').to.include('ci-admin');
      });
    });
  });

  it('shows the names in the autocomplete of the search form', () => {
    cy.visit(`${base}&view=search&Itemid=${menuId}`);
    // Tribute opens its list after its trigger, the @ (search.js keeps the default of Tribute).
    cy.get('#kusersearch').should('be.visible').type('@ci-');
    cy.get('.tribute-container').should('be.visible').and('contain', 'ci-admin');
  });

  it('gives the names from the address of the user list', () => {
    cy.visit(`${base}&view=user&layout=list&Itemid=${menuId}`);
    searchUrl('ci-').then((url) => {
      cy.request({ url, failOnStatusCode: false }).then((response) => {
        expect(response.status, 'the answer of the user search').to.eq(200);
        expect(names(response.body), 'the names').to.include('ci-admin');
      });
    });
  });
});
