// A mention tells the user who is mentioned about the post (an entry in the inbox, an e-mail). A user who may not read the category
// must not get it: it would show him the title of a topic and the link of a post which he may not see. The check of the right to read
// was made for the user who posts (the first user of the request) and not for the user who is mentioned.
describe('Kunena permissions: a mention', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', id: 798 };
  let restricted;

  const typeInEditor = (text) => {
    cy.window().should('have.property', 'sceditor');
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty')
      .then(($body) => cy.wrap($body).click().type(text, { parseSpecialCharSequences: false }));
  };

  const mention = (catid, subject) => {
    cy.visit(`${base}&view=topic&layout=create&catid=${catid}`);
    cy.get('#postform input[name="subject"]').type(subject);
    typeInEditor(`Hello [mention userid=${user.id}]${user.name}[/mention], it is a test.`);
    cy.get('#form_submit_button').should('not.be.disabled').click();
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => expect(rows, 'the new topic').to.have.length(1));
  };

  const mentionsOfTheUser = (subject) => cy.task(
    'queryDB',
    `SELECT i.id FROM #__kunena_inbox AS i INNER JOIN #__kunena_topics AS t ON t.id = i.topic_id WHERE i.user_id = ${user.id} AND i.type = 'mention' AND t.subject = '${subject}'`,
  );

  beforeEach(() => {
    // A category for the access level Special: the user (Registered) may not read it, the administrator may.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress mention ${stamp}', 'cypress-mention-${stamp}', '', '', '', '', '', 'joomla.level', 3, 3, 1, 8, 1, 91, 1, '{}', 'default')`,
    ).then((category) => {
      restricted = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-mention-${stamp}', 'catid', ${restricted}, 0)`);
    });

    // A new login: the categories which a user may read are kept in the session, and the category of this test is new.
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE 'Cypress mention % ${stamp}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE FROM #__kunena_inbox WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-mention-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE name = 'Cypress mention ${stamp}'`);
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
  });

  it('tells the user who is mentioned in a category which he may read', () => {
    const subject = `Cypress mention public ${stamp}`;

    mention(3, subject);
    mentionsOfTheUser(subject).then((rows) => expect(rows, 'the notification of the mention').to.have.length(1));
  });

  it('does not tell a user who may not read the category', () => {
    const subject = `Cypress mention restricted ${stamp}`;

    mention(restricted, subject);
    mentionsOfTheUser(subject).then((rows) => expect(rows, 'no notification for a user without the right to read').to.have.length(0));
  });
});
