// The settings of the profile (hide the e-mail address, show the online status, gender...) took any number of the request: a
// value outside the choices of the form (hidemail=200, gender=300) was a database error (the columns are small) and nothing of the
// profile was saved. A value outside the choices is the default of the setting now.
describe('Kunena profile settings: values outside the choices of the form', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const username = `ci-set-${stamp}`;
  let id;

  const save = (fields) => cy.request(`${base}&view=home`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({ method: 'POST', url: `${base}&view=user&task=save`, form: true, body: { ...fields, [token]: '1' }, failOnStatusCode: false, followRedirect: false });
  });

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Settings', '${username}', '${username}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
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

  it('saves the profile with the defaults for the values which are no choice', () => {
    cy.then(() => cy.doFrontendLogin(username, 'ci-user-17082005-x', false));
    cy.then(() => save({ userid: id, personalText: `Settings ${stamp}`, gender: 300, hidemail: 200, showonline: 9, cansubscribe: 500, displayagebirthdate: 7, messageordering: 400 }))
      .its('status').should('be.lessThan', 500);
    cy.then(() => cy.task('queryDB', `SELECT personalText, gender, hideEmail, showOnline, canSubscribe, displayAgeBirthdate, ordering FROM #__kunena_users WHERE userid = ${id}`)).then((rows) => {
      expect(rows.length, 'the profile').to.eq(1);
      expect(rows[0].personalText, 'the profile is saved').to.eq(`Settings ${stamp}`);
      expect(Number(rows[0].gender), 'gender').to.eq(0);
      expect(Number(rows[0].hideEmail), 'hide the e-mail address').to.eq(1);
      expect(Number(rows[0].showOnline), 'show the online status').to.eq(1);
    });
  });
});
