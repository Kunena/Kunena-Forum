// [tableau] shows a visualization of Tableau Public, sized by a script after it. Each visualization had the same id, so on a page with
// two of them (two messages of a topic) the script of the second one sized the first one again and left the second one without its
// size. The script of Tableau is not loaded in the test (the objects stay as the page made them); the rows are removed after it.
describe('Kunena BBCode: two [tableau] on one page', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = { messages: [] };

  const message = (topic, parent, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${topic}, 3, 'ci-user', ${user.id}, '', 'Cypress tableau ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((result) => {
    ids.messages.push(result.insertId);
    cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${result.insertId}, '${text}')`);

    return cy.wrap(result.insertId);
  });

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress tableau ${stamp}', 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return message(topic.insertId, 0, '[tableau]https://public.tableau.com/views/CypressFirst/Sheet1[/tableau]');
    }).then((first) => message(ids.topic, first, '[tableau]https://public.tableau.com/views/CypressSecond/Sheet2[/tableau]'))
      .then((second) => {
        cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.messages[0]}, last_post_id = ${second} WHERE id = ${ids.topic}`);
      });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('gives each visualization its own id and its own size', () => {
    cy.intercept('https://public.tableau.com/**', { statusCode: 404, body: '' });
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&catid=3&id=${ids.topic}`);

    cy.get('.tableauPlaceholder').should('have.length', 2).then(($boxes) => {
      expect($boxes[0].id, 'the ids').to.not.eq($boxes[1].id);
    });
    cy.get('.tableauPlaceholder object.tableauViz').each(($object) => {
      expect($object[0].style.height, 'the height of the visualization').to.match(/px$/);
    });
  });
});
