// The backend user list removes the subscriptions of the chosen users (tasks users.removecatsubscriptions and
// users.removetopicsubscriptions): the category and topic subscriptions of that user end, those of the other users stay.
describe('Kunena backend users: remove subscriptions', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const subscribed = (table, userid) => cy.task('queryDB', `SELECT COALESCE(SUM(subscribed), 0) AS n FROM #__kunena_${table} WHERE user_id = ${userid}`).then((rows) => Number(rows[0].n));
  const send = (task) => cy.request(`${admin}&view=users`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ method: 'POST', url: `${admin}&task=users.${task}`, form: true, body: { 'cid[]': ids.user, [token]: '1' }, followRedirect: false });
  });

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Subscriber', 'ci-subscriber-${stamp}', 'ci-subscriber-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      ids.user = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts) VALUES (${result.insertId}, 0)`);
      // Subscriptions of the user and of the second user of the test (another temporary user).
      cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${result.insertId}, 3, 0, 0, 1, ''), (${result.insertId}, 4, 0, 0, 1, '')`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${result.insertId}, 9, 3, 0, 0, 0, 0, 1, '')`);
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Other', 'ci-other-${stamp}', 'ci-other-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      ids.other = result.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${result.insertId}, 3, 0, 0, 1, '')`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${result.insertId}, 9, 3, 0, 0, 0, 0, 1, '')`);
    });
  });

  afterEach(() => {
    [ids.user, ids.other].forEach((id) => {
      cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${id}`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE user_id = ${id}`);
      cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${id}`);
      cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${id}`);
      cy.task('queryDB', `DELETE FROM #__users WHERE id = ${id}`);
    });
  });

  it('ends the category and topic subscriptions of the chosen user only', () => {
    cy.doAdministratorLogin();
    cy.then(() => send('removecatsubscriptions'));
    cy.then(() => subscribed('user_categories', ids.user)).should('eq', 0);
    cy.then(() => subscribed('user_categories', ids.other)).should('eq', 1);
    cy.then(() => send('removetopicsubscriptions'));
    cy.then(() => subscribed('user_topics', ids.user)).should('eq', 0);
    cy.then(() => subscribed('user_topics', ids.other)).should('eq', 1);
  });
});
