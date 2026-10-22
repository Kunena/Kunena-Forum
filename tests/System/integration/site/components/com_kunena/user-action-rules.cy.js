// The rules of the small actions of a user on a topic: a thank you (not for an own message, once), a rating (not for an own topic, once,
// only 1 to 5, only where it is allowed), the status "solved" (the starter of the topic and the moderators only) and a subscription
// or a favorite of a topic which the user may not read (none).
describe('Kunena user actions: the rules', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const old = {};
  const ids = {};

  const makeTopic = (category, author, name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category}, 'Cypress rules ${name} ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${author}, 'x', '', 0, UNIX_TIMESTAMP(), ${author}, 'x', '', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${category}, 'author', ${author}, '', 'Cypress rules ${name} ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
  )).then(() => ({ topic: topic.insertId, message: message.insertId, category }))));

  const tokenOf = () => cy.request(`${base}&view=topic&catid=3&id=9`).then((page) => /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x');
  const task = (set, name, extra = '') => tokenOf().then((token) => cy.request({ url: `${base}&view=topic&catid=${set.category}&id=${set.topic}&mesid=${set.message}&task=${name}${extra}&${token}=1`, failOnStatusCode: false, followRedirect: false }));
  const rate = (set, star) => tokenOf().then((token) => cy.request({ url: `${base}&view=topic&task=setrate&format=json&topic_id=${set.topic}&starid=${star}&${token}=1`, failOnStatusCode: false }));

  const thanks = (set) => cy.task('queryDB', `SELECT userid FROM #__kunena_thankyou WHERE postid = ${set.message}`);
  const rates = (set) => cy.task('queryDB', `SELECT userid, rate FROM #__kunena_rate WHERE topic_id = ${set.topic}`);
  const iconOf = (set) => cy.task('queryDB', `SELECT icon_id FROM #__kunena_topics WHERE id = ${set.topic}`).then((rows) => Number(rows[0].icon_id));
  const userTopic = (set) => cy.task('queryDB', `SELECT subscribed, favorite FROM #__kunena_user_topics WHERE topic_id = ${set.topic} AND user_id = ${user.id}`);

  before(() => {
    ['showThankYou', 'ratingEnabled', 'topicIcons'].forEach((name) => cy.kunena_setOption(name, '1').then((value) => {
      old[name] = value;
    }));
    cy.task('queryDB', 'SELECT id, allowRatings FROM #__kunena_categories WHERE id IN (3, 4)').then((rows) => {
      old.ratings = Object.fromEntries(rows.map((row) => [row.id, Number(row.allowRatings)]));
    });
    cy.task('queryDB', 'UPDATE #__kunena_categories SET allowRatings = 1 WHERE id = 3');
    cy.task('queryDB', 'UPDATE #__kunena_categories SET allowRatings = 0 WHERE id = 4');
  });

  after(() => {
    ['showThankYou', 'ratingEnabled', 'topicIcons'].forEach((name) => cy.kunena_setOption(name, old[name] ?? '1'));
    cy.then(() => [3, 4].forEach((id) => cy.task('queryDB', `UPDATE #__kunena_categories SET allowRatings = ${old.ratings[id]} WHERE id = ${id}`)));
  });

  beforeEach(() => {
    makeTopic(3, 797, 'admin').then((set) => {
      ids.admin = set;

      return makeTopic(3, user.id, 'user');
    }).then((set) => {
      ids.user = set;

      return makeTopic(4, 797, 'noratings');
    }).then((set) => {
      ids.noRatings = set;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress rules restricted ${stamp}', 'cypress-rules-${stamp}', '', '', '', '', '', 'joomla.level', 3, 3, 1, 8, 1, 92, 1, '{}', 'default')`,
      );
    }).then((category) => {
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-rules-${stamp}', 'catid', ${category.insertId}, 0)`);

      return makeTopic(category.insertId, 797, 'restricted');
    }).then((set) => {
      ids.restricted = set;
    });
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id, first_post_id FROM #__kunena_topics WHERE subject LIKE 'Cypress rules % ${stamp}'`).then((rows) => {
      rows.forEach(({ id, first_post_id: first }) => {
        cy.task('queryDB', `DELETE FROM #__kunena_thankyou WHERE postid = ${first}`);
        cy.task('queryDB', `DELETE FROM #__kunena_rate WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-rules-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE name = 'Cypress rules restricted ${stamp}'`);
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
  });

  it('counts a thank you once, and none for an own message or from a guest', () => {
    task(ids.admin, 'thankyou');
    thanks(ids.admin).then((rows) => expect(rows, 'a thank you of a guest').to.have.length(0));

    cy.doFrontendLogin(user.name, user.password, false);
    task(ids.admin, 'thankyou');
    task(ids.admin, 'thankyou');
    thanks(ids.admin).then((rows) => expect(rows.map((row) => Number(row.userid)), 'the thanks for the message').to.deep.eq([user.id]));
    task(ids.user, 'thankyou');
    thanks(ids.user).then((rows) => expect(rows, 'a thank you for an own message').to.have.length(0));
  });

  it('counts a rating once, from 1 to 5, not for an own topic, not where it is not allowed', () => {
    // The answer is JSON also for a guest (it was the text "Array", with a PHP warning).
    rate(ids.admin, 4).then((response) => expect(response.body, 'the answer for a guest').to.not.eq('Array'));
    rates(ids.admin).then((rows) => expect(rows, 'a rating of a guest').to.have.length(0));

    cy.doFrontendLogin(user.name, user.password, false);
    rate(ids.admin, 4);
    rate(ids.admin, 2);
    rates(ids.admin).then((rows) => expect(rows.map((row) => `${row.userid}:${row.rate}`), 'the rating').to.deep.eq([`${user.id}:4`]));
    rate(ids.user, 5);
    rates(ids.user).then((rows) => expect(rows, 'a rating of an own topic').to.have.length(0));
    rate(ids.noRatings, 3);
    rates(ids.noRatings).then((rows) => expect(rows, 'a rating where it is not allowed').to.have.length(0));
    rate(ids.admin, 9);
    rate(ids.admin, 0);
    rates(ids.admin).then((rows) => expect(rows, 'a rating out of 1 to 5').to.have.length(1));
  });

  it('lets the starter of a topic mark it solved, and not another user', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    task(ids.admin, 'solved');
    iconOf(ids.admin).then((icon) => expect(icon, 'the topic of another user').to.eq(0));
    task(ids.user, 'solved');
    iconOf(ids.user).then((icon) => expect(icon, 'the own topic').to.not.eq(0));
  });

  it('does not subscribe a user to a topic which he may not read, nor make it his favorite', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    task(ids.restricted, 'subscribe');
    task(ids.restricted, 'favorite');
    userTopic(ids.restricted).then((rows) => {
      rows.forEach((row) => {
        expect(Number(row.subscribed), 'subscribed').to.eq(0);
        expect(Number(row.favorite), 'favorite').to.eq(0);
      });
    });
    task(ids.admin, 'subscribe');
    userTopic(ids.admin).then((rows) => expect(Number(rows[0].subscribed), 'subscribed to a topic which he may read').to.eq(1));
  });
});
