// A topic written with a poll (the fields of the dialog of the editor are added to the form), a vote, and a poll with a time to live
// which is no date. The answer is checked in the database, and the afterEach of the support file fails a test for a PHP notice or
// warning on a page.
describe('Kunena writing: polls', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };

  const typeInEditor = (text) => {
    cy.window().should('have.property', 'sceditor');
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty')
      .then(($body) => cy.wrap($body).click().type(text, { parseSpecialCharSequences: false }));
  };

  // The fields which the dialog of the poll adds to the form of the topic.
  const addPollFields = (title, options, timeToLive) => {
    cy.get('#postform').then(($form) => {
      const add = (name, value) => $form.append(`<input type="hidden" name="${name}" value="${String(value).replace(/"/g, '&quot;')}">`);

      add('poll_title', title);
      options.forEach((option, index) => add(`polloptionsID[new${index}]`, option));

      if (timeToLive !== undefined) {
        add('poll_time_to_live', timeToLive);
      }
    });
  };

  const writeTopic = (subject, title, options, timeToLive) => {
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    cy.get('#postform input[name="subject"]').type(subject);
    typeInEditor('A topic with a poll.');
    addPollFields(title, options, timeToLive);
    cy.get('#form_submit_button').should('not.be.disabled').click();
  };

  const old = {};

  before(() => {
    cy.kunena_setOption('pollEnabled', '1').then((value) => {
      old.pollEnabled = value;
    });
    cy.task('queryDB', 'SELECT allowPolls FROM #__kunena_categories WHERE id = 3').then((rows) => {
      old.allowPolls = rows[0].allowPolls;
    });
    cy.task('queryDB', 'UPDATE #__kunena_categories SET allowPolls = 1 WHERE id = 3');
  });

  after(() => {
    cy.kunena_setOption('pollEnabled', old.pollEnabled ?? '1');
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET allowPolls = ${Number(old.allowPolls)} WHERE id = 3`));
    cy.task('queryDB', `SELECT id, poll_id FROM #__kunena_topics WHERE subject LIKE 'Cypress poll ${stamp}%'`).then((rows) => {
      rows.forEach(({ id, poll_id: pollId }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);

        if (pollId) {
          cy.task('queryDB', `DELETE FROM #__kunena_polls_users WHERE pollid = ${pollId}`);
          cy.task('queryDB', `DELETE FROM #__kunena_polls_options WHERE pollid = ${pollId}`);
          cy.task('queryDB', `DELETE FROM #__kunena_polls WHERE id = ${pollId}`);
        }
      });
    });
    // The counters of the users and the rows of the topics which are gone.
    cy.kunena_fixUserCounters();
  });

  beforeEach(() => {
    cy.doFrontendLogin(user.name, user.password);
  });

  it('writes a topic with a poll and a vote counts', () => {
    writeTopic(`Cypress poll ${stamp}`, 'Which one?', ['Red', 'Green', 'Blue']);

    cy.task('queryDB', `SELECT id, poll_id FROM #__kunena_topics WHERE subject = 'Cypress poll ${stamp}'`).then((rows) => {
      expect(rows, 'the topic').to.have.length(1);
      expect(Number(rows[0].poll_id), 'the poll of the topic').to.be.greaterThan(0);

      cy.task('queryDB', `SELECT text FROM #__kunena_polls_options WHERE pollid = ${rows[0].poll_id} ORDER BY id`).then((options) => {
        expect(options.map((option) => option.text)).to.deep.eq(['Red', 'Green', 'Blue']);
      });

      cy.visit(`${base}&view=topic&catid=3&id=${rows[0].id}`);
      cy.get('#poll-vote input[type="radio"], #poll-vote input[type="checkbox"]').first().check({ force: true });
      cy.get('#poll-vote').contains('button, input[type="submit"]', /vote/i).click();

      cy.task('queryDB', `SELECT votes FROM #__kunena_polls_options WHERE pollid = ${rows[0].poll_id} ORDER BY id`).then((options) => {
        expect(options.map((option) => Number(option.votes)).reduce((a, b) => a + b, 0), 'one vote').to.eq(1);
      });
    });
  });

  it('does not break on a time to live which is no date', () => {
    writeTopic(`Cypress poll ${stamp} ttl`, 'Which time?', ['One', 'Two'], 'not a date');

    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = 'Cypress poll ${stamp} ttl'`).then((rows) => {
      expect(rows, 'the topic is written').to.have.length(1);
    });
  });
});
