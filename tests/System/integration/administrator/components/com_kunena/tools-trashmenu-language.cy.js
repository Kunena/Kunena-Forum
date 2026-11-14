// On a site with one content language, "Trash the menu" makes the Forum item and its sub items for all languages (*): Joomla's
// start page drop-down of the Forum item then lists the sub items without " (en-GB)" after their titles.
describe('Kunena backend tools: the language of the menu made by "Trash the menu"', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const menu = () => cy.task('queryDB', "SELECT GROUP_CONCAT(id ORDER BY id) AS ids FROM #__menu WHERE menutype = 'kunenamenu'").then((rows) => rows[0].ids);

  beforeEach(() => {
    cy.doAdministratorLogin();
  });

  it('makes the forum item and its sub items for all languages on a single language site', () => {
    cy.task('queryDB', 'SELECT COUNT(*) AS n FROM #__languages WHERE published = 1').then((rows) => expect(Number(rows[0].n)).to.eq(1));
    menu().then((before) => {
      cy.request(`${admin}&view=tools&layout=menu`).then((page) => {
        const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

        return cy.request({ method: 'POST', url: `${admin}&view=tools`, form: true, body: { task: 'tools.trashMenu', [token]: '1' }, followRedirect: false });
      });
      // The menu was made new: its items have new ids.
      menu().should('not.eq', before);
    });
    cy.task('queryDB', "SELECT link, language FROM #__menu WHERE menutype = 'kunenamenu' AND client_id = 0").then((items) => {
      // The Forum item and its 9 sub items.
      expect(items).to.have.length(10);
      items.forEach((item) => expect(item.language, item.link).to.eq('*'));
      expect(new Set(items.map((item) => `${item.link}|${item.language}`)).size).to.eq(items.length);
    });
  });
});
