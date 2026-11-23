// A member with a ban for life who opens a page which the ban refuses (the reply form) gets the page of the ban. Its text gave the end
// of the ban, and the end of a ban for life is the 31st of December 9999: "You are banned until 31 December 9999". The warning on
// every page tells a ban for life apart already.
describe('Kunena bans: the page of a refused action for a ban for life', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const old = {};

  beforeEach(() => {
    cy.task('queryDB', `SELECT CAST(banned AS CHAR) AS banned FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      old.banned = rows[0].banned;
    });
    cy.task('queryDB', `DELETE FROM #__kunena_users_banned WHERE userid = ${user.id}`);
    cy.task('queryDB', `INSERT INTO #__kunena_users_banned (userid, ip, blocked, expiration, created_by, created_time, reason_private, reason_public, modified_by, modified_time, comments, params) VALUES (${user.id}, NULL, 0, '9999-12-31 23:59:59', 797, NOW(), 'Cypress', 'Cypress', NULL, NULL, '', '')`);
    cy.task('queryDB', `UPDATE #__kunena_users SET banned = '9999-12-31 23:59:59' WHERE userid = ${user.id}`);
    cy.task('queryDB', 'SELECT id, category_id FROM #__kunena_topics WHERE hold = 0 AND moved_id = 0 AND locked = 0 ORDER BY id LIMIT 1').then((rows) => {
      old.topic = rows[0];
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_users_banned WHERE userid = ${user.id}`);
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET banned = '${old.banned}' WHERE userid = ${user.id}`));
  });

  it('does not give the year 9999 as the end of the ban', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => cy.request({ url: `${base}&view=topic&layout=reply&catid=${old.topic.category_id}&id=${old.topic.id}`, failOnStatusCode: false })).then((page) => {
      expect(page.status, 'the reply is refused').to.eq(403);
      expect(page.body, 'the end of the ban').to.not.include('9999');
    });
  });
});
