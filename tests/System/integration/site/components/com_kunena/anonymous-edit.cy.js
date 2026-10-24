// A topic which its author makes anonymous afterwards (the edit form with "anonymous") must not show who wrote it: the message and
// the topic lose the author, and the lists of the topics of the user (started, posted, involved) do not show it to the others.
describe('Kunena anonymous: a topic made anonymous with an edit', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress later anonymous ${stamp}`;
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};

  const token = () => cy.request(`${base}&view=home`).then((page) => /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x');
  const send = (task, body) => token().then((value) => cy.request({
    method: 'POST',
    url: `${base}&view=topic&task=${task}`,
    form: true,
    body: { ...body, [value]: '1' },
    failOnStatusCode: false,
    followRedirect: false,
  }));

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, allowAnonymous, postAnonymous, params, iconset) VALUES (1, 'Cypress anon edit ${stamp}', 'cypress-anon-edit-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, 1, 0, '{}', 'default')`,
    ).then((result) => {
      ids.category = result.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-anon-edit-${stamp}', 'catid', ${result.insertId}, 0)`);
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
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-anon-edit-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.kunena_fixUserCounters();
  });

  it('hides the author of a topic which he makes anonymous with an edit', () => {
    cy.task('queryDB', `SELECT posts FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      ids.posts = Number(rows[0].posts);
    });
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => send('post', { catid: ids.category, subject, message: `First written with my name ${stamp}.` }));
    cy.task('queryDB', `SELECT id, first_post_id FROM #__kunena_topics WHERE category_id = ${ids.category}`).then((rows) => {
      expect(rows, 'the topic').to.have.length(1);
      ids.topic = rows[0].id;
      ids.message = rows[0].first_post_id;
    });
    cy.then(() => send('edit', { mesid: ids.message, catid: ids.category, id: ids.topic, subject, message: `Now anonymous ${stamp}.`, anonymous: 1 }));

    cy.then(() => cy.task('queryDB', `SELECT userid FROM #__kunena_messages WHERE id = ${ids.message}`)).its('0.userid').should('eq', 0);
    cy.then(() => cy.task('queryDB', `SELECT first_post_userid, last_post_userid FROM #__kunena_topics WHERE id = ${ids.topic}`)).its('0').should('deep.eq', { first_post_userid: 0, last_post_userid: 0 });

    // The topic is no longer one where the user wrote or which he started (his subscription may stay: it is private).
    cy.then(() => cy.task('queryDB', `SELECT posts, owner FROM #__kunena_user_topics WHERE topic_id = ${ids.topic} AND user_id = ${user.id}`)).then((rows) => {
      expect(rows.map((row) => ({ posts: Number(row.posts), owner: Number(row.owner) })), 'the row of the user for the topic').to.satisfy((list) => list.every((row) => row.posts === 0 && row.owner === 0));
    });

    // The post is no longer counted for him.
    cy.then(() => cy.task('queryDB', `SELECT posts FROM #__kunena_users WHERE userid = ${user.id}`)).then((rows) => {
      expect(Number(rows[0].posts), 'the number of posts of the user').to.eq(ids.posts);
    });

    // What the others see of the user: none of his lists has the topic.
    cy.clearAllCookies();
    ['default', 'started', 'posted'].forEach((mode) => {
      cy.request(`${base}&view=topics&layout=user&mode=${mode}&userid=${user.id}&sel=-1`).its('body').should('not.include', subject);
    });
  });
});
