// Blocking in the inbox (plugin Kunena - Inbox): a user blocks another user, who can then send him no private message; staff and the
// user himself can not be blocked; after the unblock the messages come through again.
describe('Kunena inbox: blocking a user', () => {
  const base = '/index.php?option=com_ajax&plugin=inbox&group=kunena&format=json';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};

  const pageToken = () => cy.request('/index.php?option=com_kunena&view=home').then((response) => /name="([0-9a-f]{32})" value="1"/.exec(response.body)[1]);
  const ajax = (task, params) => pageToken().then((token) => cy.request({
    method: 'POST',
    url: `${base}&task=${task}`,
    form: true,
    body: { ...params, [token]: '1' },
    failOnStatusCode: false,
  }));
  const blocks = () => cy.task('queryDB', `SELECT blocked_id FROM #__kunena_inbox_blocks WHERE user_id = ${user.id}`).then((rows) => rows.map((row) => Number(row.blocked_id)));
  const received = () => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_inbox WHERE user_id = ${user.id} AND sender_id = ${ids.second}`).then((rows) => Number(rows[0].n));
  const sendFromSecond = () => {
    cy.clearAllCookies();
    cy.doFrontendLogin(`ci-blocked-${stamp}`, user.password, false);

    return ajax('send', { to: user.name, subject: `Hello ${stamp}`, body: 'A private message.' });
  };

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Blocked', 'ci-blocked-${stamp}', 'ci-blocked-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      ids.second = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts) VALUES (${result.insertId}, 1)`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_inbox WHERE sender_id = ${ids.second} OR user_id = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_inbox_blocks WHERE user_id IN (${user.id}, ${ids.second}) AND blocked_id IN (${user.id}, ${ids.second}, 797)`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_sessions WHERE userid = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.second}`);
  });

  it('refuses to block oneself and an administrator', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    ajax('block', { id: user.id });
    ajax('block', { id: 797 });
    blocks().should('deep.eq', []);
  });

  it('keeps the messages of a blocked user out, and lets them in again after the unblock', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => ajax('block', { id: ids.second }));
    cy.then(() => blocks()).should('deep.eq', [ids.second]);
    cy.then(() => sendFromSecond());
    cy.then(() => received()).should('eq', 0);

    cy.clearAllCookies();
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => ajax('unblock', { id: ids.second }));
    cy.then(() => blocks()).should('deep.eq', []);
    cy.then(() => sendFromSecond());
    cy.then(() => received()).should('eq', 1);
  });
});
