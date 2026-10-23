// The title of a poll has 150 characters in the database and an option 100. The poll dialog does not stop a longer text: the
// strict database refused it and the poll (or its options) was not saved. The texts are shortened now.
describe('Kunena polls: texts longer than their columns', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const old = {};

  before(() => {
    cy.kunena_setOption('pollEnabled', '1').then((value) => {
      old.pollEnabled = value;
    });
    cy.kunena_setOption('floodProtection', '0');
    cy.task('queryDB', 'SELECT allowPolls FROM #__kunena_categories WHERE id = 3').then((rows) => {
      old.allowPolls = rows[0].allowPolls;
    });
    cy.task('queryDB', 'UPDATE #__kunena_categories SET allowPolls = 1 WHERE id = 3');
  });

  after(() => {
    cy.kunena_setOption('pollEnabled', old.pollEnabled ?? '1');
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET allowPolls = ${Number(old.allowPolls)} WHERE id = 3`));
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id, poll_id FROM #__kunena_topics WHERE subject = 'Cypress poll texts ${stamp}'`).then((rows) => {
      rows.forEach(({ id, poll_id: poll }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_polls_options WHERE pollid = ${poll}`);
        cy.task('queryDB', `DELETE FROM #__kunena_polls WHERE id = ${poll}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_polls WHERE title LIKE 'Cypress long poll ${stamp}%'`);
    cy.kunena_fixUserCounters();
    cy.task('queryDB', "UPDATE #__kunena_categories c SET numTopics = (SELECT COUNT(*) FROM #__kunena_topics t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0), numPosts = (SELECT COALESCE(SUM(t.posts), 0) FROM #__kunena_topics t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0) WHERE c.id = 3");
  });

  it('saves a poll with a long title and long options, shortened', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=topic&layout=create&catid=3`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: {
          catid: 3,
          subject: `Cypress poll texts ${stamp}`,
          message: 'A topic with a poll.',
          poll_title: `Cypress long poll ${stamp} ${'t'.repeat(200)}`,
          'polloptionsID[new1]': `First ${'o'.repeat(120)}`,
          'polloptionsID[new2]': `Second ${'o'.repeat(120)}`,
          [token]: '1',
        },
        failOnStatusCode: false,
        followRedirect: false,
      }).its('status').should('be.lessThan', 500);
    });

    cy.task('queryDB', `SELECT p.title, GROUP_CONCAT(o.text ORDER BY o.id SEPARATOR '|') AS options FROM #__kunena_topics AS t INNER JOIN #__kunena_polls AS p ON p.id = t.poll_id LEFT JOIN #__kunena_polls_options AS o ON o.pollid = p.id WHERE t.subject = 'Cypress poll texts ${stamp}' GROUP BY p.id`).then((rows) => {
      expect(rows, 'the poll of the topic').to.have.length(1);
      expect(rows[0].title.length, 'the length of the title').to.be.at.most(150);
      const options = String(rows[0].options ?? '').split('|');

      expect(options, 'the options').to.have.length(2);
      options.forEach((option) => expect(option.length, 'the length of an option').to.be.at.most(100));
    });
  });
});
