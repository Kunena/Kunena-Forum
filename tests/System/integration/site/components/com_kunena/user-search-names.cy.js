// The autocomplete of the user search (task user.getusersmentionssearch, the search box of the user list and the author field of
// the search) gives the names which the forum shows. When the forum shows usernames (the default), the real names of the users
// (the "name" of Joomla, often a full name) stay hidden: the autocomplete must not give them.
describe('Kunena user search: no real names when the forum shows usernames', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const realName = `Secret Realname ${stamp}`;
  let second;
  let oldName;

  const search = (text) => cy.request({ url: `${base}&view=user&task=getusersmentionssearch&format=json&usersearch=${encodeURIComponent(text)}`, failOnStatusCode: false }).its('body');

  beforeEach(() => {
    cy.kunena_setOption('username', '1').then((previous) => {
      oldName = previous;
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT '${realName}', 'ci-realname-${stamp}', 'ci-realname@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      second = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${second}, 2)`);
    });
  });

  afterEach(() => {
    cy.then(() => cy.kunena_setOption('username', oldName === undefined ? null : oldName));
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${second}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${second}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${second}`);
  });

  it('gives the username and not the real name of a user', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    search(`ci-realname-${stamp}`).then((body) => {
      const text = JSON.stringify(body);

      expect(text, 'the user is found').to.include(`ci-realname-${stamp}`);
      expect(text, 'the real name').to.not.include(realName);
    });
  });
});
