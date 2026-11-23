// With IP tracking, a post stores the address of its author in his profile. An anonymous post has no address (it is removed so
// that nobody can tell who wrote it), and that empty address replaced the one which the profile had kept.
describe('Kunena anonymous: the stored address of the author', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const subject = `Cypress anonymous ip ${stamp}`;
  const ids = {};
  const old = {};

  beforeEach(() => {
    cy.kunena_setOption('ipTracking', '1').then((value) => {
      old.ipTracking = value ?? null;
    });
    cy.task('queryDB', `SELECT ip FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      old.ip = rows[0].ip;
      cy.task('queryDB', `UPDATE #__kunena_users SET ip = '10.9.8.7' WHERE userid = ${user.id}`);
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, allowAnonymous, postAnonymous, params, iconset) VALUES (1, 'Cypress anon ip ${stamp}', 'cypress-anon-ip-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, 1, 0, '{}', 'default')`,
    ).then((result) => {
      ids.category = result.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-anon-ip-${stamp}', 'catid', ${result.insertId}, 0)`);
    });
    // The flood protection: the posts of the last minutes of the tests are moved out of its time.
    cy.task('queryDB', "UPDATE #__kunena_messages SET time = time - 3600 WHERE time > UNIX_TIMESTAMP() - 600");
  });

  afterEach(() => {
    cy.kunena_setOption('ipTracking', old.ipTracking);
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET ip = ${old.ip === null ? 'NULL' : `'${old.ip}'`} WHERE userid = ${user.id}`));
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${ids.category}`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-anon-ip-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.kunena_fixUserCounters();
  });

  it('keeps the stored address when the member posts anonymously', () => {
    cy.doFrontendLogin(user.name, user.password, false);
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
    cy.then(() => cy.task('queryDB', `SELECT m.userid, m.ip FROM #__kunena_messages AS m INNER JOIN #__kunena_topics AS t ON t.id = m.thread WHERE t.category_id = ${ids.category}`)).then((rows) => {
      expect(rows, 'the anonymous topic').to.have.length(1);
      expect(Number(rows[0].userid), 'the user of the message').to.eq(0);
    });
    cy.task('queryDB', `SELECT ip FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      expect(rows[0].ip, 'the address in the profile').to.eq('10.9.8.7');
    });
  });
});
