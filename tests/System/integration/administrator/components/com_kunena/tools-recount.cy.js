// The tool "Recount statistics" of the backend (tasks tools.recount and tools.doRecount, AJAX steps until 100%) puts wrong
// counters right: the posts of a user, the topics and posts of a category, the posts of a topic. The counters of the fixture are
// read first and put back after the test in any case (the fixture keeps the numbers it had, also when they were off).
describe('Kunena backend tools: recount', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const old = {};
  const counters = () => cy.task(
    'queryDB',
    'SELECT (SELECT posts FROM #__kunena_users WHERE userid = 797) AS userPosts, (SELECT numTopics FROM #__kunena_categories WHERE id = 3) AS catTopics, (SELECT numPosts FROM #__kunena_categories WHERE id = 3) AS catPosts, (SELECT posts FROM #__kunena_topics WHERE id = 9) AS topicPosts',
  ).then((rows) => Object.fromEntries(Object.entries(rows[0]).map(([key, value]) => [key, Number(value)])));

  beforeEach(() => {
    counters().then((values) => Object.assign(old, values));
  });

  afterEach(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET posts = ${old.userPosts} WHERE userid = 797`));
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = ${old.catTopics}, numPosts = ${old.catPosts} WHERE id = 3`));
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_topics SET posts = ${old.topicPosts} WHERE id = 9`));
  });

  it('puts the counters of users, categories and topics right', () => {
    cy.task('queryDB', 'UPDATE #__kunena_users SET posts = 999 WHERE userid = 797');
    cy.task('queryDB', 'UPDATE #__kunena_categories SET numTopics = 999, numPosts = 999 WHERE id = 3');
    cy.task('queryDB', 'UPDATE #__kunena_topics SET posts = 99 WHERE id = 9');

    cy.doAdministratorLogin();
    cy.request(`${admin}&view=tools&layout=recount`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      cy.request({ method: 'POST', url: `${admin}&task=tools.recount&format=json`, form: true, body: { topics: 1, usertopics: 1, categories: 1, users: 1, polls: 1, [token]: '1' } });

      // The steps go on until 100% (at most 20 of them).
      const step = (n) => cy.request(`${admin}&task=tools.doRecount&format=json&${token}=1`).its('body').then((body) => {
        const answer = typeof body === 'string' ? JSON.parse(body) : body;


        expect(answer.success, `step ${n}`).to.eq(true);

        if (answer.status !== '100%' && n < 20) {
          step(n + 1);
        }
      });

      step(1);
    });

    // The right numbers, counted from the messages and topics.
    cy.task(
      'queryDB',
      'SELECT (SELECT COUNT(*) FROM #__kunena_messages WHERE userid = 797 AND hold = 0) AS userPosts, (SELECT COUNT(*) FROM #__kunena_topics WHERE category_id = 3 AND hold = 0 AND moved_id = 0) AS catTopics, (SELECT COALESCE(SUM(posts), 0) FROM #__kunena_topics WHERE category_id = 3 AND hold = 0 AND moved_id = 0) AS catPosts, (SELECT COUNT(*) FROM #__kunena_messages WHERE thread = 9 AND hold = 0) AS topicPosts',
    ).then((rows) => {
      const right = Object.fromEntries(Object.entries(rows[0]).map(([key, value]) => [key, Number(value)]));

      counters().should('deep.eq', right);
    });
  });
});
