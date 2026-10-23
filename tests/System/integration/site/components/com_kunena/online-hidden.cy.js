// A user who hides his online status (showOnline = 0) is not in the list of the users who are online for a visitor; a
// moderator sees him (in the list of the hidden users). A user who does not hide it is listed for everybody.
describe('Kunena who is online: a user who hides his status', () => {
  const base = '/index.php?option=com_kunena';
  const user = { id: 798, name: 'ci-user' };
  const session = `cypresshidden${Date.now()}`.slice(0, 32);
  let old;

  // The users of the widget "who is online" at the bottom of the list of the categories.
  const onlinePart = () => cy.request(`${base}&view=category&layout=list`).its('body').then((body) => {
    const page = String(body);
    const start = page.indexOf('id="kwho"');

    return start > -1 ? page.slice(start, start + 6000) : '';
  });

  before(() => {
    cy.task('queryDB', `SELECT showOnline FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      old = rows[0].showOnline;
    });
  });

  beforeEach(() => {
    cy.task('queryDB', `INSERT INTO #__session (session_id, client_id, guest, time, data, userid, username) VALUES ('${session}', 0, 0, UNIX_TIMESTAMP(), '', ${user.id}, '${user.name}')`);
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__session WHERE session_id = '${session}'`);
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET showOnline = ${old === null || old === undefined ? 1 : Number(old)} WHERE userid = ${user.id}`));
  });

  it('lists a user who shows his status for a visitor', () => {
    cy.task('queryDB', `UPDATE #__kunena_users SET showOnline = 1 WHERE userid = ${user.id}`);
    onlinePart().should('include', `>${user.name}<`);
  });

  it('does not list a user who hides his status for a visitor, and lists him for a moderator', () => {
    cy.task('queryDB', `UPDATE #__kunena_users SET showOnline = 0 WHERE userid = ${user.id}`);
    onlinePart().should('not.include', `>${user.name}<`);

    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    onlinePart().should('include', user.name);
  });
});
