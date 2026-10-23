// What a user has not read yet: a new topic of another user is unread, it is read after the user has opened it, a new reply makes it
// unread again, and "mark as read" of the category clears it. The list of the unread topics is what the user sees.
describe('Kunena unread: the topics a user has not read', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress unread ${stamp}`;
  const user = { name: 'ci-user', password: 'ci-user-17082005-x' };
  const ids = {};

  const typeInEditor = (value) => {
    cy.window().should('have.property', 'sceditor');
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty')
      .then(($body) => cy.wrap($body).click().type(value, { parseSpecialCharSequences: false }));
  };

  const asUser = (name, password) => {
    cy.clearAllCookies();
    cy.doFrontendLogin(name, password, false);
  };
  const asAdmin = () => asUser(Cypress.expose('username'), Cypress.expose('password'));

  const unreadList = () => cy.request(`${base}&view=topics&layout=unread`).then((response) => String(response.body).includes(subject));

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress category unread ${stamp}', 'cypress-unread-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 98, 1, '{}', 'default')`,
    ).then((result) => {
      ids.category = result.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-unread-${stamp}', 'catid', ${result.insertId}, 0)`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${ids.category}`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-unread-${stamp}'`);
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    // The counters of the users and the rows of the topics which are gone.
    cy.kunena_fixUserCounters();
  });

  it('is unread for another user, read after he opened it, unread after a reply, and read after mark as read', () => {
    // The user is online before the topic is written.
    asUser(user.name, user.password);
    cy.request(`${base}&view=home`);

    asAdmin();
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    cy.get('#postform input[name="subject"]').type(subject);
    typeInEditor('The first message.');
    cy.get('#form_submit_button').should('not.be.disabled').click();
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      ids.topic = rows[0].id;
    });

    asUser(user.name, user.password);
    cy.then(() => unreadList()).should('eq', true);

    // He opens the topic.
    cy.then(() => cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`));
    cy.then(() => unreadList()).should('eq', false);

    // A new reply of the administrator.
    asAdmin();
    cy.then(() => cy.visit(`${base}&view=topic&layout=reply&catid=${ids.category}&id=${ids.topic}`));
    typeInEditor('A new reply.');
    cy.get('#form_submit_button').should('not.be.disabled').click();

    asUser(user.name, user.password);
    cy.then(() => unreadList()).should('eq', true);

    // He marks the category as read.
    cy.then(() => cy.request(`${base}&view=category&catid=${ids.category}`)).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({ url: `${base}&view=category&catid=${ids.category}&task=markread&${token}=1`, failOnStatusCode: false });
    });
    cy.then(() => unreadList()).should('eq', false);
  });
});
