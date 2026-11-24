// A category whose name has a "%" (100% Free, Save 50%) gave an error page (500): the name was the format of sprintf() in the
// title of the category page, and "% F" or a "%" at the end is a format without its value (ArgumentCountError, ValueError).
describe('Kunena category: a name with a percent sign', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};

  const addCategory = (name, alias) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, '${name}', '${alias}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 90, 1, '{}', 'default')`,
  ).then((category) => {
    ids[alias] = category.insertId;
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('${alias}', 'catid', ${category.insertId}, 0)`);
  });

  beforeEach(() => {
    addCategory(`Cypress 100% Free ${stamp}`, `cypress-percent-a-${stamp}`);
    addCategory(`Cypress save 50% ${stamp}%`, `cypress-percent-b-${stamp}`);
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias LIKE 'cypress-percent-%-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE alias LIKE 'cypress-percent-%-${stamp}'`);
  });

  it('shows the page of the category with its name in the title', () => {
    [[`cypress-percent-a-${stamp}`, `Cypress 100% Free ${stamp}`], [`cypress-percent-b-${stamp}`, `Cypress save 50% ${stamp}%`]].forEach(([alias, name]) => {
      cy.then(() => cy.request({ url: `${base}&view=category&catid=${ids[alias]}`, failOnStatusCode: false })).then((response) => {
        expect(response.status, name).to.eq(200);
        expect(response.body).to.match(/<title>[^<]*<\/title>/);
        expect(response.body.match(/<title>([^<]*)<\/title>/)[1]).to.contain(name);
      });
    });
  });
});
