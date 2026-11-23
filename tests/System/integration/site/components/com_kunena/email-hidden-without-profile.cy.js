// The e-mail address of a member is hidden unless he chose to show it (hideEmail, 1 by default). A Joomla user who has no Kunena
// profile yet (he never logged in since the forum was installed: users imported or created before) has no hideEmail at all, and
// "nothing" counted as "show it": with the e-mail option of the forum on, his profile gave his address to every visitor.
describe('Kunena profile: the e-mail address of a user without a Kunena profile', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const email = `cypress-hidden-${stamp}@example.com`;
  const ids = {};
  const old = {};

  beforeEach(() => {
    cy.kunena_setOption('showEmail', '1').then((value) => {
      old.showEmail = value ?? null;
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, params) VALUES ('Cypress Hidden ${stamp}', 'cypress-hidden-${stamp}', '${email}', '', 0, 0, NOW(), '{}')`,
    ).then((result) => {
      ids.user = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
    });
  });

  afterEach(() => {
    cy.kunena_setOption('showEmail', old.showEmail);
    cy.then(() => cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.user}`));
    cy.then(() => cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.user}`));
    cy.then(() => cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.user}`));
  });

  it('does not show the address to a visitor', () => {
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_users WHERE userid = ${ids.user}`).then((rows) => {
      expect(Number(rows[0].n), 'no Kunena profile yet').to.eq(0);
    });
    cy.request(`${base}&view=user&userid=${ids.user}`).then((page) => {
      expect(page.body, 'the profile is shown').to.include(`Cypress Hidden ${stamp}`);
      // The address is cloaked by Joomla in a script: its name part alone is enough to find it.
      expect(page.body, 'the e-mail address').to.not.include(`cypress-hidden-${stamp}@`);
      expect(page.body, 'the e-mail address, cloaked').to.not.include(`mailto:`);
    });
  });
});
