// The list of the banned users (view=user&layout=banmanager: the names, the level and the dates of the bans) is for the
// moderators. A visitor and a user get no list.
describe('Kunena ban manager: who may see the list of the banned users', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };

  const page = () => cy.request({ url: `${base}&view=user&layout=banmanager`, failOnStatusCode: false }).then((response) => {
    expect(response.status, 'the status').to.be.lessThan(500);

    return { status: response.status, body: String(response.body) };
  });

  beforeEach(() => {
    // A ban of the test user for an hour (a ban of the forum only: he can still log in).
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_users_banned (userid, ip, blocked, expiration, created_by, created_time, reason_private, reason_public, modified_by, modified_time, comments, params) VALUES (${user.id}, '', 0, DATE_ADD(NOW(), INTERVAL 1 HOUR), 797, NOW(), 'private reason', 'public reason', 0, '1000-01-01 00:00:00', '', '')`,
    );
  });

  afterEach(() => {
    cy.task('queryDB', `UPDATE #__kunena_users SET banned = '1000-01-01 00:00:00' WHERE userid = ${user.id}`);
  });

  it('shows the list to a moderator', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    page().then(({ status, body }) => {
      expect(status, 'the status for a moderator').to.eq(200);
      expect(body, 'the heading of the list').to.include('Banned From');
      expect(body, 'the banned user in the list').to.include('ci-user');
    });
  });

  it('shows no list to a visitor', () => {
    page().then(({ status, body }) => {
      expect(status, 'the status for a visitor').to.eq(401);
      expect(body, 'the list for a visitor').to.not.include('Banned From');
    });
  });

  it('shows no list to a user', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    page().then(({ status, body }) => {
      expect(status, 'the status for a user').to.eq(403);
      expect(body, 'the list for a user').to.not.include('Banned From');
    });
  });
});
