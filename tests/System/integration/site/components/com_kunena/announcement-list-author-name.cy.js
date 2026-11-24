// The list of the announcements (for the moderators) named their author by his login name when the forum shows the nicknames
// (username = 2), and by his real name only otherwise: the column tested the option for "username" and took the login name for
// any other value than 0. It shows the name which the forum shows everywhere else now.
describe('Kunena announcements: the name of the author on the list', () => {
  const stamp = Date.now();
  const nickname = `Captain ${stamp}`;
  const ids = {};
  const old = {};

  const setOption = (name, value) => cy.kunena_setOption(name, value).then((previous) => {
    if (!(name in old)) {
      old[name] = previous;
    }
  });

  beforeEach(() => {
    // A text field of the users for the nickname, the forum shows the nicknames, and ci-admin has one.
    cy.task(
      'queryDB',
      `INSERT INTO #__fields (asset_id, context, group_id, title, name, label, default_value, type, note, description, state, required, only_use_in_subform, ordering, params, fieldparams, language, created_time, created_user_id, modified_time, modified_by, access) VALUES (0, 'com_users.user', 0, 'Nickname', 'cypress-annnick-${stamp}', 'Nickname', '', 'text', '', '', 1, 0, 0, 0, '{}', '{}', '*', NOW(), 797, NOW(), 0, 1)`,
    ).then((field) => {
      ids.field = field.insertId;
      setOption('displayNameField', String(field.insertId));
      setOption('username', '2');
      cy.task('queryDB', `INSERT INTO #__fields_values (field_id, item_id, value) VALUES (${field.insertId}, '797', '${nickname}')`);
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_announcement (title, created_by, sdescription, description, created, published, publish_up, publish_down, ordering, showdate) VALUES ('Cypress announcement name ${stamp}', 797, 'Short', 'Long', NOW(), 1, NOW(), '1000-01-01 00:00:00', 0, 1)`,
    ).then((announcement) => {
      ids.announcement = announcement.insertId;
    });
  });

  afterEach(() => {
    Object.entries(old).forEach(([name, value]) => cy.kunena_setOption(name, value === undefined ? null : value));
    Object.keys(old).forEach((name) => delete old[name]);
    cy.task('queryDB', `DELETE FROM #__kunena_announcement WHERE id = ${ids.announcement}`);
    cy.task('queryDB', `DELETE FROM #__fields_values WHERE field_id = ${ids.field}`);
    cy.task('queryDB', `DELETE FROM #__fields WHERE id = ${ids.field}`);
  });

  it('shows the nickname of the author', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.request('/index.php?option=com_kunena&view=announcement&layout=listing').its('body').then((body) => {
      const text = String(body);
      const start = text.indexOf(`Cypress announcement name ${stamp}`);
      const row = text.slice(start, text.indexOf('</tr>', start));

      expect(start, 'the announcement on the list').to.be.greaterThan(-1);
      expect(row, 'the author').to.contain(nickname);
    });
  });
});
