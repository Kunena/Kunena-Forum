// The filter "banned users" of the user list (usertype=banned) works together with the other conditions of the list: the name
// which is searched still counts. (The condition had an OR without brackets, which undid the search and the hidden users.)
describe('Kunena user list: the filter of the banned users', () => {
  const base = '/index.php?option=com_kunena&view=user&layout=list';
  const user = { name: 'ci-user', id: 798 };
  let userListUserType;

  const list = (query) => cy.request({ url: `${base}&${query}`, failOnStatusCode: false }).then((response) => {
    expect(response.status, `the status for "${query}"`).to.eq(200);

    return String(response.body);
  });

  before(() => {
    cy.kunena_setOption('userListUserType', '1').then((old) => {
      userListUserType = old;
    });
  });

  after(() => {
    cy.kunena_setOption('userListUserType', userListUserType ?? '0');
  });

  beforeEach(() => {
    // A ban of the test user for an hour.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_users_banned (userid, ip, blocked, expiration, created_by, created_time, reason_private, reason_public, modified_by, modified_time, comments, params) VALUES (${user.id}, '', 0, DATE_ADD(NOW(), INTERVAL 1 HOUR), 797, NOW(), '', '', 0, '1000-01-01 00:00:00', '', '')`,
    );
    cy.task('queryDB', `UPDATE #__kunena_users SET banned = DATE_ADD(UTC_TIMESTAMP(), INTERVAL 1 HOUR) WHERE userid = ${user.id}`);
  });

  afterEach(() => {
    cy.task('queryDB', `UPDATE #__kunena_users SET banned = '1000-01-01 00:00:00' WHERE userid = ${user.id}`);
  });

  it('lists the banned user, and not when another name is searched', () => {
    list('usertype=banned').then((body) => {
      expect(body, 'the banned user').to.include(user.name);
      expect(body, 'a user who is not banned').to.not.include('ci-admin');
    });
    list('usertype=banned&search=zzznobody').then((body) => {
      expect(body, 'the banned user with a search for another name').to.not.include(`>${user.name}<`);
    });
  });
});
