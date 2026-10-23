// The ban of a user with the form of the moderator: a ban without an end, the end of a ban, a ban which is lifted, a date which is
// no date, and the rules that nobody bans himself and that a user without the right to ban does not.
describe('Kunena moderation: the ban of a user', () => {
  const base = '/index.php?option=com_kunena';
  const target = 798;
  const admin = 797;
  let oldBanned;

  const bannedOf = (id) => cy.task('queryDB', `SELECT CAST(banned AS CHAR) AS banned FROM #__kunena_users WHERE userid = ${id}`).then((rows) => rows[0].banned);
  const bansOf = (id) => cy.task('queryDB', `SELECT id, blocked, CAST(expiration AS CHAR) AS expiration, reason_public FROM #__kunena_users_banned WHERE userid = ${id} ORDER BY id`);

  const send = (fields) => cy.request(`${base}&view=user&userid=${fields.userid}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({ method: 'POST', url: `${base}&view=user&task=ban`, form: true, body: { ...fields, [token]: '1' }, failOnStatusCode: false, followRedirect: false });
  });

  beforeEach(() => {
    cy.task('queryDB', `SELECT CAST(banned AS CHAR) AS banned FROM #__kunena_users WHERE userid = ${target}`).then((rows) => {
      oldBanned = rows[0].banned;
    });
    cy.task('queryDB', `DELETE FROM #__kunena_users_banned WHERE userid IN (${target}, ${admin})`);
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_users_banned WHERE userid IN (${target}, ${admin})`);
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET banned = '${oldBanned}' WHERE userid = ${target}`));
    cy.task('queryDB', `UPDATE #__kunena_users SET banned = '1000-01-01 00:00:00' WHERE userid = ${admin}`);
  });

  it('bans a user without an end, and lifts the ban', () => {
    send({ userid: target, banlevel: 0, expiration: '', reason_public: 'A public reason', reason_private: 'A private reason', comment: 'A comment' });
    bansOf(target).then((rows) => {
      expect(rows, 'the ban is stored').to.have.length(1);
      expect(rows[0].reason_public).to.eq('A public reason');
    });
    bannedOf(target).then((banned) => expect(banned, 'the user is banned').to.not.eq('1000-01-01 00:00:00'));

    send({ userid: target, banlevel: 0, delban: 1, comment: 'Lifted' });
    cy.task('queryDB', `SELECT CAST(banned AS CHAR) AS banned FROM #__kunena_users WHERE userid = ${target}`).then((rows) => {
      const banned = rows[0].banned;

      // The ban has ended: an old date, or the date which means "no ban".
      expect(banned === '1000-01-01 00:00:00' || new Date(banned.replace(' ', 'T') + 'Z').getTime() <= Date.now() + 5000, `the ban is lifted (${banned})`).to.eq(true);
    });
  });

  it('does not let a user ban himself', () => {
    send({ userid: admin, banlevel: 0, expiration: '', reason_public: 'x' });
    bansOf(admin).then((rows) => expect(rows, 'a ban of the administrator by himself').to.have.length(0));
  });

  it('does not break on a date which is no date or a number of days which is too big', () => {
    send({ userid: target, banlevel: 0, expiration: 'not a date', reason_public: 'x' }).its('status').should('be.lessThan', 500);
    send({ userid: target, banlevel: 0, expiration: '', ban_days: 99999999, reason_public: 'x' }).its('status').should('be.lessThan', 500);
    send({ userid: target, banlevel: 99, expiration: '2001-01-01 00:00:00', reason_public: 'x' }).its('status').should('be.lessThan', 500);
  });

  it('does not let a user without the right to ban do it', () => {
    cy.clearAllCookies();
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    send({ userid: admin, banlevel: 0, expiration: '', reason_public: 'x' });
    bansOf(admin).then((rows) => expect(rows, 'a ban by a user').to.have.length(0));
    bannedOf(admin).then((banned) => expect(banned).to.eq('1000-01-01 00:00:00'));
  });
});
