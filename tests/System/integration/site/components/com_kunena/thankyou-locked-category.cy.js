// A locked category takes no new posts, and its posts get no new thank yous. The rule of the category for the thank yous had
// the condition Unlocked outside its list ('topic.post.thankyou' => ['Read', 'NotBanned'], 'Unlocked'): a user thanked the
// posts of a locked category.
describe('Kunena thank you: a locked category', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};
  let locked;

  const thanks = () => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_thankyou WHERE postid = ${ids.message} AND userid = ${user.id}`).its('0.n').then(Number);

  const thank = () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=topic&catid=3&id=9`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({ url: `${base}&view=topic&catid=3&id=9&mesid=${ids.message}&task=thankyou&${token}=1`, failOnStatusCode: false, followRedirect: false });
    });
  };

  before(() => {
    cy.task('queryDB', 'SELECT locked FROM #__kunena_categories WHERE id = 3').then((rows) => {
      locked = Number(rows[0].locked);
    });
  });

  beforeEach(() => {
    // A reply of the administrator in the public topic of the test site.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (11, 9, 3, 'ci-admin', 797, '', 'Re: Cypress thanks ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A reply to thank ${stamp}.')`);
    });
  });

  afterEach(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET locked = ${locked === undefined ? 0 : locked} WHERE id = 3`));
    cy.task('queryDB', `DELETE FROM #__kunena_thankyou WHERE postid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
  });

  it('thanks a post of an open category (so the test can see a thank you)', () => {
    thank();
    thanks().should('eq', 1);
  });

  it('does not thank a post of a locked category', () => {
    cy.task('queryDB', 'UPDATE #__kunena_categories SET locked = 1 WHERE id = 3');
    thank();
    thanks().should('eq', 0);
  });
});
