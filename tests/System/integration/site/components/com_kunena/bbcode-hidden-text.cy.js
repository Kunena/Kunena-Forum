// The text in [hide] (only for logged in users) and [confidential] (only for the author and the moderators) is in the stored
// message, and the topic keeps the start of the first and the last message for the lists. The text must not come out of the lists, the
// feeds, the search or a JSON answer for a visitor who may not read it. The topic is written with the form of the forum, so the start
// of the message is made as it is made on a real forum.
describe('Kunena BBCode: text for some readers', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress hidden ${stamp}`;
  const hide = `HIDETEXT${stamp}`;
  const confidential = `CONFIDENTIALTEXT${stamp}`;
  let topicId;

  const typeInEditor = (text) => {
    cy.window().should('have.property', 'sceditor');
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty')
      .then(($body) => cy.wrap($body).click().type(text, { parseSpecialCharSequences: false }));
  };

  const lists = () => [
    `${base}&view=category&catid=3`,
    `${base}&view=category&layout=list`,
    `${base}&view=category&catid=3&format=feed&layout=feed&type=rss`,
    `${base}&view=topics`,
    `${base}&view=topics&format=feed&layout=feed&type=rss`,
    `${base}&view=topics&format=json`,
    `${base}&view=topics&mode=replies`,
    `${base}&view=topics&layout=user&mode=topics&userid=797`,
    `${base}&view=home`,
    `${base}&view=search&task=results&query=${stamp}`,
    `${base}&view=search&task=results&query=Cypress+hidden+${stamp}&titleonly=1`,
    `${base}&view=statistics`,
    `${base}&view=user&layout=default&userid=797`,
    `${base}&view=topic&format=json&catid=3&id=${topicId}`,
  ];

  const leaksOf = (words) => {
    const leaks = [];

    lists().forEach((url) => {
      cy.request({ url, failOnStatusCode: false }).then((response) => {
        const body = typeof response.body === 'string' ? response.body : JSON.stringify(response.body);
        const found = words.filter((word) => body.includes(word));

        if (found.length) {
          leaks.push(`${url.replace(base, '')} shows ${found.join(', ')}`);
        }
      });
    });

    return cy.then(() => leaks);
  };

  before(() => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    cy.get('#postform input[name="subject"]').type(subject);
    typeInEditor(`[hide]${hide}[/hide] and [confidential]${confidential}[/confidential] and the rest.`);
    cy.get('#form_submit_button').should('not.be.disabled').click();
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      expect(rows, 'the new topic').to.have.length(1);
      topicId = rows[0].id;
    });
  });

  after(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    // The counters of the users and the rows of the topics which are gone.
    cy.kunena_fixUserCounters();
  });

  it('shows neither text to a guest', () => {
    // The login of the administrator in before() must not be the visitor.
    cy.clearAllCookies();

    leaksOf([hide, confidential]).then((leaks) => expect(leaks, 'the guest: the pages with the text').to.deep.eq([]));
    cy.request(`${base}&view=topic&catid=3&id=${topicId}`).its('body').should('include', 'and the rest').and('not.include', hide).and('not.include', confidential);
  });

  it('shows the confidential text to nobody but the author and the moderators', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    leaksOf([confidential]).then((leaks) => expect(leaks, 'a user: the pages with the confidential text').to.deep.eq([]));
    cy.request(`${base}&view=topic&catid=3&id=${topicId}`).its('body').should('not.include', confidential);
  });

  it('shows both texts to the author on the page of the topic', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.request(`${base}&view=topic&catid=3&id=${topicId}`).its('body').should('include', confidential);
  });
});
