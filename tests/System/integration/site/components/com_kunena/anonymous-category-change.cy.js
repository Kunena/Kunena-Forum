// The box "post as anonymous" of the form of a new topic follows the category which is chosen: shown in a category which allows
// anonymous messages, and checked only where the category makes them anonymous by default (postAnonymous). A category which only
// allows them checked the box too, so the user who changed the category posted anonymously without asking for it. Nothing is
// posted; the categories of the test are removed after it.
describe('Kunena anonymous: the box of a new topic when the category is changed', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x' };
  const ids = {};

  // The categories of the test are children of the first one: the form of a category without children has no list of categories.
  const category = (key, parentid, allowAnonymous, postAnonymous) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, allowAnonymous, postAnonymous, params, iconset) VALUES (${parentid}, 'Cypress anon ${key} ${stamp}', 'cypress-anon-${key}-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, ${allowAnonymous}, ${postAnonymous}, '{}', 'default')`,
  ).then((result) => {
    ids[key] = result.insertId;
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-anon-${key}-${stamp}', 'catid', ${result.insertId}, 0)`);
  });

  beforeEach(() => {
    category('first', 1, 1, 0).then(() => {
      category('allows', ids.first, 1, 0);
      category('default', ids.first, 1, 1);
      category('none', ids.first, 0, 0);
    });
  });

  afterEach(() => {
    ['none', 'default', 'allows', 'first'].forEach((key) => {
      cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-anon-${key}-${stamp}'`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids[key]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids[key]}`);
    });
  });

  it('checks the box only in a category which makes the messages anonymous by default', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.first}`);
    cy.get('#kanonymous').should('not.be.checked');

    cy.get('#postcatid').then(($select) => {
      $select.val(String(ids.allows));
      $select[0].dispatchEvent(new Event('change', { bubbles: true }));
    });
    cy.get('#kanynomous-check').should('be.visible');
    cy.get('#kanonymous').should('not.be.checked');

    cy.get('#postcatid').then(($select) => {
      $select.val(String(ids.default));
      $select[0].dispatchEvent(new Event('change', { bubbles: true }));
    });
    cy.get('#kanonymous').should('be.checked');

    cy.get('#postcatid').then(($select) => {
      $select.val(String(ids.none));
      $select[0].dispatchEvent(new Event('change', { bubbles: true }));
    });
    cy.get('#kanynomous-check').should('not.be.visible');
    cy.get('#kanonymous').should('not.be.checked');
  });
});
