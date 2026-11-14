// The options which close the forum: "only for registered users" (a visitor sees no topic anywhere), "the profiles are private" (a
// visitor sees no profile and no list of users), and "the forum is offline" (only the administrator gets in). The words of a topic of
// the test site must not be in any answer for the visitor.
describe('Kunena options: who may come in', () => {
  const base = '/index.php?option=com_kunena';
  const topicWords = 'Which template do you use';
  const old = {};

  const pages = [
    `${base}&view=home`,
    `${base}&view=category&layout=list`,
    `${base}&view=category&catid=3`,
    `${base}&view=topic&catid=3&id=9`,
    `${base}&view=topics`,
    `${base}&view=topics&format=feed&layout=feed&type=rss`,
    `${base}&view=category&catid=3&format=feed&layout=feed&type=rss`,
    `${base}&view=search&task=results&query=template&searchdate=all`,
    `${base}&view=statistics`,
    `${base}&view=topic&catid=3&id=9&layout=reply`,
  ];

  const sweep = (urls, words) => {
    const leaks = [];

    urls.forEach((url) => {
      cy.request({ url, failOnStatusCode: false }).then((response) => {
        if (words.some((word) => String(response.body).includes(word))) {
          leaks.push(`${url.replace(base, '')} (${response.status})`);
        }
      });
    });

    return cy.then(() => leaks);
  };

  const setOption = (name, value) => cy.kunena_setOption(name, value).then((previous) => {
    if (!(name in old)) {
      old[name] = previous;
    }
  });

  afterEach(() => {
    Object.entries(old).forEach(([name, value]) => cy.kunena_setOption(name, value ?? ''));
    cy.then(() => Object.keys(old).forEach((name) => delete old[name]));
  });

  it('gives nothing of the forum to a visitor when it is only for registered users', () => {
    setOption('regOnly', '1');
    sweep(pages, [topicWords]).then((leaks) => expect(leaks, 'the pages which show a topic').to.deep.eq([]));

    // A user gets the topic as always.
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(`${base}&view=topic&catid=3&id=9`).its('body').should('include', topicWords);
  });

  it('shows the login form to a visitor when the forum is only for registered users, and logs in back to the page', () => {
    setOption('regOnly', '1');

    cy.request({ url: `${base}&view=category&catid=3`, failOnStatusCode: false }).then((response) => {
      const body = String(response.body);

      expect(response.status, 'the status for a visitor').to.eq(403);
      expect(body, 'the message').to.include('Please Log In');
      expect(body, 'the message').to.include('Only community members can participate in this forum');
      expect(body, 'the user name field').to.include('id="klogin-username-full"');
      expect(body, 'the password field').to.include('id="klogin-passwd-full"');
      expect(body, 'the topics of the category').to.not.include(topicWords);
    });

    // The feeds get no form.
    cy.request({ url: `${base}&view=topics&format=feed&layout=feed&type=rss`, failOnStatusCode: false }).then((response) => {
      expect(response.status, 'the status of the feed').to.be.gte(400);
      expect(String(response.body), 'the feed').to.not.include('klogin-username-full');
    });

    // A wrong password comes back to the same page with the form.
    cy.visit(`${base}&view=category&catid=3`, { failOnStatusCode: false });
    cy.get('#klogin-username-full').type('ci-user');
    cy.get('#klogin-passwd-full').type('not-the-password');
    cy.get('.kloginform form').submit();
    cy.get('#klogin-username-full').should('exist');
    cy.contains('Please Log In').should('exist');

    cy.get('#klogin-username-full').clear().type('ci-user');
    cy.get('#klogin-passwd-full').type('ci-user-17082005-x', { log: false });
    cy.get('.kloginform form').submit();

    cy.location('href').should('include', 'catid=3');
    cy.get('#klogin-username-full').should('not.exist');
    cy.contains('Please Log In').should('not.exist');
    cy.contains(topicWords).should('exist');
  });

  it('shows no profile to a visitor when the profiles are private', () => {
    setOption('pubProfile', '0');
    cy.request({ url: `${base}&view=user&layout=default&userid=798`, failOnStatusCode: false }).its('status').should('be.gte', 400);

    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(`${base}&view=user&layout=default&userid=797`).its('body').should('include', 'ci-admin');
  });

  it('shows no list of users to a visitor when it is not allowed', () => {
    setOption('userlistAllowed', '0');
    cy.request({ url: `${base}&view=user&layout=list`, failOnStatusCode: false }).then((response) => {
      expect(response.status, 'the status of the list for a visitor').to.be.gte(400);
      expect(String(response.body), 'the users in the answer').to.not.include('ci-admin');
    });

    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(`${base}&view=user&layout=list`).its('body').should('include', 'ci-admin');
  });

  it('gives nothing of the forum to a visitor or a user when it is offline, and all to the administrator', () => {
    setOption('boardOffline', '1');
    sweep(pages, [topicWords]).then((leaks) => expect(leaks, 'the pages for a visitor').to.deep.eq([]));

    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    sweep(pages, [topicWords]).then((leaks) => expect(leaks, 'the pages for a user').to.deep.eq([]));

    cy.clearAllCookies();
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.request(`${base}&view=topic&catid=3&id=9`).its('body').should('include', topicWords);
  });
});
