// The backend makes a user a moderator of chosen categories (task users.moderate) and takes it away again (users.unmoderate):
// the role of moderator (#__kunena_user_categories role 1) is set for the category, and removed again.
describe('Kunena backend users: moderators', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const roles = () => cy.task('queryDB', `SELECT category_id FROM #__kunena_user_categories WHERE user_id = ${ids.user} AND role = 1 ORDER BY category_id`).then((rows) => rows.map((row) => Number(row.category_id)));
  const send = (task, body) => cy.request(`${admin}&view=users`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ method: 'POST', url: `${admin}&task=users.${task}`, form: true, body: { 'cid[]': ids.user, ...body, [token]: '1' }, followRedirect: false });
  });

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Moderator', 'ci-moderator-${stamp}', 'ci-moderator-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      ids.user = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts) VALUES (${result.insertId}, 0)`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.user}`);
  });

  it('makes the user a moderator of category 3 and takes it away again', () => {
    cy.doAdministratorLogin();
    cy.then(() => send('moderate', { 'catid[]': 3 }));
    cy.then(() => roles()).should('deep.eq', [3]);
    cy.then(() => send('unmoderate', {}));
    cy.then(() => roles()).should('deep.eq', []);
  });
});
