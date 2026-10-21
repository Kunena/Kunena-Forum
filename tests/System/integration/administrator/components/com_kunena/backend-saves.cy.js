// Saving a form of the backend without a change must not change the data: the configuration, a category and the profile of a user
// are saved as they are shown, and the database is compared before and after. A field which the form does not send (or sends wrong)
// shows as a difference. The afterEach of the support file fails a test for a PHP notice or warning on a page.
describe('Kunena backend: saving a form without a change', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const userId = 798;
  const rowOf = (sql) => cy.task('queryDB', sql).then((rows) => JSON.parse(JSON.stringify(rows[0])));
  const apply = () => {
    cy.get('#toolbar-apply button', { timeout: 15000 }).should('be.visible').click();
    cy.get('joomla-alert, #system-message-container', { timeout: 15000 }).should('contain.text', 'ed');
  };

  beforeEach(() => {
    cy.doAdministratorLogin();
  });

  it('saves the configuration as it is shown', () => {
    const sql = "SELECT params FROM #__extensions WHERE element = 'com_kunena' AND type = 'component'";

    rowOf(sql).then((before) => {
      cy.visit('/administrator/index.php?option=com_config&view=component&component=com_kunena');
      apply();

      rowOf(sql).then((after) => {
        const was = JSON.parse(before.params);
        const now = JSON.parse(after.params);
        const different = Object.keys({ ...was, ...now }).filter((key) => String(was[key] ?? '') !== String(now[key] ?? ''));

        expect(different, 'the options which the save has changed').to.deep.eq([]);
      });
    });
  });

  it('saves a category as it is shown', () => {
    rowOf('SELECT id FROM #__kunena_categories WHERE parentid > 0 ORDER BY id LIMIT 1').then(({ id }) => {
      const sql = `SELECT name, alias, icon, icon_id, description, headerdesc, topictemplate, class_sfx, published, ordering, locked, review, allowAnonymous, postAnonymous, allowPolls, allowRatings, topicOrdering, iconset, channels, pubAccess, pubRecurse, adminAccess, adminRecurse, params, accesstype, access, checked_out FROM #__kunena_categories WHERE id = ${id}`;

      rowOf(sql).then((before) => {
        cy.visit(`${admin}&view=category&layout=edit&catid=${id}`);
        apply();

        rowOf(sql).then((after) => {
          const different = Object.keys(before).filter((key) => String(before[key]) !== String(after[key]));

          expect(different, 'the columns which the save has changed').to.deep.eq([]);
        });
      });
    });
  });

  it('saves the profile of a user as it is shown', () => {
    const fields = `status_text, signature, personalText, gender, CAST(birthdate AS CHAR) AS birthdate, location, websitename, websiteurl, hideEmail, showOnline, canSubscribe, userListtime`;
    const sql = `SELECT ${fields} FROM #__kunena_users WHERE userid = ${userId}`;

    // A profile with values in all the fields (the old values come back after the test).
    rowOf(sql).then((old) => {
      cy.task('queryDB', `UPDATE #__kunena_users SET status_text = 'busy', signature = '[b]sig[/b]', personalText = 'about me', gender = 1, birthdate = '1990-05-17', location = 'Amsterdam', websitename = 'My site', websiteurl = 'https://example.com', hideEmail = 0, showOnline = 0, canSubscribe = 1, userListtime = 24 WHERE userid = ${userId}`);

      rowOf(sql).then((before) => {
        cy.visit(`${admin}&view=user&layout=edit&userid=${userId}`);
        apply();

        rowOf(sql).then((after) => {
          const different = Object.keys(before).filter((key) => String(before[key]) !== String(after[key]));

          const restore = Object.entries(old).map(([key, value]) => `${key} = ${value === null ? 'NULL' : `'${String(value).replaceAll('\\', '\\\\').replaceAll("'", "\\'")}'`}`).join(', ');

          cy.task('queryDB', `UPDATE #__kunena_users SET ${restore} WHERE userid = ${userId}`).then(() => {
            expect(different, 'the columns which the save has changed').to.deep.eq([]);
          });
        });
      });
    });
  });
});
