// The profile form of a user, saved with the values of a hostile or strange user: what is saved is shown on the profile without an
// injected element or a link with a script, and the page does not give a PHP notice or warning (the afterEach of the support file).
describe('Kunena writing: the profile of a user', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  let start;

  beforeEach(() => {
    cy.doFrontendLogin(user.name, user.password);
  });

  it('keeps the start values for the restore', () => {
    cy.task('queryDB', `SELECT signature, personalText, CAST(birthdate AS CHAR) AS birthdate, location, websitename, websiteurl, socials, hideEmail, showOnline FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      start = rows[0];
    });
  });

  after(() => {
    if (!start) {
      return;
    }

    const escape = (value) => (value === null ? 'NULL' : `'${String(value).replaceAll('\\', '\\\\').replaceAll("'", "\\'")}'`);

    cy.task(
      'queryDB',
      `UPDATE #__kunena_users SET signature = ${escape(start.signature)}, personalText = ${escape(start.personalText)}, birthdate = ${escape(start.birthdate)}, location = ${escape(start.location)}, websitename = ${escape(start.websitename)}, websiteurl = ${escape(start.websiteurl)}, socials = ${escape(start.socials)}, hideEmail = ${Number(start.hideEmail)}, showOnline = ${Number(start.showOnline)} WHERE userid = ${user.id}`,
    );
  });

  it('saves the profile with strange values', () => {
    cy.visit(`${base}&view=user&layout=edit`);

    cy.get('#kuserform [name="personalText"]').clear({ force: true }).type('It is "quoted" & <b>bold</b>', { force: true, parseSpecialCharSequences: false });
    cy.get('#kuserform [name="location"]').clear({ force: true }).type('<script>alert(1)</script>\'"', { force: true, parseSpecialCharSequences: false });
    cy.get('#kuserform [name="websitename"]').clear({ force: true }).type('<img src=x onerror=alert(1)>', { force: true, parseSpecialCharSequences: false });
    cy.get('#kuserform [name="websiteurl"]').clear({ force: true }).type('javascript:alert(1)', { force: true, parseSpecialCharSequences: false });
    cy.get('#kuserform [name="signature"]').clear({ force: true }).type('[b]sig[/b] [url=javascript:alert(1)]x[/url]', { force: true, parseSpecialCharSequences: false });
    cy.get('#kuserform [name="birthdate"]').invoke('val', '2024-02-31');
    cy.get('#kuserform [name="socialgithub"], #kuserform [name="socialx_social"]').first().invoke('val', '"><img src=x onerror=alert(1)>');
    cy.get('#kuserform button[type="submit"]').click();

    cy.task('queryDB', `SELECT location, websiteurl, birthdate FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      expect(rows[0].location, 'the location is saved').to.include('alert(1)');
      expect(rows[0].websiteurl, 'the address is saved as it is typed (the profile makes it a safe link)').to.eq('javascript:alert(1)');
    });
  });

  it('shows the saved profile without an injected element or a script link', () => {
    cy.visit(`${base}&view=user&userid=${user.id}`);

    cy.get('body').then(($body) => {
      expect($body.find('script:contains("alert(1)")').length, 'script of the user').to.eq(0);
      expect($body.find('img[onerror], [onerror], [onclick*="alert"]').length, 'handler of the user').to.eq(0);
      expect($body.find('a[href^="javascript:"], a[href^="JavaScript:"]').length, 'a script link').to.eq(0);
    });
  });

  it('lists the user in the list of users', () => {
    cy.visit(`${base}&view=user&layout=list`);
    cy.get('body').should('contain.text', user.name);
  });
});
