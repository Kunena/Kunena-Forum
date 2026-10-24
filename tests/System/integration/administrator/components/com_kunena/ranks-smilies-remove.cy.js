// The backend removes the chosen ranks (task ranks.remove) and smilies (task smilies.remove): those rows go, the others stay,
// and the profile of a user who had the removed special rank still opens.
describe('Kunena backend: remove ranks and smilies', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const code = `:cy${String(stamp).slice(-6)}:`;
  const ids = {};
  const count = (sql) => cy.task('queryDB', sql).then((rows) => Number(rows[0].n));
  const send = (view, task, cid) => cy.request(`${admin}&view=${view}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ method: 'POST', url: `${admin}&task=${view}.${task}`, form: true, body: { 'cid[]': cid, [token]: '1' }, followRedirect: false });
  });

  beforeEach(() => {
    cy.task('queryDB', 'SELECT COUNT(*) AS n FROM #__kunena_ranks').then((rows) => {
      ids.ranks = Number(rows[0].n);
    });
    cy.task('queryDB', 'SELECT COUNT(*) AS n FROM #__kunena_smileys').then((rows) => {
      ids.smileys = Number(rows[0].n);
    });
    cy.task('queryDB', `INSERT INTO #__kunena_ranks (rankTitle, rankMin, rankSpecial, rankImage) VALUES ('Cypress rank ${stamp}', 0, 1, 'rank1.gif')`).then((rank) => {
      ids.rank = rank.insertId;
    });
    cy.task('queryDB', `INSERT INTO #__kunena_smileys (code, location, emoticonbar) VALUES ('${code}', 'smile.png', 0)`).then((smiley) => {
      ids.smiley = smiley.insertId;
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Ranked', 'ci-ranked-${stamp}', 'ci-ranked-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      ids.user = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts, \`rank\`) VALUES (${result.insertId}, 0, ${ids.rank})`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_ranks WHERE rankTitle = 'Cypress rank ${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_smileys WHERE code = '${code}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.user}`);
  });

  it('removes the chosen rank and smiley only', () => {
    cy.doAdministratorLogin();
    cy.then(() => send('ranks', 'remove', ids.rank));
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_ranks WHERE rankId = ${ids.rank}`)).should('eq', 0);
    cy.then(() => count('SELECT COUNT(*) AS n FROM #__kunena_ranks')).should('eq', ids.ranks);
    cy.then(() => send('smilies', 'remove', ids.smiley));
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_smileys WHERE id = ${ids.smiley}`)).should('eq', 0);
    cy.then(() => count('SELECT COUNT(*) AS n FROM #__kunena_smileys')).should('eq', ids.smileys);
    // The profile of the user of the removed rank.
    cy.then(() => cy.request({ url: `/index.php?option=com_kunena&view=user&userid=${ids.user}`, failOnStatusCode: false })).its('status').should('eq', 200);
  });
});
