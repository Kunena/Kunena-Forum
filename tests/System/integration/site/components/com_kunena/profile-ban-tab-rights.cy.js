// The profile of a member has a tab "New ban" for a moderator. It was there for every moderator on every other profile: a
// moderator who may not ban that member (an administrator, or another moderator when he is no administrator) got the tab with a
// refusal in it. It shows now only when he may ban the member, as the tab of the ban history.
describe('Kunena profile: the tab for a new ban', () => {
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const stamp = Date.now();
  const ids = {};

  beforeEach(() => {
    // ci-user is a global moderator (the row of category 0 of the test site has role 0); a member of its own, whom he may ban.
    cy.task('queryDB', `UPDATE #__kunena_user_categories SET role = 1 WHERE user_id = ${user.id} AND category_id = 0`);
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) VALUES ('CI Bannable', 'ci-ban-${stamp}', 'ci-ban-${stamp}@example.com', 'x', 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '')`,
    ).then((result) => {
      ids.member = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `UPDATE #__kunena_user_categories SET role = 0 WHERE user_id = ${user.id} AND category_id = 0`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.member}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.member}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.member}`);
  });

  it('has no tab on the profile of an administrator, and one on the profile of a member', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request('/index.php?option=com_kunena&view=user&userid=797').its('body').should('not.contain', 'id="banuser-tab"');
    cy.then(() => cy.request(`/index.php?option=com_kunena&view=user&userid=${ids.member}`)).its('body').should('contain', 'id="banuser-tab"');
  });
});
