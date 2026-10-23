// The subscription of a category (task category.subscribe) and of a topic (topic.subscribe), and a favorite topic (topic.favorite),
// follow the rules of their actions: not while the subscriptions or the favorites are switched off, not for a banned user. (The
// tasks checked only that the user may read the category or the topic; the form of a post checked the rules.)
describe('Kunena subscriptions and favorites: the rules of the tasks', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const old = {};

  const subscribe = () => cy.request(`${base}&view=category&catid=3`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({ url: `${base}&view=category&task=subscribe&catid=3&${token}=1`, failOnStatusCode: false, followRedirect: false });
  });
  const subscribed = () => cy.task('queryDB', `SELECT subscribed FROM #__kunena_user_categories WHERE user_id = ${user.id} AND category_id = 3`)
    .then((rows) => (rows.length ? Number(rows[0].subscribed) : 0));

  before(() => {
    cy.kunena_setOption('allowSubscriptions', '1').then((value) => {
      old.allowSubscriptions = value;
    });
    cy.kunena_setOption('categorySubscriptions', 'post').then((value) => {
      old.categorySubscriptions = value;
    });
    cy.kunena_setOption('allowFavorites', '1').then((value) => {
      old.allowFavorites = value;
    });
    cy.task('queryDB', `SELECT subscribed FROM #__kunena_user_categories WHERE user_id = ${user.id} AND category_id = 3`).then((rows) => {
      old.row = rows.length ? Number(rows[0].subscribed) : null;
    });
  });

  after(() => {
    cy.kunena_setOption('allowSubscriptions', old.allowSubscriptions ?? '1');
    cy.kunena_setOption('categorySubscriptions', old.categorySubscriptions ?? 'post');
    cy.kunena_setOption('allowFavorites', old.allowFavorites ?? '1');
  });

  const topicTask = (task) => cy.request(`${base}&view=topic&catid=3&id=9`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({ url: `${base}&view=topic&task=${task}&catid=3&id=9&${token}=1`, failOnStatusCode: false, followRedirect: false });
  });
  const topicRow = () => cy.task('queryDB', `SELECT subscribed, favorite FROM #__kunena_user_topics WHERE user_id = ${user.id} AND topic_id = 9`)
    .then((rows) => rows[0] ?? { subscribed: 0, favorite: 0 });

  afterEach(() => {
    cy.task('queryDB', `UPDATE #__kunena_user_topics SET subscribed = 0, favorite = 0 WHERE user_id = ${user.id} AND topic_id = 9`);
    cy.then(() => (old.row === null
      ? cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${user.id} AND category_id = 3`)
      : cy.task('queryDB', `UPDATE #__kunena_user_categories SET subscribed = ${old.row} WHERE user_id = ${user.id} AND category_id = 3`)));
    cy.task('queryDB', `UPDATE #__kunena_users SET banned = '1000-01-01 00:00:00' WHERE userid = ${user.id}`);
  });

  it('subscribes a user to a category while the subscriptions are on', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    subscribe();
    subscribed().should('eq', 1);
  });

  it('does not subscribe while the category subscriptions are off', () => {
    cy.kunena_setOption('categorySubscriptions', 'disabled');
    cy.doFrontendLogin(user.name, user.password, false);
    subscribe();
    subscribed().should('eq', 0);
    cy.kunena_setOption('categorySubscriptions', 'post');
  });

  it('does not subscribe a banned user', () => {
    cy.task('queryDB', `INSERT INTO #__kunena_users_banned (userid, ip, blocked, expiration, created_by, created_time, reason_private, reason_public, modified_by, modified_time, comments, params) VALUES (${user.id}, '', 0, DATE_ADD(NOW(), INTERVAL 1 HOUR), 797, NOW(), '', '', 0, '1000-01-01 00:00:00', '', '')`);
    cy.task('queryDB', `UPDATE #__kunena_users SET banned = DATE_ADD(UTC_TIMESTAMP(), INTERVAL 1 HOUR) WHERE userid = ${user.id}`);
    cy.doFrontendLogin(user.name, user.password, false);
    subscribe();
    subscribed().should('eq', 0);
  });

  it('subscribes to a topic and makes it a favorite while that is on', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    topicTask('subscribe');
    topicTask('favorite');
    topicRow().then((row) => {
      expect(Number(row.subscribed), 'subscribed').to.eq(1);
      expect(Number(row.favorite), 'favorite').to.eq(1);
    });
  });

  it('does not subscribe to a topic while the subscriptions are off, nor make a favorite while the favorites are off', () => {
    cy.kunena_setOption('allowSubscriptions', '0');
    cy.kunena_setOption('allowFavorites', '0');
    cy.doFrontendLogin(user.name, user.password, false);
    topicTask('subscribe');
    topicTask('favorite');
    topicRow().then((row) => {
      expect(Number(row.subscribed), 'subscribed').to.eq(0);
      expect(Number(row.favorite), 'favorite').to.eq(0);
    });
    cy.kunena_setOption('allowSubscriptions', '1');
    cy.kunena_setOption('allowFavorites', '1');
  });

  it('does not subscribe a banned user to a topic', () => {
    cy.task('queryDB', `INSERT INTO #__kunena_users_banned (userid, ip, blocked, expiration, created_by, created_time, reason_private, reason_public, modified_by, modified_time, comments, params) VALUES (${user.id}, '', 0, DATE_ADD(NOW(), INTERVAL 1 HOUR), 797, NOW(), '', '', 0, '1000-01-01 00:00:00', '', '')`);
    cy.task('queryDB', `UPDATE #__kunena_users SET banned = DATE_ADD(UTC_TIMESTAMP(), INTERVAL 1 HOUR) WHERE userid = ${user.id}`);
    cy.doFrontendLogin(user.name, user.password, false);
    topicTask('subscribe');
    topicRow().then((row) => expect(Number(row.subscribed), 'subscribed').to.eq(0));
  });
});
