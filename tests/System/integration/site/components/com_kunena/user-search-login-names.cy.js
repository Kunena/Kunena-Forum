// The search of the users of the search form (getusersmentionssearch) answered with the usernames, the names with which the users
// log in, also when the forum shows the real names and keeps the usernames for itself. It listed the super users too when the
// forum hides them from the list of the users.
describe('Kunena user search: no usernames when the forum shows the real names', () => {
  const url = '/index.php?option=com_kunena&view=user&task=getusersmentionssearch&format=json&usersearch=ci-';
  const stamp = Date.now();
  const realName = `Zq Real ${stamp}`;
  const old = {};

  before(() => {
    cy.kunena_setOption('username', '0').then((value) => {
      old.username = value ?? null;
    });
    cy.kunena_setOption('superAdminUserlist', '1').then((value) => {
      old.superAdminUserlist = value ?? null;
    });
    cy.task('queryDB', 'SELECT name FROM #__users WHERE id = 798').then((rows) => {
      old.name = rows[0].name;
    });
    cy.task('queryDB', `UPDATE #__users SET name = '${realName}' WHERE id = 798`);
  });

  after(() => {
    cy.kunena_setOption('username', old.username);
    cy.kunena_setOption('superAdminUserlist', old.superAdminUserlist);
    cy.then(() => cy.task('queryDB', `UPDATE #__users SET name = '${old.name}' WHERE id = 798`));
  });

  it('answers with the names which the forum shows, without the super users', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(url).then((response) => {
      const body = JSON.stringify(response.body);

      expect(body, 'the real name of the user').to.contain(realName);
      expect(body, 'the username of the user').not.to.contain('"ci-user"');
      expect(body, 'a super user').not.to.contain('ci-admin');
    });
  });
});
