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
