// An administrator of one category (not a global administrator) may not move his category under a parent of which he is no
// administrator: the save keeps the old parent. But the save took the access of the category from that refused parent all the same,
// as for a moved category: he could open his category, closed to the visitors, by naming an open category as its parent. A refused
// move keeps the access of the category.
describe('Kunena categories: a refused move keeps the access', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};

  beforeEach(() => {
    // A category for the access level Special, under the category 1, of which ci-user is the administrator.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress closed ${stamp}', 'cypress-closed-${stamp}', '', '', '', '', '', 'joomla.level', 3, 1, 1, 8, 1, 96, 1, '{}', 'default')`,
    ).then((category) => {
      ids.own = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-closed-${stamp}', 'catid', ${category.insertId}, 0)`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${user.id}, ${category.insertId}, 2, 0, 0, '')`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE category_id = ${ids.own}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.own}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-closed-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.own}`);
  });

  it('keeps the parent and the access level when the new parent is not his', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => cy.request(`${base}&view=home`)).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

      // The category 2 is open to everybody, and ci-user is no administrator of it.
      return cy.request({
        method: 'POST',
        url: `${base}&view=category&catid=${ids.own}`,
        form: true,
        body: { task: 'save', catid: ids.own, [token]: '1', parentid: 2, name: `Cypress closed ${stamp}`, published: 1 },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.then(() => cy.task('queryDB', `SELECT name, parentid, access FROM #__kunena_categories WHERE id = ${ids.own}`)).then((rows) => {
      expect(rows[0].name, 'the category was saved').to.eq(`Cypress closed ${stamp}`);
      expect(rows[0].parentid, 'the parent').to.eq(1);
      expect(rows[0].access, 'the access level').to.eq(3);
    });
  });
});
