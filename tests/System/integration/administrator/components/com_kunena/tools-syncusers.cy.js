// The tool "Synchronise users" of the backend (task tools.syncUsers): with "add" a Joomla user without a forum profile gets one,
// with "delete" a forum profile of a user who is no Joomla user (any more) is removed. The test users and rows are removed again.
describe('Kunena backend tools: synchronise users', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  // A number which is no Joomla user.
  const ghost = 900000000 + (stamp % 99999999);
  const ids = {};
  const profiles = (userid) => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_users WHERE userid = ${userid}`).then((rows) => Number(rows[0].n));

  beforeEach(() => {
    // A Joomla user without a forum profile, and a forum profile without a Joomla user.
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Sync', 'ci-sync-${stamp}', 'ci-sync-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      ids.user = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
    });
    cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts) VALUES (${ghost}, 0)`);
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid IN (${ids.user}, ${ghost})`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.user}`);
  });

  it('adds the missing profile and removes the profile of a user who does not exist', () => {
    cy.then(() => profiles(ids.user)).should('eq', 0);
    cy.doAdministratorLogin();
    cy.request(`${admin}&view=tools&layout=syncusers`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({ method: 'POST', url: `${admin}&task=tools.syncUsers`, form: true, body: { userAdd: 1, userDel: 1, [token]: '1' }, followRedirect: false });
    });
    cy.then(() => profiles(ids.user)).should('eq', 1);
    profiles(ghost).should('eq', 0);
    // The users of the test site keep their profiles.
    cy.task('queryDB', 'SELECT COUNT(*) AS n FROM #__kunena_users WHERE userid IN (796, 797, 798)').its('0.n').should('eq', 3);
  });
});
