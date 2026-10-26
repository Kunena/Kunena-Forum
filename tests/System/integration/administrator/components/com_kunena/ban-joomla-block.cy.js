// The ban of the forum and the account of Joomla. Only a block of the forum blocks the account (and lifting it unblocks it):
// a ban of the forum changed the account too, it unblocked an account which an administrator had blocked in Joomla. And the
// account of a super user is only for a super user: Joomla refuses the change of it (no test here).
describe('Kunena bans: the account of Joomla', () => {
  const password = 'ci-user-17082005-x';
  const users = {};
  const stamp = Date.now() % 100000;

  const createUser = (key, group, block) => cy.task(
    'queryDB',
    `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI ${key}', 'ci-${key}-${stamp}', 'ci-${key}-${stamp}@example.com', password, ${block}, 0, NOW(), NULL, '', '{"allowTourAutoStart":"0"}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
  ).then((result) => {
    users[key] = result.insertId;
    cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, ${group})`);
    // The profile of the forum: a ban needs it.
    cy.task('queryDB', `INSERT INTO #__kunena_users (userid) VALUES (${result.insertId})`);
  });

  const blockOf = (key) => cy.task('queryDB', `SELECT block FROM #__users WHERE id = ${users[key]}`).then((rows) => Number(rows[0].block));

  const task = (name, key) => cy.request('/administrator/index.php?option=com_kunena&view=users').then((page) => {
    expect(page.body, 'the users of the forum (not the login form)').not.to.contain('mod-login');
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ method: 'POST', url: '/administrator/index.php?option=com_kunena&view=users', form: true, body: { task: `users.${name}`, 'cid[]': users[key], [token]: '1' } });
  });

  before(() => {
    createUser('blocked', 2, 1);
  });

  after(() => {
    Object.values(users).forEach((id) => {
      cy.task('queryDB', `DELETE FROM #__kunena_users_banned WHERE userid = ${id}`);
      cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${id}`);
      cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${id}`);
      cy.task('queryDB', `DELETE FROM #__session WHERE userid = ${id}`);
      cy.task('queryDB', `DELETE FROM #__users WHERE id = ${id}`);
    });
  });

  it('keeps an account blocked in Joomla blocked when the forum bans the user', () => {
    cy.doAdministratorLogin();
    task('ban', 'blocked');

    cy.task('queryDB', `SELECT blocked FROM #__kunena_users_banned WHERE userid = ${users.blocked}`).should('have.length', 1);
    blockOf('blocked').should('eq', 1);
  });
});
