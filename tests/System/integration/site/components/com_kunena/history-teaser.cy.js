// The edit history of a message (view=topic&layout=history) with "Show the edit history: everybody": in a category which hides the
// replies from the guests (teaser), a guest read there the replies, their old versions and their text as it is now.
describe('Kunena edit history: the replies hidden from the guests', () => {
  const stamp = Date.now();
  const reply = 11;
  const old = {};

  before(() => {
    cy.kunena_setOption('teaser', '1').then((value) => {
      old.teaser = value ?? null;
    });
    cy.kunena_setOption('editHistory', '2').then((value) => {
      old.editHistory = value ?? null;
    });
  });

  after(() => {
    cy.kunena_setOption('teaser', old.teaser);
    cy.kunena_setOption('editHistory', old.editHistory);
  });

  beforeEach(() => {
    // An older version of the reply of topic 9.
    cy.task('queryDB', `INSERT INTO #__kunena_messages_history (message_id, edited_by, edited_time, reason, subject, message) VALUES (${reply}, 798, UNIX_TIMESTAMP(), '', 'Re: Which template do you use?', 'An older text of the reply ${stamp}')`);
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_history WHERE message LIKE '%${stamp}%'`);
  });

  it('does not show a guest the history of a hidden reply', () => {
    cy.request({ url: `/index.php?option=com_kunena&view=topic&layout=history&catid=3&id=9&mesid=${reply}`, failOnStatusCode: false }).then((response) => {
      const body = String(response.body);

      expect(body, 'the older version').not.to.include(`An older text of the reply ${stamp}`);
      expect(body, 'the text of the reply now').not.to.include('I use the Cassiopeia template of Joomla');
    });
  });
});
