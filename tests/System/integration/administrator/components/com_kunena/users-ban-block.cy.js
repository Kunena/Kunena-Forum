// The ban and block buttons of the backend user list (tasks users.ban, users.unban, users.block, users.unblock): the list
// shows "ban" or "unban" by isBanned() and "block" or "unblock" by isBlocked(), so a banned user that is not blocked gets
// the "block" button. Blocking that user blocks them (the ban stays), and lifting a ban or a block that is not there creates
// no ban.
describe('Kunena backend users: ban and block', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const state = () => cy.task(
    'queryDB',
    `SELECT (SELECT COUNT(*) FROM #__kunena_users_banned WHERE userid = ${ids.user}) AS bans, (SELECT COUNT(*) FROM #__kunena_users_banned WHERE userid = ${ids.user} AND expiration > NOW()) AS active, (SELECT COALESCE(MAX(blocked), 0) FROM #__kunena_users_banned WHERE userid = ${ids.user} AND expiration > NOW()) AS level, (SELECT block FROM #__users WHERE id = ${ids.user}) AS block`,
  ).then((rows) => ({ bans: Number(rows[0].bans), active: Number(rows[0].active), level: Number(rows[0].level), block: Number(rows[0].block) }));
  const send = (task) => cy.request(`${admin}&view=users`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ method: 'POST', url: `${admin}&task=users.${task}`, form: true, body: { 'cid[]': ids.user, [token]: '1' }, followRedirect: false });
  });

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Banned', 'ci-banned-${stamp}', 'ci-banned-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      ids.user = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts) VALUES (${result.insertId}, 0)`);
    });
    cy.doAdministratorLogin();
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_users_banned WHERE userid = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.user}`);
  });

  it('bans the user and lifts the ban again', () => {
    cy.then(() => send('ban'));
    cy.then(() => state()).should('deep.include', { active: 1, level: 0, block: 0 });
    cy.then(() => send('unban'));
    cy.then(() => state()).should('deep.include', { active: 0, block: 0 });
  });

  it('blocks a banned user instead of lifting the ban', () => {
    cy.then(() => send('ban'));
    cy.then(() => send('block'));
    cy.then(() => state()).should('deep.include', { active: 1, level: 1, block: 1 });
    cy.then(() => send('unblock'));
    cy.then(() => state()).should('deep.include', { active: 0, block: 0 });
  });

  it('creates no ban when it lifts a ban or a block that is not there', () => {
    cy.then(() => send('unban'));
    cy.then(() => state()).should('deep.eq', { bans: 0, active: 0, level: 0, block: 0 });
    cy.then(() => send('unblock'));
    cy.then(() => state()).should('deep.eq', { bans: 0, active: 0, level: 0, block: 0 });
  });
});
