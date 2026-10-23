// The tool "Trash the menu" (task tools.trashMenu) deletes the menu of the forum and makes it new: the menu items, their numbers
// and their settings are gone. Like every task which changes something it needs the token of the form: a link which an
// administrator opens (or a picture on a page with that address) must not do it.
describe('Kunena backend tools: the token of "Trash the menu"', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const menu = () => cy.task('queryDB', "SELECT GROUP_CONCAT(id ORDER BY id) AS ids FROM #__menu WHERE menutype = 'kunenamenu'").then((rows) => rows[0].ids);

  beforeEach(() => {
    cy.doAdministratorLogin();
  });

  it('keeps the menu of the forum for a request without the token', () => {
    menu().then((before) => {
      cy.request({ url: `${admin}&task=tools.trashMenu`, failOnStatusCode: false, followRedirect: false });
      cy.request({ method: 'POST', url: `${admin}&task=tools.trashMenu`, form: true, body: {}, failOnStatusCode: false, followRedirect: false });
      menu().should('eq', before);
    });
  });
});
