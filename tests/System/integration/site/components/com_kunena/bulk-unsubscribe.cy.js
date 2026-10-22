// The list of the subscriptions of a user has a form which ends several subscriptions at once (topics.unsubscribe). It sends the id of
// the user whose subscriptions they are: a user may end his own, a moderator those of a user, and nobody those of somebody else.
describe('Kunena subscriptions: ending several at once', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const topics = [9, 8];

  // The rows of the users for these topics may exist (the posts of the user in the topic are counted there): the subscription is
  // set on them, or a row without posts is added, and the rows go back to how they were after the test.
  const rows = [];
  const subscribe = (userId) => topics.forEach((topic) => {
    cy.task('queryDB', `SELECT subscribed FROM #__kunena_user_topics WHERE user_id = ${userId} AND topic_id = ${topic}`).then((found) => {
      rows.push({ userId, topic, subscribed: found.length ? Number(found[0].subscribed) : null });
    });
    cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${userId}, ${topic}, 3, 0, 0, 0, 0, 1, '') ON DUPLICATE KEY UPDATE subscribed = 1`);
  });
  const restore = () => {
    rows.splice(0).forEach(({ userId, topic, subscribed }) => {
      if (subscribed === null) {
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE user_id = ${userId} AND topic_id = ${topic}`);
      } else {
        cy.task('queryDB', `UPDATE #__kunena_user_topics SET subscribed = ${subscribed} WHERE user_id = ${userId} AND topic_id = ${topic}`);
      }
    });
  };
  const subscribed = (userId) => cy.task('queryDB', `SELECT topic_id FROM #__kunena_user_topics WHERE user_id = ${userId} AND subscribed = 1 AND topic_id IN (${topics.join(',')})`).then((rows) => rows.map((row) => Number(row.topic_id)).sort());

  const unsubscribe = (userId) => cy.request(`${base}&view=home`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';
    const body = { userid: userId, [token]: '1' };

    topics.forEach((topic) => {
      body[`topics[${topic}]`] = 1;
    });

    return cy.request({ method: 'POST', url: `${base}&view=topics&task=unsubscribe`, form: true, body, failOnStatusCode: false, followRedirect: false });
  });

  beforeEach(() => {
    subscribe(user.id);
    subscribe(admin);
  });

  afterEach(() => {
    restore();
  });

  it('lets a user end his own subscriptions, and not those of another user', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    unsubscribe(admin);
    subscribed(admin).should('deep.eq', [8, 9]);
    unsubscribe(user.id);
    subscribed(user.id).should('deep.eq', []);
  });

  it('lets a moderator end the subscriptions of a user', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    unsubscribe(user.id);
    subscribed(user.id).should('deep.eq', []);
  });

  it('lets a visitor end nothing', () => {
    unsubscribe(user.id);
    subscribed(user.id).should('deep.eq', [8, 9]);
  });
});
