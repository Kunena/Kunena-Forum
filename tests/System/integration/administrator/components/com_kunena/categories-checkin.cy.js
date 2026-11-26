// The lock of a checked-out category in the backend category list (task category.checkin): the category is checked in. The task
// was an empty placeholder which only went back to the list, so a category stayed checked out (Joomla's Global Check-in was the only way).
describe('Kunena backend categories: check-in', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const name = `Cypress checkin ${stamp}`;
  const ids = {};
  const checkedOut = () => cy.task('queryDB', `SELECT checked_out FROM #__kunena_categories WHERE id = ${ids.category}`).then((rows) => Number(rows[0].checked_out || 0));

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset, checked_out, checked_out_time) SELECT 1, '${name}', 'cypress-checkin-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default', id, NOW() FROM #__users WHERE username = 'ci-admin'`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-checkin-${stamp}', 'catid', ${category.insertId}, 0)`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-checkin-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('checks the category in with the lock of its row', () => {
    cy.doAdministratorLogin();
    cy.then(() => checkedOut()).should('be.greaterThan', 0);

    cy.visit(`${admin}&view=categories&filter[search]=${encodeURIComponent(name)}`);
    cy.contains('tr', name).find('[data-item-task="category.checkin"]').click();

    cy.get('#system-message-container').should('contain', '1 categories checked in');
    cy.then(() => checkedOut()).should('eq', 0);
  });
});
