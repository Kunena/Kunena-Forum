// The rules of the karma: a user gives +1 or -1 to another user once in six hours, a moderator is not stopped by the delay, a guest
// gives none, a request without a token gives none, and a user who raises his own karma gets -10.
describe('Kunena karma: the rules', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = { id: 797 };
  const old = {};

  const karmaOf = (id) => cy.task('queryDB', `SELECT karma FROM #__kunena_users WHERE userid = ${id}`).then((rows) => Number(rows[0].karma));

  const give = (task, target, withToken = true) => cy.request(`${base}&view=topic&catid=3&id=9`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1];

    return cy.request({ url: `${base}&view=user&task=${task}&userid=${target}${withToken && token ? `&${token}=1` : ''}`, failOnStatusCode: false, followRedirect: false });
  });

  before(() => {
    cy.kunena_setOption('showKarma', '1').then((value) => {
      old.showKarma = value;
    });
  });

  after(() => {
    cy.kunena_setOption('showKarma', old.showKarma ?? '0');
  });

  beforeEach(() => {
    cy.task('queryDB', 'SELECT userid, karma FROM #__kunena_users WHERE userid IN (797, 798)').then((rows) => {
      old.karma = Object.fromEntries(rows.map((row) => [row.userid, Number(row.karma)]));
    });
    cy.task('queryDB', `DELETE FROM #__kunena_karma WHERE userid IN (${user.id}, ${admin.id})`);
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_karma WHERE userid IN (${user.id}, ${admin.id})`);
    [797, 798].forEach((id) => cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET karma = ${old.karma[id]} WHERE userid = ${id}`)));
  });

  it('gives no karma to a guest, or without a token', () => {
    give('karmaup', admin.id);
    karmaOf(admin.id).should('eq', old.karma[797]);

    cy.doFrontendLogin(user.name, user.password, false);
    give('karmaup', admin.id, false);
    karmaOf(admin.id).should('eq', old.karma[797]);
  });

  it('gives +1 once, and nothing more before the delay has gone', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    give('karmaup', admin.id);
    karmaOf(admin.id).should('eq', old.karma[797] + 1);
    give('karmaup', admin.id);
    give('karmadown', admin.id);
    karmaOf(admin.id).should('eq', old.karma[797] + 1);
  });

  it('takes 10 from a user who raises his own karma', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    give('karmaup', user.id);
    karmaOf(user.id).should('eq', old.karma[798] - 10);
  });

  it('does not stop a moderator with the delay', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    give('karmaup', user.id);
    give('karmaup', user.id);
    karmaOf(user.id).should('eq', old.karma[798] + 2);
  });
});
