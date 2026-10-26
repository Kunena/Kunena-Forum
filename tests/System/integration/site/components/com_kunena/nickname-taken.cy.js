// A nickname is not the name of another member: when the forum shows the nicknames, a user could take the username, the real
// name or the nickname of another member (an administrator) as his nickname, and his posts carried that name.
describe('Kunena nicknames: not the name of another member', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};
  const old = {};

  const setOption = (name, value) => cy.kunena_setOption(name, value).then((previous) => {
    if (!(name in old)) {
      old[name] = previous;
    }
  });
  const save = (nickname) => cy.request(`${base}&view=home`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ method: 'POST', url: `${base}&view=user&task=save`, form: true, body: { userid: user.id, nickname, [token]: '1' }, failOnStatusCode: false, followRedirect: false });
  });
  const nicknameOf = (userid) => cy.task('queryDB', `SELECT value FROM #__fields_values WHERE field_id = ${ids.field} AND item_id = '${userid}'`).then((rows) => rows[0]?.value ?? null);

  beforeEach(() => {
    // A text field of the users for the nickname, and the forum shows the nicknames.
    cy.task(
      'queryDB',
      `INSERT INTO #__fields (asset_id, context, group_id, title, name, label, default_value, type, note, description, state, required, only_use_in_subform, ordering, params, fieldparams, language, created_time, created_user_id, modified_time, modified_by, access) VALUES (0, 'com_users.user', 0, 'Nickname', 'cypress-nick-${stamp}', 'Nickname', '', 'text', '', '', 1, 0, 0, 0, '{}', '{}', '*', NOW(), 797, NOW(), 0, 1)`,
    ).then((field) => {
      ids.field = field.insertId;
      setOption('displayNameField', String(field.insertId));
      setOption('username', '2');
    });
  });

  afterEach(() => {
    Object.entries(old).forEach(([name, value]) => cy.kunena_setOption(name, value === undefined ? null : value));
    Object.keys(old).forEach((name) => delete old[name]);
    cy.task('queryDB', `DELETE FROM #__fields_values WHERE field_id = ${ids.field}`);
    cy.task('queryDB', `DELETE FROM #__fields WHERE id = ${ids.field}`);
  });

  it('refuses the username of another member as a nickname, and keeps a nickname of nobody else', () => {
    cy.doFrontendLogin(user.name, user.password, false);

    save('CI-ADMIN');
    nicknameOf(user.id).should('not.eq', 'CI-ADMIN');

    save(`Night Owl ${stamp}`);
    nicknameOf(user.id).should('eq', `Night Owl ${stamp}`);
  });
});
