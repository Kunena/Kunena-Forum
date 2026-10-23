// The end of a poll is a text field of the poll dialog. A date which the parser of PHP does not know (a day-first date like
// 23/10/2030, or a typo) gave an error page after the topic was saved already. Now a date of the format of the date picker is the
// end of that day, a text which is no date is no end, and the topic with its poll is made in every case.
describe('Kunena polls: the end date of a new poll', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const old = {};

  const newTopic = (name, end) => cy.request(`${base}&view=topic&layout=create&catid=3`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=post`,
      form: true,
      body: {
        catid: 3,
        subject: `Cypress poll end ${name} ${stamp}`,
        message: 'A topic with a poll.',
        poll_title: 'A poll',
        'polloptionsID[new1]': 'Yes',
        'polloptionsID[new2]': 'No',
        poll_time_to_live: end,
        [token]: '1',
      },
      failOnStatusCode: false,
      followRedirect: false,
    }).then((response) => expect(response.status, `the status for the end "${end}"`).to.be.lessThan(500));
  });

  const endOf = (name) => cy.task(
    'queryDB',
    `SELECT CAST(p.polltimetolive AS CHAR) AS ending FROM #__kunena_topics AS t INNER JOIN #__kunena_polls AS p ON p.id = t.poll_id WHERE t.subject = 'Cypress poll end ${name} ${stamp}'`,
  ).then((rows) => (rows.length ? rows[0].ending : null));

  before(() => {
    cy.kunena_setOption('pollEnabled', '1').then((value) => {
      old.pollEnabled = value;
    });
    cy.kunena_setOption('datePickerFormat', 'dd/mm/yyyy').then((value) => {
      old.format = value;
    });
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.flood = value;
    });
    cy.task('queryDB', 'SELECT allowPolls FROM #__kunena_categories WHERE id = 3').then((rows) => {
      old.allowPolls = rows[0].allowPolls;
    });
    cy.task('queryDB', 'UPDATE #__kunena_categories SET allowPolls = 1 WHERE id = 3');
  });

  after(() => {
    cy.kunena_setOption('pollEnabled', old.pollEnabled ?? '1');
    cy.kunena_setOption('datePickerFormat', old.format ?? '');
    cy.kunena_setOption('floodProtection', old.flood ?? '0');
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET allowPolls = ${Number(old.allowPolls)} WHERE id = 3`));
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id, poll_id FROM #__kunena_topics WHERE subject LIKE 'Cypress poll end % ${stamp}'`).then((rows) => {
      rows.forEach(({ id, poll_id: poll }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_polls_options WHERE pollid = ${poll}`);
        cy.task('queryDB', `DELETE FROM #__kunena_polls WHERE id = ${poll}`);
      });
    });
    cy.task('queryDB', `UPDATE #__kunena_users u SET posts = (SELECT COUNT(*) FROM #__kunena_messages m WHERE m.userid = u.userid AND m.hold = 0) WHERE u.userid = ${user.id}`);
    cy.task('queryDB', "UPDATE #__kunena_categories c SET numTopics = (SELECT COUNT(*) FROM #__kunena_topics t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0), numPosts = (SELECT COALESCE(SUM(t.posts), 0) FROM #__kunena_topics t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0) WHERE c.id = 3");
  });

  it('takes an end in the format of the date picker as the end of that day', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    newTopic('picker', '23/10/2030');
    endOf('picker').then((ending) => expect(ending, 'the end of the poll').to.eq('2030-10-23 23:59:59'));
  });

  it('takes an end which PHP reads as it is', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    newTopic('iso', '2030-10-23 18:00:00');
    endOf('iso').then((ending) => expect(ending, 'the end of the poll').to.eq('2030-10-23 18:00:00'));
  });

  it('makes the poll without an end for a text which is no date', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    newTopic('text', 'next tuesday-ish');
    endOf('text').then((ending) => expect(ending, 'the end of the poll').to.eq('1000-01-01 00:00:00'));
  });
});
