// A user takes a topic out of his favourites (task unfavorite) and ends his subscription of it (task unsubscribe): the flags of
// his row of the topic go back to 0, and a request without the token changes nothing.
describe('Kunena topics: unfavorite and unsubscribe', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const topic = 8;
  let row;

  const flags = () => cy.task('queryDB', `SELECT favorite, subscribed FROM #__kunena_user_topics WHERE user_id = ${user.id} AND topic_id = ${topic}`)
    .then((rows) => rows[0] ?? { favorite: 0, subscribed: 0 });
  const task = (name, withToken = true) => cy.request(`${base}&view=topic&catid=3&id=${topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1];

    return cy.request({ url: `${base}&view=topic&task=${name}&catid=3&id=${topic}${withToken && token ? `&${token}=1` : ''}`, failOnStatusCode: false, followRedirect: false });
  });

  beforeEach(() => {
    // The row of the user for the topic, a favourite and subscribed (the row may exist: it is put back after the test).
    cy.task('queryDB', `SELECT favorite, subscribed FROM #__kunena_user_topics WHERE user_id = ${user.id} AND topic_id = ${topic}`).then((rows) => {
      row = rows[0] ?? null;
    });
    cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${user.id}, ${topic}, 3, 0, 0, 0, 1, 1, '') ON DUPLICATE KEY UPDATE favorite = 1, subscribed = 1`);
  });

  afterEach(() => {
    cy.then(() => (row === null
      ? cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE user_id = ${user.id} AND topic_id = ${topic}`)
      : cy.task('queryDB', `UPDATE #__kunena_user_topics SET favorite = ${Number(row.favorite)}, subscribed = ${Number(row.subscribed)} WHERE user_id = ${user.id} AND topic_id = ${topic}`)));
  });

  it('changes nothing without the token', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    task('unfavorite', false);
    task('unsubscribe', false);
    flags().then((flag) => {
      expect(Number(flag.favorite), 'favourite').to.eq(1);
      expect(Number(flag.subscribed), 'subscribed').to.eq(1);
    });
  });

  it('takes the topic out of the favourites and ends the subscription', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    task('unfavorite');
    flags().then((flag) => {
      expect(Number(flag.favorite), 'favourite').to.eq(0);
      expect(Number(flag.subscribed), 'subscribed is kept').to.eq(1);
    });
    task('unsubscribe');
    flags().then((flag) => expect(Number(flag.subscribed), 'subscribed').to.eq(0));
  });
});
