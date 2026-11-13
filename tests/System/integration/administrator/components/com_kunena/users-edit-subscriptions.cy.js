// The edit page of a user in the backend removes the checked subscriptions of that user (tasks user.removecategorysubscriptions
// and user.removetopicsubscriptions): only the checked rows of this user end, the other subscriptions and the other fields stay.
describe('Kunena backend user edit: remove chosen subscriptions', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const edit = (userid) => `${admin}&view=user&layout=edit&userid=${userid}`;
  const subscribed = (table, column, userid, id) => cy.task('queryDB', `SELECT subscribed FROM #__kunena_${table} WHERE user_id = ${userid} AND ${column} = ${id}`).then((rows) => Number(rows[0].subscribed));
  const counts = () => cy.task(
    'queryDB',
    `SELECT (SELECT COUNT(*) FROM #__kunena_user_categories WHERE user_id IN (${ids.user}, ${ids.other})) AS cats, (SELECT COUNT(*) FROM #__kunena_user_topics WHERE user_id IN (${ids.user}, ${ids.other})) AS topics, (SELECT COALESCE(SUM(subscribed), 0) FROM #__kunena_user_categories WHERE user_id IN (${ids.user}, ${ids.other})) AS catsubs, (SELECT COALESCE(SUM(subscribed), 0) FROM #__kunena_user_topics WHERE user_id IN (${ids.user}, ${ids.other})) AS topicsubs`,
  ).then((rows) => ({ cats: Number(rows[0].cats), topics: Number(rows[0].topics), catsubs: Number(rows[0].catsubs), topicsubs: Number(rows[0].topicsubs) }));
  // Posts the task with the pairs given (a list, so that a field can repeat), with the token of the form unless token is false.
  const send = (task, pairs, { token = true, followRedirect = false } = {}) => cy.request(`${admin}&view=users`).then((page) => {
    const params = new URLSearchParams();

    params.append('task', `user.${task}`);
    pairs.forEach(([name, value]) => params.append(name, String(value)));

    if (token) {
      params.append(/name="([0-9a-f]{32})" value="1"/.exec(page.body)[1], '1');
    }

    return cy.request({
      method: 'POST',
      url: `${admin}&view=users`,
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: params.toString(),
      followRedirect,
    });
  });
  const addUser = (name) => cy.task(
    'queryDB',
    `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI ${name}', 'ci-${name}-${stamp}', 'ci-${name}-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
  ).then((result) => {
    cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
    cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts, signature) VALUES (${result.insertId}, 0, 'ci-signature')`);

    return cy.wrap(result.insertId);
  });

  beforeEach(() => {
    Object.keys(ids).forEach((key) => delete ids[key]);
    // Two existing topics: the temporary users subscribe to them, the topics themselves do not change.
    cy.task('queryDB', 'SELECT id, category_id FROM #__kunena_topics WHERE moved_id = 0 AND hold = 0 ORDER BY id LIMIT 2').then((rows) => {
      ids.topics = rows.map((row) => ({ id: Number(row.id), category: Number(row.category_id) }));
    });
    addUser('editsubs').then((id) => {
      ids.user = id;
      cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${id}, 3, 0, 0, 1, ''), (${id}, 4, 0, 0, 1, '')`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${id}, ${ids.topics[0].id}, ${ids.topics[0].category}, 0, 0, 0, 1, 1, ''), (${id}, ${ids.topics[1].id}, ${ids.topics[1].category}, 0, 0, 0, 0, 1, '')`);
    });
    // The second user is subscribed to category 3 only and to the first topic.
    addUser('editsubs-other').then((id) => {
      ids.other = id;
      cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${id}, 3, 0, 0, 1, '')`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${id}, ${ids.topics[0].id}, ${ids.topics[0].category}, 0, 0, 0, 0, 1, '')`);
    });
    // A user without any subscription.
    addUser('editsubs-none').then((id) => {
      ids.none = id;
    });
    cy.doAdministratorLogin();
  });

  afterEach(() => {
    // A user which a failed beforeEach did not create has no id.
    [ids.user, ids.other, ids.none].filter(Boolean).forEach((id) => {
      cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${id}`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE user_id = ${id}`);
      cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${id}`);
      cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${id}`);
      cy.task('queryDB', `DELETE FROM #__users WHERE id = ${id}`);
    });
  });

  it('shows a checkbox for every subscription and the two remove buttons', () => {
    cy.then(() => cy.visit(edit(ids.user)));
    cy.get('input[type=checkbox][name="catsubs[]"]').should('have.length', 2);
    cy.get('input[type=checkbox][name="catsubs[]"][value="3"]').should('exist');
    cy.get('input[type=checkbox][name="catsubs[]"][value="4"]').should('exist');
    cy.get('input[type=checkbox][name="topicsubs[]"]').should('have.length', 2);
    cy.then(() => cy.get(`input[type=checkbox][name="topicsubs[]"][value="${ids.topics[0].id}"]`).should('exist'));
    cy.get('button[onclick*="user.removecategorysubscriptions"]').should('have.length', 1);
    cy.get('button[onclick*="user.removetopicsubscriptions"]').should('have.length', 1);
  });

  it('shows no checkbox and no button for a user without subscriptions', () => {
    cy.then(() => cy.visit(edit(ids.none)));
    cy.get('input[name="catsubs[]"]').should('not.exist');
    cy.get('input[name="topicsubs[]"]').should('not.exist');
    cy.get('button[onclick*="user.removecategorysubscriptions"]').should('not.exist');
    cy.get('button[onclick*="user.removetopicsubscriptions"]').should('not.exist');
    cy.get('#tab4').should('contain.text', 'No categories subscribed');
    cy.get('#tab5').should('contain.text', 'No subscriptions found for this user');
  });

  it('removes the checked category subscription of this user only', () => {
    cy.then(() => send('removecategorysubscriptions', [['uid', ids.user], ['catsubs[]', 3]])).then((response) => {
      expect(response.status).to.be.oneOf([302, 303]);
      expect(response.headers.location).to.include(`view=user&layout=edit&userid=${ids.user}`);
    });
    cy.then(() => subscribed('user_categories', 'category_id', ids.user, 3)).should('eq', 0);
    cy.then(() => subscribed('user_categories', 'category_id', ids.user, 4)).should('eq', 1);
    cy.then(() => subscribed('user_categories', 'category_id', ids.other, 3)).should('eq', 1);
    cy.then(() => subscribed('user_topics', 'topic_id', ids.user, ids.topics[0].id)).should('eq', 1);
    cy.then(() => subscribed('user_topics', 'topic_id', ids.user, ids.topics[1].id)).should('eq', 1);
  });

  it('removes the checked topic subscription and keeps the favorite', () => {
    cy.then(() => send('removetopicsubscriptions', [['uid', ids.user], ['topicsubs[]', ids.topics[0].id]]));
    cy.then(() => cy.task('queryDB', `SELECT subscribed, favorite FROM #__kunena_user_topics WHERE user_id = ${ids.user} AND topic_id = ${ids.topics[0].id}`)).then((rows) => {
      expect(Number(rows[0].subscribed)).to.eq(0);
      expect(Number(rows[0].favorite)).to.eq(1);
    });
    cy.then(() => subscribed('user_topics', 'topic_id', ids.user, ids.topics[1].id)).should('eq', 1);
    cy.then(() => subscribed('user_topics', 'topic_id', ids.other, ids.topics[0].id)).should('eq', 1);
    cy.then(() => subscribed('user_categories', 'category_id', ids.user, 3)).should('eq', 1);
    cy.then(() => subscribed('user_categories', 'category_id', ids.user, 4)).should('eq', 1);
  });

  it('ignores the ids which are not subscriptions of this user', () => {
    // The second user posts category 4 and the second topic: subscriptions of the first user, not of the second one.
    cy.then(() => counts()).then((before) => {
      cy.then(() => send('removecategorysubscriptions', [['uid', ids.other], ['catsubs[]', 4], ['catsubs[]', 987654321], ['catsubs[]', 'abc']], { followRedirect: true }))
        .its('body').should('include', 'No subscriptions were selected.');
      cy.then(() => send('removetopicsubscriptions', [['uid', ids.other], ['topicsubs[]', ids.topics[1].id], ['topicsubs[]', 987654321], ['topicsubs[]', 'abc']], { followRedirect: true }))
        .its('body').should('include', 'No subscriptions were selected.');
      cy.then(() => counts()).should('deep.equal', before);
    });
  });

  it('warns when nothing is checked', () => {
    cy.then(() => counts()).then((before) => {
      cy.then(() => send('removecategorysubscriptions', [['uid', ids.user]], { followRedirect: true })).then((response) => {
        expect(response.redirects.join(' ')).to.include(`userid=${ids.user}`);
        expect(response.body).to.include('No subscriptions were selected.');
      });
      cy.then(() => send('removetopicsubscriptions', [['uid', ids.user]], { followRedirect: true }))
        .its('body').should('include', 'No subscriptions were selected.');
      cy.then(() => counts()).should('deep.equal', before);
    });
  });

  it('shows the success message and does not save the other fields', () => {
    cy.then(() => send('removecategorysubscriptions', [['uid', ids.user], ['catsubs[]', 4], ['signature', 'changed-by-test']], { followRedirect: true }))
      .its('body').should('include', 'The selected category subscriptions have been removed.');
    cy.then(() => send('removetopicsubscriptions', [['uid', ids.user], ['topicsubs[]', ids.topics[1].id], ['signature', 'changed-by-test']], { followRedirect: true }))
      .its('body').should('include', 'The selected topic subscriptions have been removed.');
    cy.then(() => cy.task('queryDB', `SELECT signature FROM #__kunena_users WHERE userid = ${ids.user}`)).then((rows) => {
      expect(rows[0].signature).to.eq('ci-signature');
    });
  });

  it('changes nothing without a valid token', () => {
    cy.then(() => counts()).then((before) => {
      cy.then(() => send('removecategorysubscriptions', [['uid', ids.user], ['catsubs[]', 3], ['catsubs[]', 4]], { token: false }));
      cy.then(() => send('removetopicsubscriptions', [['uid', ids.user], ['topicsubs[]', ids.topics[0].id], ['topicsubs[]', ids.topics[1].id]], { token: false }));
      cy.then(() => counts()).should('deep.equal', before);
    });
  });
});
