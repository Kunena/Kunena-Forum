// Who may save the profile of whom: a user his own, a moderator the forum profile of a user (not his account: the name, the e-mail
// address and the password of Joomla stay as they are) and not the profile of an administrator, a visitor nobody's.
describe('Kunena profiles: who may save whose profile', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  let second;
  // The rules of com_users, when a test changed them (they are put back after the test, also after a failure).
  let usersRules;

  const save = (fields) => cy.request(`${base}&view=home`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({ method: 'POST', url: `${base}&view=user&task=save`, form: true, body: { ...fields, [token]: '1' }, failOnStatusCode: false, followRedirect: false });
  });
  const account = (id) => cy.task('queryDB', `SELECT name, email, password FROM #__users WHERE id = ${id}`).then((rows) => rows[0]);
  const profileText = (id) => cy.task('queryDB', `SELECT personalText FROM #__kunena_users WHERE userid = ${id}`).then((rows) => rows[0]?.personalText ?? null);

  beforeEach(() => {
    // A second user of the forum (the password of the first one), and the first one is a moderator of the whole forum.
    cy.task(
      'queryDB',
      "INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Second', 'ci-second', 'ci-second@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798",
    ).then((result) => {
      second = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${second}, 2)`);
    });
    // The row of category 0 may exist already: the forum keeps the "all read" time of the user in it.
    cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${user.id}, 0, 1, UNIX_TIMESTAMP(), 0, '') ON DUPLICATE KEY UPDATE role = 1`);
    cy.task('queryDB', `UPDATE #__kunena_users SET moderator = 1 WHERE userid = ${user.id}`);
  });

  afterEach(() => {
    if (usersRules !== undefined) {
      cy.task('queryDB', `UPDATE #__assets SET rules = '${usersRules}' WHERE name = 'com_users'`);
      usersRules = undefined;
    }

    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${second}`);
    cy.task('queryDB', `UPDATE #__kunena_users SET moderator = 0, personalText = NULL WHERE userid IN (${user.id}, ${admin})`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${user.id} AND category_id = 0`);
  });

  it('lets a moderator change the forum profile of a user, but not his account', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    account(second).then((before) => {
      save({ userid: second, name: 'Hijacked', email: 'hijack@example.test', password: 'NewPassword-12345', password2: 'NewPassword-12345', personalText: 'A text of the moderator' });
      account(second).then((after) => expect(after, 'the account of the user').to.deep.eq(before));
      profileText(second).should('eq', 'A text of the moderator');
    });
  });

  it('does not let a moderator change the privacy settings of a user', () => {
    // Hide the e-mail address, show the online status, show the age: the choices of the user (a moderator made the e-mail
    // address of a user public).
    cy.task('queryDB', `INSERT INTO #__kunena_users (userid, hideEmail, showOnline) VALUES (${second}, 1, 1)`);
    cy.doFrontendLogin(user.name, user.password, false);
    save({ userid: second, hidemail: 0, showonline: 0, personalText: 'A text of the moderator' });
    cy.task('queryDB', `SELECT hideEmail, showOnline, personalText FROM #__kunena_users WHERE userid = ${second}`).then((rows) => {
      expect(rows[0].personalText, 'the forum profile, which the moderator may change').to.eq('A text of the moderator');
      expect(Number(rows[0].hideEmail), 'the e-mail address stays hidden').to.eq(1);
      expect(Number(rows[0].showOnline), 'the online status').to.eq(1);
    });
  });

  it('does not let a moderator change the profile of an administrator', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    save({ userid: admin, personalText: 'A text of the moderator' });
    profileText(admin).should('not.eq', 'A text of the moderator');
  });

  it('lets a user change his own profile, and nobody else\'s', () => {
    cy.task('queryDB', `UPDATE #__kunena_users SET moderator = 0 WHERE userid = ${user.id}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${user.id} AND category_id = 0`);
    cy.doFrontendLogin(user.name, user.password, false);
    save({ userid: user.id, personalText: 'My own text' });
    profileText(user.id).should('eq', 'My own text');
    save({ userid: second, personalText: 'A text of another user' });
    profileText(second).should('not.eq', 'A text of another user');
  });

  it('does not let an administrator of the forum change the account of a user when he may not edit the users in Joomla', () => {
    // An administrator of the forum (the group Administrator manages the components) for whom the users of Joomla are closed
    // (core.edit of com_users denied): before, he changed the e-mail address and the password of another user in the profile.
    const forumAdmin = 'ci-forumadmin2';
    let forumAdminId;

    cy.task('queryDB', "SELECT rules FROM #__assets WHERE name = 'com_users'").then((rows) => {
      usersRules = rows[0].rules;
      const rules = JSON.parse(usersRules || '{}');

      rules['core.edit'] = { ...(rules['core.edit'] ?? {}), 7: 0 };
      cy.task('queryDB', `UPDATE #__assets SET rules = '${JSON.stringify(rules)}' WHERE name = 'com_users'`);
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Forum Admin', '${forumAdmin}', '${forumAdmin}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      forumAdminId = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${forumAdminId}, 7)`);
    });
    // The lists of the administrators of the forum are kept in the cache.
    cy.task('deleteRelativePath', 'cache/kunena');
    cy.task('deleteRelativePath', 'administrator/cache/kunena');

    cy.doFrontendLogin(forumAdmin, user.password, false);
    account(second).then((before) => {
      save({ userid: second, name: 'Hijacked', email: 'hijack@example.test', password: 'NewPassword-12345', password2: 'NewPassword-12345' });
      account(second).then((after) => expect(after, 'the account of the user').to.deep.eq(before));
    });

    cy.then(() => {
      cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${forumAdminId}`);
      cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${forumAdminId}`);
      cy.task('queryDB', `DELETE FROM #__session WHERE userid = ${forumAdminId}`);
      cy.task('queryDB', `DELETE FROM #__users WHERE id = ${forumAdminId}`);
      cy.task('deleteRelativePath', 'cache/kunena');
      cy.task('deleteRelativePath', 'administrator/cache/kunena');
    });
  });

  it('lets a visitor save nothing', () => {
    save({ userid: second, personalText: 'A text of a visitor' });
    profileText(second).should('not.eq', 'A text of a visitor');
  });
});
