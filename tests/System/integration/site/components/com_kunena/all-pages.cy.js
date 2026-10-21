// Smoke test of the pages of the forum for a guest: every page answers as it should (the page, or a refusal for a page which
// needs a login), and none of them shows a PHP error. The pages for a logged-in user are in all-pages-user.cy.js.
describe('Kunena pages for a guest', () => {
  const base = '/index.php?option=com_kunena';

  // What a PHP error looks like in a page (the debug mode of Joomla shows them).
  const phpErrors = ['Fatal error', 'Parse error', 'Warning:', 'Notice:', 'Deprecated:', 'Call to undefined', 'Uncaught ', 'Array to string'];

  const assertNoPhpError = (body) => {
    phpErrors.forEach((text) => {
      expect(body, `the page shows "${text}"`).not.to.include(text);
    });
  };

  // Pages which a guest may open.
  const open = {
    'the home page': 'view=home',
    'the list of the categories': 'view=category&layout=list',
    'the recent topics': 'view=topics&layout=default&mode=recent',
    'the topics without replies': 'view=topics&layout=default&mode=noreplies',
    'the unread topics': 'view=topics&layout=unread',
    'the recent posts': 'view=topics&layout=posts&mode=recent',
    'the topics which need attention': 'view=topics&layout=moderator',
    'the search form': 'view=search',
    'the results of a search': 'view=search&query=test',
    'the statistics': 'view=statistics',
    'the credits': 'view=credits',
    'the help page': 'view=misc',
    'the list of the users': 'view=user&layout=list',
    'the list of the announcements': 'view=announcement&layout=listing',
  };

  Object.entries(open).forEach(([name, query]) => {
    it(`opens ${name}`, () => {
      cy.request({ url: `${base}&${query}`, failOnStatusCode: false }).then((response) => {
        expect(response.status).to.eq(200);
        expect(response.headers['content-type']).to.include('text/html');
        expect(response.body).to.include('kunena');
        assertNoPhpError(response.body);
      });
    });
  });

  // Pages which ask a guest to log in, or refuse him.
  const refused = {
    'the topics of the user': ['view=topics&layout=user&mode=default', [200, 401, 403]],
    'the subscribed categories': ['view=category&layout=user', [401, 403]],
    'the form of a new category': ['view=category&layout=create&parentid=1', [401, 403]],
    'the page to manage a category': ['view=category&layout=manage&catid=2', [401, 403]],
    'the form of a new topic': ['view=topic&layout=create&catid=2', [401, 403]],
    'the form of a reply': ['view=topic&layout=reply&catid=2&id=1', [401, 403]],
    'the drafts': ['view=topic&layout=drafts', [401, 403, 404]],
    'the page to edit the profile': ['view=user&layout=edit', [401, 403]],
    'the form of a new announcement': ['view=announcement&layout=create', [401, 403]],
  };

  Object.entries(refused).forEach(([name, [query, statuses]]) => {
    it(`does not give ${name} to a guest`, () => {
      cy.request({ url: `${base}&${query}`, failOnStatusCode: false }).then((response) => {
        expect(response.status).to.be.oneOf(statuses);
        assertNoPhpError(response.body);
      });
    });
  });

  it('opens a category and a topic of it', () => {
    cy.visit(`${base}&view=category&layout=list`);
    cy.get('tr[id^="category"] h3 a').first().click();

    cy.get('body').should('not.contain.text', 'Fatal error');
    cy.get('#categoryactions .category-item a.topictitle').first().click();

    cy.get('.kunena-topic-item > h1').should('be.visible');
    cy.get('.topic-item-messages').should('be.visible');
    cy.document().then((doc) => assertNoPhpError(doc.body.innerText));
  });

  it('opens the profile of a user from the list of the users', () => {
    cy.visit(`${base}&view=user&layout=list`);
    // A link to a profile: /user/46-name (SEF) or &userid=46.
    cy.get('a').filter((index, link) => /\/user\/\d+|userid=\d+/.test(link.getAttribute('href') || '')).first().click();

    cy.get('body').should('contain.html', 'kunena');
    cy.document().then((doc) => assertNoPhpError(doc.body.innerText));
  });

  it('gives the recent topics as a feed', () => {
    cy.visit(`${base}&view=category&layout=list`);

    // The address of the feed is the one which the page gives (SEF or not).
    cy.get('link[rel="alternate"][type="application/rss+xml"]').first().invoke('attr', 'href').then((href) => {
      // The address has the path of the site in it: it is made a complete address, so the base of Cypress is not added again.
      cy.location('origin').then((origin) => cy.request({ url: new URL(href, origin).href, failOnStatusCode: false })).then((response) => {
        expect(response.status).to.eq(200);
        expect(response.headers['content-type']).to.match(/xml/);
        expect(response.body).to.include('<rss');
      });
    });
  });

  it('says a page does not exist for a view which is not there', () => {
    cy.request({ url: `${base}&view=nothinghere`, failOnStatusCode: false }).then((response) => {
      expect(response.status).to.be.oneOf([200, 404]);
      // The debug bar of Joomla can list the (handled) error of the view: only a real crash of PHP is a failure here.
      expect(response.body).not.to.include('Fatal error');
      expect(response.body).not.to.include('Parse error');
    });
  });
});
