// In a category where the users only see their own topics (option ownTopicsOnly, a helpdesk), a user who follows the category
// must not be told about the topics of the other users: they are not his to read. The notifications go to the same users as the
// e-mails (the inbox plugin writes one for each of them), so they show who got the e-mail.
describe('Kunena own topics only: no notifications about the topics of others', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const subject = `Helpdesk question ${stamp}`;
  const ids = {};

  const notifications = (userid) => cy.task('queryDB', `SELECT id FROM #__kunena_inbox WHERE user_id = ${userid} AND type <> 'message' AND subject LIKE '%${stamp}%'`).then((rows) => rows.length);

  beforeEach(() => {
    // A second user, who asks a question in the helpdesk.
    cy.task(
      'queryDB',
      "INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Asker', 'ci-asker', 'ci-asker@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798",
    ).then((result) => {
      ids.second = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${ids.second}, 2)`);
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress helpdesk ${stamp}', 'cypress-helpdesk-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{"ownTopicsOnly":"1"}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-helpdesk-${stamp}', 'catid', ${category.insertId}, 0)`);
      // ci-user and the administrator (who may read every topic) follow the category.
      cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${user.id}, ${category.insertId}, 0, 0, 1, '')`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (797, ${category.insertId}, 0, 0, 1, '')`);
    });
    // The flood protection goes by the address: the posts of the last minutes of the tests are moved out of its time.
    cy.task('queryDB', "UPDATE #__kunena_messages SET time = time - 3600 WHERE ip = '127.0.0.1' AND time > UNIX_TIMESTAMP() - 600");
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${ids.category}`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_inbox WHERE subject LIKE '%${stamp}%'`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-helpdesk-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_sessions WHERE userid = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE user_id = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE user_id = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.second}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.second}`);
    cy.kunena_fixUserCounters();
  });

  it('does not notify a follower of the category about the topic of another user', () => {
    cy.doFrontendLogin('ci-asker', user.password, false);
    cy.request(`${base}&view=home`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: { catid: ids.category, subject, message: `My private question ${stamp}.`, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.then(() => cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${ids.category}`)).its('length').should('eq', 1);

    // The administrator gets his notification (the test sees that they are made); ci-user, who may not read it, gets none.
    cy.then(() => notifications(797)).should('eq', 1);
    cy.then(() => notifications(user.id)).should('eq', 0);
  });
});
