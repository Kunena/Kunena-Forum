// The column of the e-mail addresses in the user list (option "Show e-mail in the user list") follows the same rule as the
// profile page: the address of a user who hides his address (hideEmail) is not shown, to a moderator it is.
describe('Kunena user list: the e-mail addresses', () => {
  const base = '/index.php?option=com_kunena&view=user&layout=list';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  // The address ci-user@example.com, as the cloaking of Joomla writes it: <joomla-hidden-mail first="..." last="...">.
  const cloaked = 'first="Y2ktdXNlcg==" last="ZXhhbXBsZS5jb20="';
  const old = {};

  const list = () => cy.request(base).then((response) => String(response.body));

  before(() => {
    cy.kunena_setOption('userlistEmail', '1').then((value) => {
      old.userlistEmail = value;
    });
    cy.kunena_setOption('showEmail', '1').then((value) => {
      old.showEmail = value;
    });
  });

  after(() => {
    cy.kunena_setOption('userlistEmail', old.userlistEmail ?? '0');
    cy.kunena_setOption('showEmail', old.showEmail ?? '0');
  });

  afterEach(() => {
    cy.task('queryDB', `UPDATE #__kunena_users SET hideEmail = 0 WHERE userid = ${user.id}`);
  });

  it('shows the address of a user who does not hide it, and not of one who hides it', () => {
    cy.task('queryDB', `UPDATE #__kunena_users SET hideEmail = 0 WHERE userid = ${user.id}`);
    list().then((body) => expect(body, 'the address which is not hidden').to.include(cloaked));

    cy.task('queryDB', `UPDATE #__kunena_users SET hideEmail = 1 WHERE userid = ${user.id}`);
    list().then((body) => expect(body, 'the hidden address').to.not.include(cloaked));
  });

  it('shows a hidden address to a moderator', () => {
    cy.task('queryDB', `UPDATE #__kunena_users SET hideEmail = 1 WHERE userid = ${user.id}`);
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    list().then((body) => expect(body, 'the hidden address for a moderator').to.include(cloaked));
  });
});
