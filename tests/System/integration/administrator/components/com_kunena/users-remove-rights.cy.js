// The task users.remove of the backend deletes the account of Joomla of the user, not only his profile of the forum: it needs
// the right of Joomla to delete users (core.delete of com_users), like the users of Joomla. Before, every user of the backend of
// the forum could delete the accounts of the site.
describe('Kunena users: who may delete an account', () => {
  const stamp = Date.now() % 100000;
  const password = 'ci-user-17082005-x';
  const ids = {};
  let usersRules;

  const createUser = (key, group) => cy.task(
    'queryDB',
    `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI ${key}', 'ci-${key}-${stamp}', 'ci-${key}-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{"allowTourAutoStart":"0"}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
  ).then((result) => {
    ids[key] = result.insertId;
    cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, ${group})`);
    cy.task('queryDB', `INSERT INTO #__kunena_users (userid) VALUES (${result.insertId})`);
  });

  before(() => {
    // The group Administrator (7) manages the forum; the deletion of users of Joomla is denied to it for the test.
    cy.task('queryDB', "SELECT rules FROM #__assets WHERE name = 'com_users'").then((rows) => {
      usersRules = rows[0].rules;
      const rules = JSON.parse(usersRules || '{}');

      rules['core.delete'] = { ...(rules['core.delete'] ?? {}), 7: 0 };
      cy.task('queryDB', `UPDATE #__assets SET rules = '${JSON.stringify(rules)}' WHERE name = 'com_users'`);
    });
    createUser('remover', 7);
    createUser('victim', 2);
  });

  after(() => {
    cy.task('queryDB', `UPDATE #__assets SET rules = '${usersRules}' WHERE name = 'com_users'`);
    Object.values(ids).forEach((id) => {
      cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${id}`);
      cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${id}`);
      cy.task('queryDB', `DELETE FROM #__session WHERE userid = ${id}`);
      cy.task('queryDB', `DELETE FROM #__users WHERE id = ${id}`);
    });
  });

  it('does not let a user of the backend of the forum without the right to delete users delete an account', () => {
    cy.doAdministratorLogin(`ci-remover-${stamp}`, password, false);
    cy.request('/administrator/index.php?option=com_kunena&view=users').then((page) => {
      expect(page.body, 'the users of the forum (not the login form)').not.to.contain('mod-login');
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      cy.request({ method: 'POST', url: '/administrator/index.php?option=com_kunena&view=users', form: true, body: { task: 'users.remove', 'cid[]': ids.victim, [token]: '1' } });
    });

    cy.task('queryDB', `SELECT id FROM #__users WHERE id = ${ids.victim}`).should('have.length', 1);
  });
});
