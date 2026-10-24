// The batch of the backend category list moves the chosen categories under another one (task categories.batchcategories):
// - with "move" chosen, and also without it (the radio "move", the only action of the batch, is not checked in the form);
// - not under one of its own children (a loop).
describe('Kunena backend categories: batch move', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const addCategory = (name, parentid) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (${parentid}, 'Cypress batch ${name} ${stamp}', 'cypress-batch-${name}-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
  ).then((category) => {
    ids[name] = category.insertId;
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-batch-${name}-${stamp}', 'catid', ${category.insertId}, 0)`);
  });
  const parent = (name) => cy.task('queryDB', `SELECT parentid FROM #__kunena_categories WHERE id = ${ids[name]}`).then((rows) => Number(rows[0].parentid));
  const send = (cid, target, body = {}) => cy.request(`${admin}&view=categories`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ method: 'POST', url: `${admin}&task=categories.batchcategories`, form: true, body: { 'cid[]': cid, batch_catid_target: target, ...body, [token]: '1' }, followRedirect: false });
  });

  beforeEach(() => {
    addCategory('target', 1);
    addCategory('moved', 1);
    cy.doAdministratorLogin();
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias LIKE 'cypress-batch-%-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE alias LIKE 'cypress-batch-%-${stamp}'`);
  });

  it('moves the category with "move" chosen', () => {
    cy.then(() => send(ids.moved, ids.target, { move_copy: 'move' }));
    cy.then(() => parent('moved')).should('eq', ids.target);
  });

  it('moves the category when "move" is not checked', () => {
    cy.then(() => send(ids.moved, ids.target));
    cy.then(() => parent('moved')).should('eq', ids.target);
  });

  it('does not move a category under its own child', () => {
    cy.then(() => send(ids.moved, ids.target, { move_copy: 'move' }));
    cy.then(() => send(ids.target, ids.moved, { move_copy: 'move' }));
    cy.then(() => parent('target')).should('eq', 1);
  });
});
