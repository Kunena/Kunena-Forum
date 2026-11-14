// The Joomla user group filter of the backend user list (filter[group_id]): it keeps the users who are a direct member of the
// group, each once, together with the search and every ordering. The filter bar stays open while a group is selected, the
// user state remembers the selection, and a value that is not a group id gives no error.
describe('Kunena backend users: filter by Joomla user group', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const search = `ci-group-${stamp}`;
  const ids = {};
  // The user list with the given group filter (and the search on the two test users unless another one is given).
  const list = (group, extra = '') => `${admin}&view=users&filter[search]=${search}&filter[group_id]=${group}&list[fullordering]=username ASC${extra}`;
  const rows = () => cy.get('#userList tbody tr');
  const addUser = (key, groups) => cy.task(
    'queryDB',
    `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Group ${key}', '${search}-${key}', '${search}-${key}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
  ).then((result) => {
    ids[key] = result.insertId;
    groups().forEach((group) => cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, ${group})`));
    cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts) VALUES (${result.insertId}, 0)`);
  });

  beforeEach(() => {
    cy.task('queryDB', `INSERT INTO #__usergroups (parent_id, lft, rgt, title) VALUES (1, 0, 0, 'CI Group ${stamp}')`).then((result) => {
      ids.group = result.insertId;
    });
    addUser('a', () => [ids.group, 2]);
    addUser('b', () => [2]);
    cy.doAdministratorLogin();
  });

  afterEach(() => {
    // The session is shared with the other specs: forget the filter and the ordering of this spec.
    cy.request(`${admin}&view=users&filter[search]=&filter[group_id]=&list[fullordering]=username ASC`);
    ['a', 'b'].forEach((key) => {
      cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids[key]}`);
      cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids[key]}`);
      cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids[key]}`);
    });
    cy.task('queryDB', `DELETE FROM #__usergroups WHERE id = ${ids.group}`);
  });

  it('lists every Joomla user group in the filter bar', () => {
    cy.visit(list(''));
    cy.get('label[for="filter_group_id"]').should('contain', 'Joomla User Group');
    cy.get('#filter_group_id option').first().should('have.value', '').and('have.text', '- Select Joomla User Group -');
    cy.then(() => cy.get(`#filter_group_id option[value="${ids.group}"]`).should('contain', `CI Group ${stamp}`));
    cy.task('queryDB', 'SELECT COUNT(*) AS total FROM #__usergroups').then((result) => {
      cy.get('#filter_group_id option').should('have.length', Number(result[0].total) + 1);
    });
  });

  it('lists the members of the selected group once', () => {
    cy.visit(list(''));
    rows().should('have.length', 2);
    cy.get('.js-stools-btn-filter').click();
    cy.then(() => cy.get('#filter_group_id').select(String(ids.group)));
    cy.then(() => cy.get('#filter_group_id').should('have.value', String(ids.group)));
    rows().should('have.length', 1).and('contain', `${search}-a`);
    cy.get('#userList').should('not.contain', `${search}-b`);
    cy.get('.pagination__wrapper').should('contain', '1 - 1 / 1 items');
  });

  it('combines the group with the search and the ordering', () => {
    cy.then(() => cy.visit(list(ids.group).replace(`filter[search]=${search}`, `filter[search]=${search}-b`)));
    rows().should('not.exist');
    cy.then(() => cy.visit(list(ids.group).replace('username ASC', 'email DESC')));
    rows().should('have.length', 1).and('contain', `${search}-a`);
    cy.get('.pagination__wrapper').should('contain', '1 - 1 / 1 items');
    cy.then(() => cy.visit(list(ids.group).replace('username ASC', 'banned DESC')));
    rows().should('have.length', 1).and('contain', `${search}-a`);
  });

  it('keeps the filter bar open and remembers the group', () => {
    cy.then(() => cy.visit(list(ids.group)));
    cy.get('.js-stools-container-filters').should('have.class', 'js-stools-container-filters-visible');
    cy.then(() => cy.get('#filter_group_id').should('have.value', String(ids.group)));
    cy.visit(`${admin}&view=users`);
    cy.get('.js-stools-container-filters').should('have.class', 'js-stools-container-filters-visible');
    cy.then(() => cy.get('#filter_group_id').should('have.value', String(ids.group)));
    rows().should('have.length', 1).and('contain', `${search}-a`);
  });

  it('ignores a value which is not a group id and lists nobody for an unknown group', () => {
    ['abc', ''].forEach((value) => {
      cy.request(list(value)).its('status').should('eq', 200);
      cy.visit(list(value));
      cy.get('#system-message-container joomla-alert[type="danger"]').should('not.exist');
      rows().should('have.length', 2);
      cy.get('#userList').should('contain', `${search}-a`).and('contain', `${search}-b`);
    });
    cy.request(list(999999999)).its('status').should('eq', 200);
    cy.visit(list(999999999));
    cy.get('#system-message-container joomla-alert[type="danger"]').should('not.exist');
    rows().should('not.exist');
  });

  it('lists the other users again after Clear', () => {
    cy.then(() => cy.visit(list(ids.group)));
    rows().should('have.length', 1);
    cy.get('.js-stools-btn-clear').click();
    cy.get('#filter_group_id').should('have.value', '');
    // The remembered filter has no group any more: a search on the remembered filters lists both users.
    cy.visit(`${admin}&view=users`);
    cy.get('#filter_group_id').should('have.value', '');
    cy.get('#filter_search').clear().type(`${search}{enter}`);
    rows().should('have.length', 2);
    cy.get('#userList').should('contain', `${search}-a`).and('contain', `${search}-b`);
  });
});
