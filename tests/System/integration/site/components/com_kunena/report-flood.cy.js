// The flood protection of the forum (the time between two posts) is for the reports too: each report sends the moderators an
// e-mail and a notification (and makes a topic with the reports plugin), and a user could send as many as he wanted.
describe('Kunena reports: the flood protection', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const reported = { topic: 2, message: 2, category: 4 };
  const old = {};

  // Two posts to report: the inbox keeps one notification of a message, so the second report is about another post.
  const posts = [reported, { topic: 9, message: 11, category: 3 }];
  const report = (reason, post = reported) => cy.request(`${base}&view=topic&layout=report&catid=${post.category}&id=${post.topic}&mesid=${post.message}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=report`,
      form: true,
      body: { catid: post.category, id: post.topic, mesid: post.message, reason, text: `The text of the report ${stamp}`, [token]: '1' },
      failOnStatusCode: false,
      followRedirect: false,
    });
  });
  const notifications = () => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_inbox WHERE type = 'report' AND sender_id = ${user.id} AND message_id IN (${posts.map((post) => post.message).join(',')})`).then((rows) => Number(rows[0].n));

  beforeEach(() => {
    ['reportMsg', 'floodProtection'].forEach((name) => cy.kunena_setOption(name, name === 'reportMsg' ? '1' : '30').then((value) => {
      old[name] = value ?? null;
    }));
  });

  afterEach(() => {
    Object.keys(old).forEach((name) => cy.kunena_setOption(name, old[name]));
    posts.forEach((post) => {
      cy.task('queryDB', `DELETE FROM #__kunena_inbox WHERE type = 'report' AND sender_id = ${user.id} AND message_id = ${post.message}`);
      cy.task('queryDB', `DELETE FROM #__kunena_reports WHERE message_id = ${post.message}`);
    });
    // The time of the last report of the user is kept in the cache.
    cy.task('deleteRelativePath', 'cache/kunena_report_flood');
  });

  it('refuses a second report within the time of the flood protection', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    report(`First report ${stamp}`);
    notifications().then((first) => {
      expect(first, 'the notifications of the first report').to.be.greaterThan(0);
      report(`Second report ${stamp}`, posts[1]);
      notifications().should('eq', first);
    });
  });
});
