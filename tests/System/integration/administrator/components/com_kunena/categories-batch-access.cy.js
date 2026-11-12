// The batch of the backend category list sets the Joomla access level of the chosen categories (task categories.batchcategories):
// - alone or together with a move;
// - only to an existing level, only with the token, not without a target or a level;
// - and tells how many categories it changed.
describe('Kunena backend categories: batch access level', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const addCategory = (name, accesstype, access) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress batch access ${name} ${stamp}', 'cypress-batch-access-${name}-${stamp}', '', '', '', '', '', '${accesstype}', ${access}, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
  ).then((category) => {
    ids[name] = category.insertId;
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-batch-access-${name}-${stamp}', 'catid', ${category.insertId}, 0)`);
  });
  const row = (name) => cy.task('queryDB', `SELECT parentid, accesstype, access, pubAccess, adminAccess FROM #__kunena_categories WHERE id = ${ids[name]}`).then((rows) => ({
    parentid: Number(rows[0].parentid),
    accesstype: rows[0].accesstype,
    access: Number(rows[0].access),
    pubAccess: Number(rows[0].pubAccess),
    adminAccess: Number(rows[0].adminAccess),
  }));
  const send = (body, withToken = true) => cy.request(`${admin}&view=categories`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    // Built by hand: two values of cid[] are sent as two fields.
    const form = new URLSearchParams();

    Object.entries(withToken ? { ...body, [token]: '1' } : body).forEach(([key, value]) => [].concat(value).forEach((item) => form.append(key, item)));

    return cy.request({ method: 'POST', url: `${admin}&task=categories.batchcategories`, headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: form.toString(), followRedirect: false });
  });
  const unchanged = { parentid: 1, accesstype: 'joomla.group', access: 2, pubAccess: 1, adminAccess: 8 };

  beforeEach(() => {
    addCategory('target', 'joomla.level', 1);
    addCategory('changed', 'joomla.group', 2);
    addCategory('same', 'joomla.level', 3);
    addCategory('public', 'joomla.level', 1);
    cy.doAdministratorLogin();
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias LIKE 'cypress-batch-access-%-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE alias LIKE 'cypress-batch-access-%-${stamp}'`);
  });

  it('shows the access level list in the batch dialog', () => {
    cy.request(`${admin}&view=categories`).its('body').then((body) => {
      expect(body).to.contain('name="batch[assetgroup_id]"');
      expect(body).to.match(/name="batch\[assetgroup_id\]"[^>]*>\s*<option value="">[^<]*<\/option>\s*<option value="\d+"/);
    });
  });

  it('sets the level without a target', () => {
    cy.then(() => send({ 'cid[]': ids.changed, batch_catid_target: 'select', 'batch[assetgroup_id]': 3 }));
    cy.then(() => row('changed')).should('deep.equal', { ...unchanged, accesstype: 'joomla.level', access: 3 });
  });

  it('moves the category and sets the level in one run', () => {
    cy.then(() => send({ 'cid[]': ids.changed, batch_catid_target: ids.target, move_copy: 'move', 'batch[assetgroup_id]': 3 }));
    cy.then(() => row('changed')).should('deep.equal', { ...unchanged, parentid: ids.target, accesstype: 'joomla.level', access: 3 });
    cy.then(() => row('target')).its('access').should('eq', 1);
  });

  it('changes nothing with a level which does not exist', () => {
    cy.then(() => send({ 'cid[]': ids.changed, batch_catid_target: ids.target, move_copy: 'move', 'batch[assetgroup_id]': 999999 }));
    cy.then(() => row('changed')).should('deep.equal', unchanged);
  });

  it('changes nothing without a target and without a level', () => {
    cy.then(() => send({ 'cid[]': ids.changed, batch_catid_target: 'select', 'batch[assetgroup_id]': '' }));
    cy.then(() => row('changed')).should('deep.equal', unchanged);
  });

  it('changes nothing without the token', () => {
    cy.then(() => send({ 'cid[]': ids.changed, 'batch[assetgroup_id]': 3 }, false));
    cy.then(() => row('changed')).should('deep.equal', unchanged);
  });

  it('counts only the categories it changed', () => {
    cy.then(() => send({ 'cid[]': [ids.same, ids.public], 'batch[assetgroup_id]': 3 }));
    cy.then(() => row('public')).its('access').should('eq', 3);
    cy.visit(`${admin}&view=categories`);
    cy.contains('Categories with a changed access level: 1');
  });
});
