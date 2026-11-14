// The settings of a category show on the site: the CSS class suffix of a section on its block of the forum index, and the
// category header in a box above the topic list, not in a heading.
describe('Kunena category settings: class suffix and header on the site', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const header = `Cypress header ${stamp}`;
  // class_sfx is a varchar(20): a leading space, cypress-sfx- and the last 7 digits of the stamp fill it exactly.
  const sfx = `cypress-sfx-${String(stamp).slice(-7)}`;
  const ids = {};

  const addCategory = (name, parentid, classSfx, headerdesc) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (${parentid}, 'Cypress settings ${name} ${stamp}', 'cypress-settings-${name}-${stamp}', '', '${classSfx}', '', '${headerdesc}', '', 'joomla.level', 1, 1, 1, 8, 1, 90, 1, '{}', 'default')`,
  ).then((category) => {
    ids[name] = category.insertId;
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-settings-${name}-${stamp}', 'catid', ${category.insertId}, 0)`);
  });

  beforeEach(() => {
    addCategory('section', 0, ` ${sfx}`, '');
    cy.then(() => addCategory('child', ids.section, '', header));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias LIKE 'cypress-settings-%-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE name LIKE 'Cypress settings % ${stamp}'`);
  });

  it('puts the class suffix of a section on its block of the forum index', () => {
    cy.visit(`${base}&view=category&layout=list`);
    cy.then(() => cy.get(`#section${ids.section}`).should('have.class', sfx));
  });

  it('shows the category header in a div, not in a heading', () => {
    cy.then(() => cy.visit(`${base}&view=category&catid=${ids.child}`));
    cy.then(() => cy.get(`#category-headerdesc${ids.child}`)
      .should('match', 'div')
      .and('contain.text', header));
    cy.contains('h1', header).should('not.exist');
  });
});
