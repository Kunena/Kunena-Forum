// A user rates a topic once. The code looked for a rating of the user before it stored his: two requests at the same time both
// found none, and both ratings were stored. The table has a key (topic_id, userid) now, which keeps one.
describe('Kunena ratings: one rating of a user for a topic', () => {
  const user = 798;
  const topic = 9;

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_rate WHERE topic_id = ${topic} AND userid = ${user}`);
  });

  it('keeps one rating of a user for a topic in the table', () => {
    cy.task('queryDB', `DELETE FROM #__kunena_rate WHERE topic_id = ${topic} AND userid = ${user}`);
    cy.task('queryDB', `INSERT IGNORE INTO #__kunena_rate (topic_id, userid, rate, time) VALUES (${topic}, ${user}, 4, NOW())`);
    cy.task('queryDB', `INSERT IGNORE INTO #__kunena_rate (topic_id, userid, rate, time) VALUES (${topic}, ${user}, 5, NOW())`);
    cy.task('queryDB', `SELECT rate FROM #__kunena_rate WHERE topic_id = ${topic} AND userid = ${user}`).then((rows) => {
      expect(rows, 'the ratings of the user').to.have.length(1);
      expect(Number(rows[0].rate), 'the first one').to.eq(4);
    });
  });
});
