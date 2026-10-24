// The batch of the backend user list: make the chosen users moderators of the chosen categories, or global moderators
// with category 0 (task users.batchmoderators), and subscribe them to the chosen categories (users.subscribeuserstocategories).
describe('Kunena backend users: batch moderators and subscriptions', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = { users: [] };
  const rows = (where) => cy.task('queryDB', `SELECT user_id, category_id FROM #__kunena_user_categories WHERE user_id IN (${ids.users.join(',')}) AND ${where} ORDER BY user_id, category_id`).then((result) => result.map((row) => `${row.user_id - ids.users[0]}:${row.category_id}`));
  const send = (task, catids) => cy.request(`${admin}&view=users`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ method: 'POST', url: `${admin}&task=users.${task}`, form: true, body: `${ids.users.map((id) => `cid[]=${id}`).join('&')}&${catids.map((id) => `catid[]=${id}`).join('&')}&${token}=1`, headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, followRedirect: false });
  });

  beforeEach(() => {
    ids.users = [];
    ['a', 'b'].forEach((name) => {
      cy.task(
        'queryDB',
        `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Batch ${name}', 'ci-batch-${name}-${stamp}', 'ci-batch-${name}-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
      ).then((result) => {
        ids.users.push(result.insertId);
        cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
        cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts) VALUES (${result.insertId}, 0)`);
      });
    });
    cy.doAdministratorLogin();
  });

  afterEach(() => {
    ids.users.forEach((id) => {
      cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${id}`);
      cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${id}`);
      cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${id}`);
      cy.task('queryDB', `DELETE FROM #__users WHERE id = ${id}`);
    });
  });

  it('makes both users moderators of category 3 and global moderators', () => {
    cy.then(() => send('batchmoderators', [3, 0]));
    cy.then(() => rows('role = 1')).should('deep.eq', ['0:0', '0:3', '1:0', '1:3']);
  });

  it('subscribes both users to categories 3 and 4', () => {
    cy.then(() => send('subscribeuserstocategories', [3, 4]));
    cy.then(() => rows('subscribed = 1')).should('deep.eq', ['0:3', '0:4', '1:3', '1:4']);
  });
});
