// The setting "Display Age Or Birthdate" of the profile (the tab Settings of the edit page) was never saved: the form sends
// displayagebirthdate, the save read displayAgeBirthdate (the keys of the request are case sensitive), so every save stored 1
// (Birthdate) and a member who chose "Age" found "Birthdate" again.
describe('Kunena profile settings: Display Age Or Birthdate', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const username = `ci-bday-${stamp}`;
  const password = 'ci-user-17082005-x';
  let id;

  const save = (fields) => cy.request(`${base}&view=home`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({ method: 'POST', url: `${base}&view=user&task=save`, form: true, body: { ...fields, [token]: '1' }, failOnStatusCode: false, followRedirect: false });
  });

  beforeEach(() => {
    // A user of its own (the password of ci-user): the save writes his whole forum profile.
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Birthday', '${username}', '${username}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      id = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${id}, 2)`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${id}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${id}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${id}`);
  });

  it('saves the choice "Age"', () => {
    cy.then(() => cy.doFrontendLogin(username, password, false));
    cy.then(() => save({ userid: id, hidemail: 1, showonline: 1, displayagebirthdate: 0 }));
    cy.then(() => cy.task('queryDB', `SELECT displayAgeBirthdate FROM #__kunena_users WHERE userid = ${id}`)).then((rows) => {
      expect(rows.length, 'the profile of the user').to.eq(1);
      expect(Number(rows[0].displayAgeBirthdate), 'the choice "Age"').to.eq(0);
    });
  });
});
