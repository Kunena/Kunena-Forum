// The check of the alias in the form of a category on the site (category-edit.js and the task category.chkaliases of the site):
// while an alias is typed, the server says whether another category has it. It called a task which only the backend has, from a
// relative address under the page, so every alias was taken and the list of the aliases of the category was replaced.
describe('Kunena categories: the alias check of the category form on the site', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};

  const addCategory = (name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, '${name} ${stamp}', '${name}-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
  ).then((category) => {
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('${name}-${stamp}', 'catid', ${category.insertId}, 0)`);

    return cy.wrap(category.insertId);
  });

  // Types an alias and waits for the answer of the server.
  const typeAlias = (alias) => {
    cy.intercept('GET', /task=category\.chkaliases/).as('check');
    cy.get('#jform_aliases').clear().type(alias);
    cy.wait('@check').its('response.statusCode').should('eq', 200);
  };

  beforeEach(() => {
    addCategory('cypress-alias-edited').then((id) => {
      ids.edited = id;
    });
    addCategory('cypress-alias-other').then((id) => {
      ids.other = id;
    });
  });

  afterEach(() => {
    ['edited', 'other'].forEach((name) => {
      cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-alias-${name}-${stamp}'`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids[name]}`);
    });
  });

  it('accepts a free alias and refuses the alias of another category', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.visit(`${base}&view=category&layout=manage&catid=${ids.edited}`);
    cy.get('#aliascheck select').should('exist');

    // A free alias, and the alias of the category itself: no red border, the list of its aliases stays.
    typeAlias(`cypress-alias-free-${stamp}`);
    cy.get('#jform_aliases').should('not.have.class', 'invalid-border');
    typeAlias(`cypress-alias-edited-${stamp}`);
    cy.get('#jform_aliases').should('not.have.class', 'invalid-border');
    cy.get('#aliascheck select').should('exist');

    // The alias of another category is taken.
    typeAlias(`cypress-alias-other-${stamp}`);
    cy.get('#jform_aliases').should('have.class', 'invalid-border');
  });

  it('answers only the users who may manage the categories', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request({ url: `${base}&view=category&task=category.chkaliases&alias=x&catid=0&format=json`, failOnStatusCode: false })
      .its('status').should('eq', 403);
  });
});
