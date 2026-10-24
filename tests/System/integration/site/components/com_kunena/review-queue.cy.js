// A category with review: the topic of a user waits for a moderator. Until it is approved only the author and the moderators see it,
// it is in no list and no count; after the approval it is a topic like the others. A change of an approved message by its author
// (who is no moderator) sends the message back to the moderators.
describe('Kunena review: topics and messages which wait for a moderator', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress waiting topic ${stamp}`;
  const text = `REVIEWTEXT${stamp}`;
  const user = { name: 'ci-user', password: 'ci-user-17082005-x' };
  const ids = {};

  const typeInEditor = (value) => {
    cy.window().should('have.property', 'sceditor');
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty')
      .then(($body) => cy.wrap($body).click().type(value, { parseSpecialCharSequences: false }));
  };

  const topic = () => cy.task('queryDB', `SELECT id, hold, posts, first_post_id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => rows[0]);
  const category = () => cy.task('queryDB', `SELECT numTopics, numPosts FROM #__kunena_categories WHERE id = ${ids.category}`).then((rows) => ({ topics: Number(rows[0].numTopics), posts: Number(rows[0].numPosts) }));

  // As a visitor without a login.
  const visitorSees = (url, needle = text) => {
    cy.clearAllCookies();

    return cy.request({ url, failOnStatusCode: false }).then((response) => String(response.body).includes(needle));
  };

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, review, params, iconset) VALUES (1, 'Cypress review ${stamp}', 'cypress-review-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 97, 1, 1, '{}', 'default')`,
    ).then((result) => {
      ids.category = result.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-review-${stamp}', 'catid', ${result.insertId}, 0)`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${ids.category}`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-review-${stamp}'`);
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    // The rows of the users and the notifications of the inbox about the messages which are gone.
    cy.kunena_fixUserCounters();
  });

  it('keeps a new topic back until it is approved', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    cy.get('#postform input[name="subject"]').type(subject);
    typeInEditor(text);
    cy.get('#form_submit_button').should('not.be.disabled').click();

    topic().then((row) => {
      ids.topic = row.id;
      expect(Number(row.hold), 'the topic waits').to.eq(1);
    });
    category().then((numbers) => expect(numbers, 'the category does not count it').to.deep.eq({ topics: 0, posts: 0 }));

    // The visitors do not see it (the author does not either: the topic has a TODO for that, "Allow owner to see his posts").
    [
      () => `${base}&view=topic&catid=${ids.category}&id=${ids.topic}`,
      () => `${base}&view=topics`,
      () => `${base}&view=category&catid=${ids.category}`,
      () => `${base}&view=search&task=results&query=${text}`,
    ].forEach((url) => {
      // The search page shows the words which were searched: the subject of the topic is what a result would show.
      cy.then(() => visitorSees(url(), url().includes('view=search') ? subject : text)).then((seen) => expect(seen, `a visitor sees the text on ${url().replace(base, '')}`).to.eq(false));
    });
  });

  it('shows the topic and counts it after the approval, and sends a change of the author back to the moderators', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    cy.get('#postform input[name="subject"]').type(subject);
    typeInEditor(text);
    cy.get('#form_submit_button').should('not.be.disabled').click();
    topic().then((row) => {
      ids.topic = row.id;
      ids.first = row.first_post_id;
    });

    // The administrator approves it.
    cy.clearAllCookies();
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.then(() => cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`)).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({ url: `${base}&view=topic&catid=${ids.category}&id=${ids.topic}&task=approve&${token}=1`, failOnStatusCode: false, followRedirect: false });
    });
    topic().then((row) => expect(Number(row.hold), 'approved').to.eq(0));
    category().then((numbers) => expect(numbers, 'counted after the approval').to.deep.eq({ topics: 1, posts: 1 }));
    cy.then(() => visitorSees(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`)).should('eq', true);

    // The author changes his message: it waits again.
    cy.clearAllCookies();
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.first}`));
    typeInEditor(' Changed.');
    cy.get('#form_submit_button').should('not.be.disabled').click();
    cy.then(() => cy.task('queryDB', `SELECT hold FROM #__kunena_messages WHERE id = ${ids.first}`)).then((rows) => expect(Number(rows[0].hold), 'the changed message waits').to.eq(1));
  });
});
