// The rating of a topic (task topic.loadrate, which the star widget asks for) is only given for a topic which the reader may see:
// the average of an unapproved topic, or of a topic in a category closed to the reader, is not sent.
describe('Kunena ratings: only of topics the reader may see', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};

  const addTopic = (category, hold) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category}, 'Cypress rating ${stamp}', 0, 0, 0, ${hold}, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
  ).then((topic) => {
    // Three ratings of 4 (the rows have an id: the task of the database removes them after the test).
    [796, 797, 798].forEach((userid) => cy.task('queryDB', `INSERT INTO #__kunena_rate (topic_id, userid, rate, time) VALUES (${topic.insertId}, ${userid}, 4, NOW())`));

    return cy.wrap(topic.insertId);
  });

  const loadrate = (topic) => cy.request(`${base}&view=home`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({ url: `${base}&view=topic&task=loadrate&format=json&topic_id=${topic}&${token}=1`, failOnStatusCode: false });
  });

  beforeEach(() => {
    // A public category and a category for the access level Special (managers and administrators).
    [['public', 1], ['closed', 3]].forEach(([name, access]) => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress rating ${name} ${stamp}', 'cypress-rating-${name}-${stamp}', '', '', '', '', '', 'joomla.level', ${access}, 1, 1, 8, 1, 95, 1, '{}', 'default')`,
    ).then((category) => {
      ids[name] = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-rating-${name}-${stamp}', 'catid', ${category.insertId}, 0)`);
    }));
    cy.then(() => addTopic(ids.public, 0)).then((topic) => {
      ids.visible = topic;
    });
    cy.then(() => addTopic(ids.public, 1)).then((topic) => {
      ids.unapproved = topic;
    });
    cy.then(() => addTopic(ids.closed, 0)).then((topic) => {
      ids.restricted = topic;
    });
  });

  afterEach(() => {
    if ('ratingEnabled' in ids) {
      cy.kunena_setOption('ratingEnabled', ids.ratingEnabled === undefined ? null : ids.ratingEnabled);
      delete ids.ratingEnabled;
    }
    ['public', 'closed'].forEach((name) => {
      cy.task('queryDB', `DELETE FROM #__kunena_rate WHERE topic_id IN (SELECT id FROM #__kunena_topics WHERE category_id = ${ids[name]})`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-rating-${name}-${stamp}'`);
      cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids[name]}`);
    });
  });

  it('gives a visitor the rating of a visible topic, and not of an unapproved or closed one', () => {
    cy.then(() => loadrate(ids.visible)).its('body').should('deep.include', { data: 4 });
    cy.then(() => loadrate(ids.unapproved)).its('body.data').should('not.eq', 4);
    cy.then(() => loadrate(ids.restricted)).its('body.data').should('not.eq', 4);
  });

  it('lets a user rate an open topic, and not a locked one (the rule "rate" of the topic)', () => {
    const userRating = (topic) => cy.task('queryDB', `SELECT rate FROM #__kunena_rate WHERE topic_id = ${topic} AND userid = 798`).then((rows) => rows.length);
    const rate = (topic) => cy.request(`${base}&view=home`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

      return cy.request({ url: `${base}&view=topic&task=setrate&format=json&topic_id=${topic}&starid=5&${token}=1`, failOnStatusCode: false });
    });

    cy.kunena_setOption('ratingEnabled', '1').then((previous) => {
      ids.ratingEnabled = previous;
    });
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET allowRatings = 1 WHERE id = ${ids.public}`));
    // ci-user has not rated the topics yet; the second one is locked.
    cy.then(() => cy.task('queryDB', `DELETE FROM #__kunena_rate WHERE userid = 798 AND topic_id IN (${ids.visible}, ${ids.unapproved})`));
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_topics SET hold = 0, locked = 1 WHERE id = ${ids.unapproved}`));
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);

    cy.then(() => rate(ids.visible));
    cy.then(() => userRating(ids.visible)).should('eq', 1);
    cy.then(() => rate(ids.unapproved));
    cy.then(() => userRating(ids.unapproved)).should('eq', 0);
  });
});
