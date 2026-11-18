// The signature on the edit page of a user in the backend follows the option "Max. Signature Length", as the profile on the site: the
// field stops at the limit, the counter of the characters which are left follows every change (typing, deleting, pasting; it only
// followed the keys which type a character), and the save cuts a longer signature which a request sends.
describe('Kunena backend user edit: the length of the signature', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const limit = 20;
  const ids = {};
  const old = {};
  const edit = (userid) => `${admin}&view=user&layout=edit&userid=${userid}`;
  const signature = () => cy.task('queryDB', `SELECT signature FROM #__kunena_users WHERE userid = ${ids.user}`).then((rows) => rows[0].signature);

  before(() => {
    cy.kunena_setOption('maxSig', String(limit)).then((value) => {
      old.maxSig = value;
    });
  });

  after(() => {
    cy.then(() => cy.kunena_setOption('maxSig', old.maxSig === undefined ? null : old.maxSig));
  });

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI signature', 'ci-signature-${stamp}', 'ci-signature-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      ids.user = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts, signature) VALUES (${result.insertId}, 0, 'abc')`);
    });
    cy.doAdministratorLogin();
  });

  afterEach(() => {
    cy.then(() => {
      if (ids.user) {
        cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.user}`);
        cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.user}`);
        cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.user}`);
      }
    });
  });

  it('counts the characters which are left after typing, deleting and pasting, and stops at the limit', () => {
    cy.then(() => cy.visit(edit(ids.user)));
    cy.get('#user-signature').should('have.attr', 'maxlength', String(limit));
    cy.get('#current_count').should('have.value', String(limit - 3));

    cy.get('#user-signature').type('de');
    cy.get('#current_count').should('have.value', String(limit - 5));
    cy.get('#user-signature').type('{backspace}{backspace}{backspace}');
    cy.get('#current_count').should('have.value', String(limit - 2));

    // A paste is a change of the value (the event input), as the browser gives it.
    cy.get('#user-signature').invoke('val', 'pasted text').trigger('input');
    cy.get('#current_count').should('have.value', String(limit - 'pasted text'.length));

    // The browser stops typing at the limit.
    cy.get('#user-signature').clear().type('x'.repeat(limit + 5));
    cy.get('#user-signature').invoke('val').its('length').should('eq', limit);
    cy.get('#current_count').should('have.value', '0');
  });

  it('cuts a longer signature which a request sends', () => {
    cy.then(() => cy.visit(edit(ids.user)));
    // The field without its limit, as a request made by hand.
    cy.get('#user-signature').invoke('removeAttr', 'maxlength').invoke('val', 'y'.repeat(limit + 10));
    cy.get('#toolbar-save button, #toolbar-apply button, .button-apply').first().click();
    signature().should('eq', 'y'.repeat(limit));
  });
});
