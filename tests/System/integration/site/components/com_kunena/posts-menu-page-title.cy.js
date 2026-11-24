// The lists of posts (view=topics&layout=posts) under a menu item with a "Browser Page Title": the title of the page was empty
// (only " - <board>"): the page checked page_title, then read the parameter menu-title, which no menu item has.
describe('Kunena lists of posts: the browser page title of the menu item', () => {
  const stamp = Date.now();
  const title = `Cypress posts title ${stamp}`;
  const item = 140;
  const old = {};

  before(() => {
    cy.task('queryDB', `SELECT params FROM #__menu WHERE id = ${item}`).then((rows) => {
      old.params = rows[0].params;

      const params = JSON.parse(old.params || '{}');

      params.page_title = title;

      return cy.task('queryDB', `UPDATE #__menu SET params = '${JSON.stringify(params)}' WHERE id = ${item}`);
    });
  });

  after(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__menu SET params = '${old.params}' WHERE id = ${item}`));
  });

  it('has the page title of the menu item', () => {
    cy.request(`/index.php?option=com_kunena&view=topics&layout=posts&mode=recent&userid=798&Itemid=${item}`).then((page) => {
      const found = /<title>([^<]*)<\/title>/.exec(String(page.body));

      expect(found && found[1], 'the title of the page').to.include(title);
    });
  });
});
