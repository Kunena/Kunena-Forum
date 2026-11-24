// The page to moderate a message (view=topic&layout=moderate) shows the ban history of its author in a tab. When the author had
// been banned before, the page died with "Call to undefined method Joomla\CMS\User\User::isModerator()": the layout of the history
// got the Joomla user of the session as "me" instead of the forum user.
describe('Kunena moderate page: the ban history of the author', () => {
  const stamp = Date.now();
  const user = { id: 798 };
  const message = { id: 4, topic: 4, catid: 3 };
  const reason = `Cypress old ban ${stamp}`;

  beforeEach(() => {
    // A ban of the past (it has ended): the author is not banned now, the history has one entry.
    cy.task('queryDB', `INSERT INTO #__kunena_users_banned (userid, ip, blocked, expiration, created_by, created_time, reason_private, reason_public, comments, params) VALUES (${user.id}, NULL, 0, DATE_SUB(NOW(), INTERVAL 1 DAY), 797, DATE_SUB(NOW(), INTERVAL 2 DAY), '${reason}', '', '[]', '')`);
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_users_banned WHERE reason_private = '${reason}'`);
  });

  it('shows the page with the history of the bans', () => {
    cy.request({ url: `/index.php?option=com_kunena&view=topic&layout=moderate&catid=${message.catid}&id=${message.topic}&mesid=${message.id}`, failOnStatusCode: false }).then((page) => {
      expect(page.status, 'the status of the page').to.eq(200);
      expect(String(page.body), 'the private reason, for a moderator').to.include(reason);
      expect(String(page.body)).not.to.include('Call to undefined method');
    });
  });
});
