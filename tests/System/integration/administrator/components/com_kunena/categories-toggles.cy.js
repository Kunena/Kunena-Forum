// The toggles of the backend category list (tasks categories.lock/unlock, review/unreview, allowanonymous/denyanonymous,
// allowpolls/denypolls, publish/unpublish): each one sets its column of the chosen category, and the other columns stay.
describe('Kunena backend categories: toggles', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const columns = ['locked', 'review', 'allowAnonymous', 'allowPolls', 'published'];
  const state = () => cy.task('queryDB', `SELECT ${columns.join(', ')} FROM #__kunena_categories WHERE id = ${ids.category}`).then((rows) => Object.fromEntries(columns.map((column) => [column, Number(rows[0][column])])));
  const send = (task) => cy.request(`${admin}&view=categories`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ method: 'POST', url: `${admin}&task=categories.${task}`, form: true, body: { 'cid[]': ids.category, [token]: '1' }, followRedirect: false });
  });

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset, locked, review, allowAnonymous, allowPolls) VALUES (1, 'Cypress toggles ${stamp}', 'cypress-toggles-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default', 0, 0, 0, 0)`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-toggles-${stamp}', 'catid', ${category.insertId}, 0)`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-toggles-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('sets and clears each column of the category on its own', () => {
    const off = { locked: 0, review: 0, allowAnonymous: 0, allowPolls: 0, published: 1 };
    cy.doAdministratorLogin();
    [['lock', 'unlock', 'locked', 1], ['review', 'unreview', 'review', 1], ['allowanonymous', 'denyanonymous', 'allowAnonymous', 1], ['allowpolls', 'denypolls', 'allowPolls', 1], ['unpublish', 'publish', 'published', 0]].forEach(([on, back, column, value]) => {
      cy.then(() => send(on));
      cy.then(() => state()).should('deep.eq', { ...off, [column]: value });
      cy.then(() => send(back));
      cy.then(() => state()).should('deep.eq', off);
    });
  });
});
