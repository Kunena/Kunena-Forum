// The number of the options of a poll is limited by the option pollNbOptions (the editor offers no more fields). A request with
// more options must not make a poll with more: the first ones are kept.
describe('Kunena polls: the maximum number of options', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const subject = `Cypress poll options ${stamp}`;
  const old = {};

  before(() => {
    cy.kunena_setOption('pollEnabled', '1').then((value) => {
      old.pollEnabled = value;
    });
    cy.kunena_setOption('pollNbOptions', '4').then((value) => {
      old.pollNbOptions = value;
    });
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.floodProtection = value;
    });
    cy.task('queryDB', 'SELECT allowPolls FROM #__kunena_categories WHERE id = 3').then((rows) => {
      old.allowPolls = rows[0].allowPolls;
    });
    cy.task('queryDB', 'UPDATE #__kunena_categories SET allowPolls = 1 WHERE id = 3');
  });

  after(() => {
    ['pollEnabled', 'pollNbOptions', 'floodProtection'].forEach((name) => cy.kunena_setOption(name, old[name] === undefined ? null : old[name]));
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET allowPolls = ${Number(old.allowPolls)} WHERE id = 3`));
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id, poll_id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      rows.forEach(({ id, poll_id: poll }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_polls_options WHERE pollid = ${poll}`);
        cy.task('queryDB', `DELETE FROM #__kunena_polls WHERE id = ${poll}`);
      });
    });
    cy.kunena_fixUserCounters();
    cy.task('queryDB', "UPDATE #__kunena_categories c SET numTopics = (SELECT COUNT(*) FROM #__kunena_topics t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0), numPosts = (SELECT COALESCE(SUM(t.posts), 0) FROM #__kunena_topics t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0) WHERE c.id = 3");
  });

  it('keeps no more options than the maximum', () => {
    const options = {};

    for (let i = 1; i <= 12; i += 1) {
      options[`polloptionsID[new${i}]`] = `Option ${i}`;
    }

    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=topic&layout=create&catid=3`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: { catid: 3, subject, message: 'A topic with a poll.', poll_title: `Cypress poll ${stamp}`, ...options, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_polls_options AS o INNER JOIN #__kunena_topics AS t ON t.poll_id = o.pollid WHERE t.subject = '${subject}'`).then((rows) => {
      expect(Number(rows[0].n), 'the options of the poll').to.be.within(1, 4);
    });
  });
});
