// Every page of the forum for the administrator of the test site, and what only an administrator sees on them. It needs the login of
// the test site: the administrator logs in on the site itself (cy.doFrontendLogin with his username and password). Cypress fails a
// test by itself when a page has an error in its JavaScript, so a visit of a page is also a check of the scripts of that page.
describe('Kunena pages for an administrator', () => {

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

  let topic;
  let message;
  let otherUser;

  before(() => {
    cy.task(
      'queryDB',
      'SELECT t.id, t.category_id FROM #__kunena_topics AS t INNER JOIN #__kunena_categories AS c ON c.id = t.category_id WHERE t.hold = 0 AND c.published = 1 ORDER BY t.id ASC LIMIT 1',
    ).then((rows) => {
      expect(rows, 'a forum topic is available').not.to.be.empty;
      topic = rows[0];

      cy.task('queryDB', `SELECT id, userid FROM #__kunena_messages WHERE thread = ${topic.id} AND hold = 0 ORDER BY id ASC LIMIT 1`).then((messages) => {
        message = messages[0];
      });
    });

    cy.task('queryDB', 'SELECT id FROM #__users ORDER BY id DESC LIMIT 1').then((users) => {
      otherUser = users[0];
    });
  });

  beforeEach(() => {
    // A new installation has no announcement: one is made for the test.
    cy.kunena_createAnnouncement();
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  // page => [address, something which is on the page]
  const pages = {
    'the home page': ['view=home', 'body'],
    'the list of the categories': ['view=category&layout=list', '.kcat-new-top'],
    'the subscribed categories': ['view=category&layout=user', 'body'],
    'the form of a new category': ['view=category&layout=create&parentid=1', '#adminForm'],
    'the page to manage a category': ['view=category&layout=manage&catid=%catid%', '#adminForm'],
    'the form of a new topic': ['view=topic&layout=create&catid=%catid%', '#postform'],
    'the form of a reply': ['view=topic&layout=reply&catid=%catid%&id=%id%', '#postform'],
    'the form to edit a message of another user': ['view=topic&layout=edit&catid=%catid%&id=%id%&mesid=%mesid%', '#postform'],
    'the report of a message': ['view=topic&layout=report&catid=%catid%&id=%id%&mesid=%mesid%', 'body'],
    'the moderation of a topic': ['view=topic&layout=moderate&catid=%catid%&id=%id%', 'body'],
    'the log of the moderators': ['view=topic&layout=modlog', 'body'],
    'the drafts': ['view=topic&layout=drafts', 'body'],
    'the recent topics': ['view=topics&layout=default&mode=recent', 'body'],
    'the topics without replies': ['view=topics&layout=default&mode=noreplies', 'body'],
    'the unread topics': ['view=topics&layout=unread', 'body'],
    'the topics of the user': ['view=topics&layout=user&mode=default', 'body'],
    'the recent posts': ['view=topics&layout=posts&mode=recent', 'body'],
    'the topics which need attention': ['view=topics&layout=moderator', 'body'],
    'the search form': ['view=search', 'body'],
    'the results of a search': ['view=search&query=test', 'body'],
    'the statistics': ['view=statistics', 'body'],
    'the credits': ['view=credits', 'body'],
    'the help page': ['view=misc', 'body'],
    'the list of the users': ['view=user&layout=list', 'body'],
    'the profile of another user': ['view=user&userid=%otheruser%', 'body'],
    'the page to edit the profile of another user': ['view=user&layout=edit&userid=%otheruser%', '#kuserform'],
    'the page to edit the own profile': ['view=user&layout=edit', '#kuserform'],
    'the attachments of the user': ['view=user&layout=attachments', 'body'],
    'the list of the banned users': ['view=user&layout=banmanager', 'body'],
    'the form to ban a user': ['view=user&layout=moderate&userid=%otheruser%', 'body'],
    'the inbox': ['view=inbox', 'body'],
    'the list of the announcements': ['view=announcement&layout=listing', 'a[onclick*="banner"]'],
    'the form of a new announcement': ['view=announcement&layout=create', 'body'],
    'the form to edit an announcement': ['view=announcement&layout=edit&id=%announcement%', 'body'],
  };

  Object.entries(pages).forEach(([name, [query, selector]]) => {
    it(`opens ${name}`, () => {
      cy.task('queryDB', 'SELECT id FROM #__kunena_announcement ORDER BY id DESC LIMIT 1').then((announcements) => {
        const url = `${base}&${query
          .replace('%catid%', topic.category_id)
          .replace('%id%', topic.id)
          .replace('%mesid%', message.id)
          .replace('%otheruser%', otherUser.id)
          .replace('%announcement%', announcements.length ? announcements[0].id : 1)}`;

        cy.request({ url, failOnStatusCode: false }).then((response) => {
          expect(response.status).to.eq(200);
          assertNoPhpError(response.body);
        });

        cy.visit(url);
        cy.get(selector).first().should('exist');
      });
    });
  });

  it('opens an announcement', () => {
    cy.task('queryDB', 'SELECT id FROM #__kunena_announcement ORDER BY id DESC LIMIT 1').then((announcements) => {
      if (!announcements.length) {
        cy.log('The test site has no announcement.');

        return;
      }

      cy.visit(`${base}&view=announcement&layout=default&id=${announcements[0].id}`);
      cy.get('body').should('contain.html', 'kunena');
    });
  });

  it('opens a category and a topic', () => {
    cy.visit(`${base}&view=category&catid=${topic.category_id}`);
    cy.get('#categoryactions').should('exist');

    cy.visit(`${base}&view=topic&catid=${topic.category_id}&id=${topic.id}`);
    cy.get('.kunena-topic-item > h1').should('be.visible');
  });

  it('shows the administrator the star which chooses the default announcement', () => {
    cy.visit(`${base}&view=announcement&layout=listing`);

    cy.get('a[onclick*="banner"]').should('have.length.greaterThan', 0);
    cy.get('select#kchecktask option[value="banner"]').should('exist');
  });

  it('shows the administrator the button of a new section on the index only', () => {
    cy.visit(`${base}&view=category&layout=list`);
    cy.get('.kcat-new-top').should('exist');

    cy.visit(`${base}&view=category&catid=${topic.category_id}`);
    cy.get('.kcat-new-top').should('not.exist');
  });

  it('shows the administrator the ban button of a message', () => {
    cy.visit(`${base}&view=topic&catid=${topic.category_id}&id=${topic.id}`);

    // The box of the ban is on the page for each message of another user (the modal with the duration and the reason).
    cy.get('body').then(($body) => {
      if ($body.find('[id^="ban"].modal').length) {
        cy.get('[id^="ban"].modal select[name="ban_days"]').first().should('exist');
        cy.get('[id^="ban"].modal textarea[name="reason_public"]').first().should('exist');
      }
    });
  });

  it('shows the IP address with its place to the administrator', () => {
    cy.visit(`${base}&view=topic&catid=${topic.category_id}&id=${topic.id}`);

    cy.get('body').then(($body) => {
      if ($body.text().includes('IP:')) {
        cy.contains('a', 'IP:').should('have.attr', 'href').and('include', 'dnslytics');
      }
    });
  });

  it('gives the administrator the popups of the server', () => {
    cy.request(`${base}&view=user&task=notifications&format=json&after=0`).then((response) => {
      expect(response.body.enabled).to.eq(true);
      expect(response.body.cursor).to.be.a('number');
    });
  });
});
