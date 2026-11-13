// The backend makes a user a moderator of chosen categories (task users.moderate) and takes it away again (users.unmoderate):
// the role of moderator (#__kunena_user_categories role 1) is set for the category, and removed again. Without categories,
// users.moderate grants only the global moderator role (category 0). The moderator icon of the user list sends
// users.unmoderate for any moderator and users.moderate for a user who moderates nothing.
describe('Kunena backend users: moderators', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const roles = () => cy.task('queryDB', `SELECT category_id FROM #__kunena_user_categories WHERE user_id = ${ids.user} AND role = 1 ORDER BY category_id`).then((rows) => rows.map((row) => Number(row.category_id)));
  const flag = () => cy.task('queryDB', `SELECT moderator FROM #__kunena_users WHERE userid = ${ids.user}`).then((rows) => Number(rows[0].moderator));
  const send = (task, body, followRedirect = false) => cy.request(`${admin}&view=users`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ method: 'POST', url: `${admin}&task=users.${task}`, form: true, body: { 'cid[]': ids.user, ...body, [token]: '1' }, followRedirect });
  });
  // Gives the user the moderator role on the category (0 is the global role), as the moderator flag of the user.
  const addRole = (catid) => {
    cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${ids.user}, ${catid}, 1, 0, 0, '')`);
    cy.task('queryDB', `UPDATE #__kunena_users SET moderator = 1 WHERE userid = ${ids.user}`);
  };
  // The moderator icon of the row of the user on the list, filtered on that user.
  const icon = () => cy.get('a.js-grid-item-action[aria-labelledby="cbmod0-desc"]');

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

  it('makes a user without a role a global moderator when no category is given', () => {
    cy.doAdministratorLogin();
    cy.then(() => send('moderate', {}, true)).its('body').should('contain', 'The user is now a global moderator.');
    cy.then(() => roles()).should('deep.eq', [0]);
    cy.then(() => flag()).should('eq', 1);
  });

  it('takes all rights from a category moderator without making them a global moderator', () => {
    cy.then(() => addRole(3));
    cy.doAdministratorLogin();
    cy.then(() => send('unmoderate', {}));
    cy.then(() => roles()).should('deep.eq', []);
    cy.then(() => flag()).should('eq', 0);
  });

  it('takes the global role from a global moderator', () => {
    cy.then(() => addRole(0));
    cy.doAdministratorLogin();
    cy.then(() => send('unmoderate', {}));
    cy.then(() => roles()).should('deep.eq', []);
    cy.then(() => flag()).should('eq', 0);
  });

  it('removes the rights of a category moderator with the icon of the list', () => {
    cy.then(() => addRole(3));
    cy.doAdministratorLogin();
    cy.then(() => cy.visit(`${admin}&view=users&filter_search=id:${ids.user}`));
    icon().should('have.attr', 'data-item-task', 'users.unmoderate');
    cy.get('#cbmod0-desc').invoke('text').should('eq', 'Remove moderator rights');
    icon().click();
    cy.get('#system-message-container').should('contain', 'The moderator rights of the user have been removed.');
    cy.then(() => roles()).should('deep.eq', []);
    cy.then(() => flag()).should('eq', 0);
  });

  it('makes a user without a role a global moderator with the icon of the list', () => {
    cy.doAdministratorLogin();
    cy.then(() => cy.visit(`${admin}&view=users&filter_search=id:${ids.user}`));
    icon().should('have.attr', 'data-item-task', 'users.moderate');
    cy.get('#cbmod0-desc').invoke('text').should('eq', 'Set as global moderator');
    icon().click();
    cy.get('#system-message-container').should('contain', 'The user is now a global moderator.');
    cy.then(() => roles()).should('deep.eq', [0]);
    cy.then(() => flag()).should('eq', 1);
  });
});
