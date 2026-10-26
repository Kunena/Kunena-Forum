// To install or to remove a template of Kunena is to install or to remove code (a template has PHP files): it needs the right of
// Joomla to install extensions (core.manage in com_installer), not only the right to manage the forum. An administrator of the
// group Administrator manages the forum but installs no extension on the test site; before, he could do both with the templates.
describe('Kunena templates: who may install and remove them', () => {
  const root = 'E:/www/apache/htdocs/tests';
  const dummy = `cypressrights${Date.now() % 100000}`;
  const user = { username: 'ci-forumadmin', password: 'ci-user-17082005-x' };
  const templates = '/administrator/index.php?option=com_kunena&view=templates';
  let userId;

  before(() => {
    // A user of the group Administrator (7) with the password of ci-user.
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Forum Admin', '${user.username}', 'ci-forumadmin@example.com', password, 0, 0, NOW(), NULL, '', '{"allowTourAutoStart":"0"}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      userId = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${userId}, 7)`);
    });
    // A template which may be removed: a copy of the description of Aurelia in a folder of its own.
    cy.readFile(`${root}/components/com_kunena/template/aurelia/config/template.xml`).then((xml) => {
      cy.writeFile(`${root}/components/com_kunena/template/${dummy}/config/template.xml`, xml.replace(/<name>[^<]*<\/name>/, `<name>${dummy}</name>`));
    });
  });

  after(() => {
    cy.task('deleteRelativePath', `components/com_kunena/template/${dummy}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${userId}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${userId}`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${userId}`);
    cy.task('queryDB', `DELETE FROM #__session WHERE userid = ${userId}`);
  });

  it('does not let an administrator without the right to install extensions remove or install a template', () => {
    cy.doAdministratorLogin(user.username, user.password, false);

    // He is in the backend of the forum (the test means nothing for a visitor of the login page).
    cy.request(templates).then((page) => {
      expect(page.body, 'the page of the templates (not the login form)').not.to.contain('mod-login');

      return /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];
    }).then((token) => {
      cy.request({ method: 'POST', url: templates, form: true, body: { task: 'template.uninstall', 'cid[]': dummy, [token]: '1' } })
        .its('body').should('match', /permission to access this/);
      cy.readFile(`${root}/components/com_kunena/template/${dummy}/config/template.xml`).should('exist');

      // A form with a file, written by hand.
      const boundary = 'cypressboundary';
      const field = (name, value) => `--${boundary}\r\nContent-Disposition: form-data; name="${name}"\r\n\r\n${value}\r\n`;
      const body = field('task', 'templates.install') + field(token, '1')
        + `--${boundary}\r\nContent-Disposition: form-data; name="install_package"; filename="cypress-template.zip"\r\nContent-Type: application/zip\r\n\r\nnot a zip\r\n`
        + `--${boundary}--\r\n`;

      // The package is not even looked at: the answer is that he may not do it.
      cy.request({ method: 'POST', url: templates, headers: { 'Content-Type': `multipart/form-data; boundary=${boundary}` }, body })
        .its('body').should('match', /permission to access this/);
    });
  });
});
