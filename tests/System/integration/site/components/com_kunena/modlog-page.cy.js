// The moderation log of the site (view=topic&layout=modlog): a visitor and a user who moderates nothing get no log, a moderator of
// a category sees the actions in his categories only, the administrator sees all of them, and the filter on the action works.
describe('Kunena moderation log page', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};
  const page = () => cy.request({ url: `${base}&view=topic&layout=modlog`, failOnStatusCode: false });

  const addCategory = (name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, '${name} ${stamp}', '${name}-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
  ).then((category) => {
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('${name}-${stamp}', 'catid', ${category.insertId}, 0)`);

    return cy.wrap(category.insertId);
  });
  // A moderation action of the administrator in a category (the rows have an id: the queryDB task removes them).
  const addLog = (category, operation) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_logs (type, user_id, category_id, topic_id, target_user, ip, time, operation, data) VALUES (1, 797, ${category}, 0, 0, '', UNIX_TIMESTAMP(), '${operation}', '{}')`,
  );

  beforeEach(() => {
    addCategory('cypress-modlog-mine').then((id) => {
      ids.mine = id;
      addLog(id, 'LOG_TOPIC_LOCK');
      // ci-user is a moderator of this category only.
      cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${user.id}, ${id}, 1, 0, 0, '')`);
    });
    addCategory('cypress-modlog-other').then((id) => {
      ids.other = id;
      addLog(id, 'LOG_TOPIC_STICKY');
    });
  });

  afterEach(() => {
    ['mine', 'other'].forEach((name) => {
      cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-modlog-${name}-${stamp}'`);
      cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids[name]}`);
    });
  });

  it('gives a visitor no log', () => {
    page().its('status').should('eq', 401);
  });

  it('shows a moderator the actions in his category and not those in another one', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    page().then((response) => {
      expect(response.status).to.eq(200);
      expect(String(response.body), 'his category').to.include(`<td>cypress-modlog-mine ${stamp}</td>`);
      expect(String(response.body), 'another category').to.not.include(`<td>cypress-modlog-other ${stamp}</td>`);
    });
  });

  it('gives a user who moderates nothing no log', () => {
    cy.then(() => cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${user.id} AND category_id = ${ids.mine}`));
    cy.doFrontendLogin(user.name, user.password, false);
    page().its('status').should('eq', 403);
  });

  it('shows the administrator all actions, and filters them on the action', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    page().its('body').should('include', `<td>cypress-modlog-mine ${stamp}</td>`).and('include', `<td>cypress-modlog-other ${stamp}</td>`);
    cy.request(`${base}&view=topic&layout=modlog&fop=LOG_TOPIC_STICKY`).its('body').then((body) => {
      expect(String(body), 'the sticky action').to.include(`<td>cypress-modlog-other ${stamp}</td>`);
      expect(String(body), 'the lock action').to.not.include(`<td>cypress-modlog-mine ${stamp}</td>`);
    });
  });
});
