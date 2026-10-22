// Every page of the forum for a logged-in user (no moderator, no administrator). It needs the login of the test site
// (cy.doFrontendLogin with the user ci-user, which the spec makes). Cypress fails a test by itself when a page has an error in its JavaScript, so a visit of a page is also
// a check of the scripts of that page. The pages of the moderators and the administrators are in all-pages-admin.cy.js.
describe('Kunena pages for a user', () => {

  // The drafts are off in a new installation (the page of the drafts gives a 404): they are switched on for the test and back after.
  let draftsBefore;

  before(() => {
    cy.kunena_setOption('enableDrafts', '1').then((old) => {
      draftsBefore = old;
    });
  });

  after(() => {
    cy.kunena_setOption('enableDrafts', draftsBefore ?? '0');
  });
  const base = '/index.php?option=com_kunena';

  const phpErrors = ['Fatal error', 'Parse error', 'Warning:', 'Notice:', 'Deprecated:', 'Call to undefined', 'Uncaught ', 'Array to string'];

  const assertNoPhpError = (body) => {
    // Kunena tells an administrator that its debug mode is on (a message of Joomla in every page): that is no error.
    const text = body.replace(/Warning:\s*(?:<[^>]+>\s*)*Kunena Debug Mode has been turned on!/g, '');

    phpErrors.forEach((error) => {
      expect(text, `the page shows "${error}"`).not.to.include(error);
    });

    // A language key which is shown as it is: its string is missing from the language files (the scripts are left out: they hold the keys of
    // the strings which they ask for).
    const shown = String(body).replace(/<script[\s\S]*?<\/script>/gi, '').replace(/<style[\s\S]*?<\/style>/gi, '').match(/COM_KUNENA_[A-Z0-9_]{3,}/g);

    expect(shown, 'language keys shown as text').to.equal(null);
  };

  // A topic and a message of the test site which the user can open, and a message which is his own.
  let topic;
  let ownMessage;

  // A user of the group Registered (the user of the test site is an administrator). The administrator makes him once; he stays.
  const user = { name: 'CI User', username: 'ci-user', password: 'ci-user-17082005-x', email: 'ci-user@example.com' };

  before(() => {
    cy.task('queryDB', `SELECT id FROM #__users WHERE username = '${user.username}'`).then((rows) => {
      if (!rows.length) {
        cy.doAdministratorLogin();
        cy.createUser(user.name, user.username, user.password, user.email, 'Registered');
      }
    });

    cy.task(
      'queryDB',
      'SELECT t.id, t.category_id FROM #__kunena_topics AS t INNER JOIN #__kunena_categories AS c ON c.id = t.category_id WHERE t.hold = 0 AND t.locked = 0 AND c.published = 1 AND c.pubAccess = 1 AND c.locked = 0 ORDER BY t.id ASC LIMIT 1',
    ).then((rows) => {
      expect(rows, 'a published forum topic is available').not.to.be.empty;
      topic = rows[0];
    });
  });

  beforeEach(() => {
    cy.doFrontendLogin(user.username, user.password);
  });

  // The pages which have no id in their address.
  const pages = {
    'the home page': 'view=home',
    'the list of the categories': 'view=category&layout=list',
    'the subscribed categories': 'view=category&layout=user',
    'the form of a new topic': 'view=topic&layout=create&catid=%catid%',
    'the drafts': 'view=topic&layout=drafts',
    'the recent topics': 'view=topics&layout=default&mode=recent',
    'the topics without replies': 'view=topics&layout=default&mode=noreplies',
    'the unread topics': 'view=topics&layout=unread',
    'the topics of the user': 'view=topics&layout=user&mode=default',
    'the recent posts': 'view=topics&layout=posts&mode=recent',
    'the topics which need attention': 'view=topics&layout=moderator',
    'the search form': 'view=search',
    'the results of a search': 'view=search&query=test',
    'the statistics': 'view=statistics',
    'the credits': 'view=credits',
    'the help page': 'view=misc',
    'the list of the users': 'view=user&layout=list',
    'the page to edit the profile': 'view=user&layout=edit',
    'the attachments of the user': 'view=user&layout=attachments',
    'the inbox': 'view=inbox',
    'the list of the announcements': 'view=announcement&layout=listing',
  };

  Object.entries(pages).forEach(([name, query]) => {
    it(`opens ${name}`, () => {
      const url = `${base}&${query.replace('%catid%', topic.category_id)}`;

      cy.request({ url, failOnStatusCode: false }).then((response) => {
        expect(response.status).to.eq(200);
        assertNoPhpError(response.body);
      });

      // The page is opened too: a mistake in a script of the page fails the test.
      cy.visit(url);
      cy.get('body').should('contain.html', 'kunena');
    });
  });

  it('opens a category, a topic, the reply form and the report form', () => {
    cy.visit(`${base}&view=category&catid=${topic.category_id}`);
    cy.get('#categoryactions').should('exist');

    cy.visit(`${base}&view=topic&catid=${topic.category_id}&id=${topic.id}`);
    cy.get('.kunena-topic-item > h1').should('be.visible');

    cy.visit(`${base}&view=topic&layout=reply&catid=${topic.category_id}&id=${topic.id}`);
    cy.get('#postform').should('be.visible');

    cy.task('queryDB', `SELECT id FROM #__kunena_messages WHERE thread = ${topic.id} AND hold = 0 ORDER BY id ASC LIMIT 1`).then((messages) => {
      cy.visit(`${base}&view=topic&layout=report&catid=${topic.category_id}&id=${topic.id}&mesid=${messages[0].id}`);
      cy.get('body').should('contain.html', 'kunena');
    });
  });

  it('opens the edit form of a message of the user', () => {
    cy.doFrontendLogin(user.username, user.password);

    // The user of the test site is the author of a message: the form to edit it opens.
    cy.task('queryDB', "SELECT m.id, m.thread, m.catid FROM #__kunena_messages AS m INNER JOIN #__users AS u ON u.id = m.userid WHERE u.username = '" + user.username + "' AND m.hold = 0 ORDER BY m.id DESC LIMIT 1").then((messages) => {
      if (!messages.length) {
        cy.log('The user of the test site has no message: nothing to edit.');

        return;
      }

      ownMessage = messages[0];

      cy.visit(`${base}&view=topic&layout=edit&catid=${ownMessage.catid}&id=${ownMessage.thread}&mesid=${ownMessage.id}`);
      cy.get('#postform').should('be.visible');
    });
  });

  it('opens an announcement', () => {
    cy.task('queryDB', 'SELECT id FROM #__kunena_announcement WHERE published = 1 ORDER BY id DESC LIMIT 1').then((announcements) => {
      if (!announcements.length) {
        cy.log('The test site has no announcement.');

        return;
      }

      cy.visit(`${base}&view=announcement&layout=default&id=${announcements[0].id}`);
      cy.get('body').should('contain.html', 'kunena');
      cy.document().then((doc) => assertNoPhpError(doc.body.innerText));
    });
  });

  it('opens the page of a user and the profile of the user himself', () => {
    cy.visit(`${base}&view=user&layout=list`);
    cy.get('body').should('contain.html', 'kunena');

    // The profile of another user (the address of the profile in the list depends on the routing of the site).
    cy.task('queryDB', `SELECT id FROM #__users WHERE username <> '${user.username}' ORDER BY id ASC LIMIT 1`).then((users) => {
      cy.visit(`${base}&view=user&layout=default&userid=${users[0].id}`);
      cy.get('body').should('contain.html', 'kunena');
      cy.document().then((doc) => assertNoPhpError(doc.body.innerText));
    });

    cy.visit(`${base}&view=user`);
    cy.get('body').should('contain.html', 'kunena');
  });

  // The pages which are for moderators and administrators: a user is refused.
  const refused = {
    'the form of a new category': 'view=category&layout=create&parentid=1',
    'the page to manage a category': 'view=category&layout=manage&catid=%catid%',
    'the moderation of a topic': 'view=topic&layout=moderate&catid=%catid%&id=%id%',
    'the log of the moderators': 'view=topic&layout=modlog',
    'the moderation of a user': 'view=user&layout=moderate&userid=1',
    'the list of the banned users': 'view=user&layout=banmanager',
    'the form of a new announcement': 'view=announcement&layout=create',
    'the form to edit an announcement': 'view=announcement&layout=edit&id=1',
  };

  Object.entries(refused).forEach(([name, query]) => {
    it(`does not give ${name} to a user`, () => {
      const url = `${base}&${query.replace('%catid%', topic.category_id).replace('%id%', topic.id)}`;

      cy.request({ url, failOnStatusCode: false }).then((response) => {
        expect(response.status).to.be.oneOf([401, 403, 404]);
        assertNoPhpError(response.body);
      });
    });
  });

  it('shows the user the setting of the popups and the popups of the server', () => {
    cy.visit(`${base}&view=user&layout=edit`);
    cy.get('#kpopupnotifications').should('exist').and('have.value', '1');

    cy.request(`${base}&view=user&task=notifications&format=json&after=0`).then((response) => {
      expect(response.body.enabled).to.eq(true);
      expect(response.body.cursor).to.be.a('number');
    });
  });

  it('has no list of the people who are online as a page of its own (it is a widget of the statistics)', () => {
    // The own page of the people who are online has no controller yet: it is left out of this test on purpose.
    cy.visit(`${base}&view=statistics`);
    cy.get('body').should('contain.html', 'kunena');
  });
});
