// The form of a new topic loads the template of the category (task categorytemplate) when the category is chosen. The template
// of a category is for the users who may write a topic in it: a user who may not see the category gets nothing of it.
describe('Kunena topic templates: who gets the template of a category', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x' };
  const ids = {};

  const template = (catid) => cy.request(`${base}&view=home`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({ url: `${base}&view=topic&task=categorytemplate&format=raw&catid=${catid}&${token}=1`, failOnStatusCode: false })
      .then((response) => String(typeof response.body === 'string' ? response.body : JSON.stringify(response.body)));
  });

  const addCategory = (name, access) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress template ${name} ${stamp}', 'cypress-template-${name}-${stamp}', '', '', '', '', 'TEMPLATE${name}${stamp}', 'joomla.level', ${access}, ${access}, 1, 8, 1, 90, 1, '{}', 'default')`,
  ).then((category) => {
    ids[name] = category.insertId;
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-template-${name}-${stamp}', 'catid', ${category.insertId}, 0)`);
  });

  beforeEach(() => {
    addCategory('open', 1);
    addCategory('hidden', 3);
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias LIKE 'cypress-template-%-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE name LIKE 'Cypress template % ${stamp}'`);
  });

  it('gives a user the template of a category he may write in, and not of a hidden one', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => template(ids.open)).should('include', `TEMPLATEopen${stamp}`);
    cy.then(() => template(ids.hidden)).should('not.include', `TEMPLATEhidden${stamp}`);
  });

  it('gives a moderator the template of the hidden category', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.then(() => template(ids.hidden)).should('include', `TEMPLATEhidden${stamp}`);
  });
});
