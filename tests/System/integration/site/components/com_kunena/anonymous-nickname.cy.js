// An anonymous post of a logged-in user must not carry his nickname. When the forum shows the nicknames (a field of the Joomla
// users, option displayNameField), the name of an anonymous post was the nickname of its author: the form sends no name for a
// logged-in user, the default is his own name, and the check of the names only refused usernames and real names.
describe('Kunena anonymous: not under the nickname of the author', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const nickname = `Night Owl ${stamp}`;
  const subject = `Cypress anonymous nickname ${stamp}`;
  const ids = {};
  const old = {};

  const setOption = (name, value) => cy.kunena_setOption(name, value).then((previous) => {
    if (!(name in old)) {
      old[name] = previous;
    }
  });

  beforeEach(() => {
    // A text field of the users with the nickname of ci-user, and the forum shows the nicknames.
    cy.task(
      'queryDB',
      `INSERT INTO #__fields (asset_id, context, group_id, title, name, label, default_value, type, note, description, state, required, only_use_in_subform, ordering, params, fieldparams, language, created_time, created_user_id, modified_time, modified_by, access) VALUES (0, 'com_users.user', 0, 'Nickname', 'cypress-nickname-${stamp}', 'Nickname', '', 'text', '', '', 1, 0, 0, 0, '{}', '{}', '*', NOW(), 797, NOW(), 0, 1)`,
    ).then((field) => {
      ids.field = field.insertId;
      cy.task('queryDB', `INSERT INTO #__fields_values (field_id, item_id, value) VALUES (${field.insertId}, '${user.id}', '${nickname}')`);
      setOption('displayNameField', String(field.insertId));
      setOption('username', '2');
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, allowAnonymous, postAnonymous, params, iconset) VALUES (1, 'Cypress anon nick ${stamp}', 'cypress-anon-nick-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, 1, 0, '{}', 'default')`,
    ).then((result) => {
      ids.category = result.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-anon-nick-${stamp}', 'catid', ${result.insertId}, 0)`);
    });
    // The flood protection goes by the address: the posts of the last minutes of the tests are moved out of its time.
    cy.task('queryDB', "UPDATE #__kunena_messages SET time = time - 3600 WHERE ip = '127.0.0.1' AND time > UNIX_TIMESTAMP() - 600");
  });

  afterEach(() => {
    Object.entries(old).forEach(([name, value]) => cy.kunena_setOption(name, value === undefined ? null : value));
    Object.keys(old).forEach((name) => delete old[name]);
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${ids.category}`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-anon-nick-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__fields_values WHERE field_id = ${ids.field}`);
    cy.task('queryDB', `DELETE FROM #__fields WHERE id = ${ids.field}`);
    cy.kunena_fixUserCounters();
  });

  it('gives an anonymous post of a user with a nickname the anonymous name', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    // The forum shows his nickname (the setup works).
    cy.request(`${base}&view=user&userid=${user.id}`).its('body').should('include', nickname);

    cy.request(`${base}&view=home`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: { catid: ids.category, subject, message: `Written anonymously ${stamp}.`, anonymous: 1, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.then(() => cy.task('queryDB', `SELECT m.userid, m.name, t.first_post_guest_name FROM #__kunena_messages AS m INNER JOIN #__kunena_topics AS t ON t.id = m.thread WHERE t.category_id = ${ids.category}`)).then((rows) => {
      expect(rows, 'the anonymous topic').to.have.length(1);
      expect(Number(rows[0].userid), 'the user of the message').to.eq(0);
      expect(rows[0].name, 'the name of the message').to.not.eq(nickname);
      expect(rows[0].first_post_guest_name, 'the name in the topic').to.not.eq(nickname);
    });
    cy.clearAllCookies();
    cy.request(`${base}&view=category&catid=${ids.category}`).its('body').should('include', subject).and('not.include', nickname);
  });
});
