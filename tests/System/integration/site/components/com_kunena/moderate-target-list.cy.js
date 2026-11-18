// The list of the target topics on the page which moderates a topic (topic.js): when another category is chosen, its topics are
// loaded. The topic which is moderated came in it too, when its own category was chosen again, and a merge into itself is refused
// by the server ("the same topic"). The list of the page leaves it out, and the loaded list now does the same.
describe('Kunena moderation: the target topics of the moderate page', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};

  const choose = (category) => {
    cy.intercept('GET', /format=json/).as('topics');
    cy.get('#kmod_categories').select(String(category));
    cy.wait('@topics').its('response.statusCode').should('eq', 200);
  };
  const targets = () => cy.get('#kmod_topics option').then(($options) => [...$options].map((option) => option.value));

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress moderate list ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', 'Cypress moderate list ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
    });
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('does not offer the topic which is moderated as its own target', () => {
    cy.visit(`${base}&view=topic&layout=moderate&catid=3&id=${ids.topic}`);
    targets().should('not.include', String(ids.topic));
    // The move options are on their own tab.
    cy.get('#tab2-tab').click();
    cy.get('#kmod_categories').should('be.visible');

    // Another category, and the own category again: the loaded list has the other topics of the category, not this one.
    choose(4);
    choose(3);
    targets().should('not.include', String(ids.topic)).and('have.length.greaterThan', 2);
  });
});
