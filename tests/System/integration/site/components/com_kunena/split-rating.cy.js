// A moderator splits replies of a rated topic into a new topic. The votes stay with the old topic, so the new topic has no rating:
// it showed the stars of the old topic with no votes.
describe('Kunena ratings: a topic split from a rated topic', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};

  // A topic of the admin with two replies, rated 4 by one vote. The messages are a minute apart, for the split of the newer ones.
  const addTopic = () => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, 'Cypress split rating ${stamp}', 0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - 300, 797, 'x', '', 0, UNIX_TIMESTAMP() - 100, 797, 'x', '', 4, '')`,
  ).then((topic) => {
    ids.topic = topic.insertId;
    ids.messages = [];
    cy.task('queryDB', `INSERT INTO #__kunena_rate (topic_id, userid, rate, time) VALUES (${ids.topic}, 798, 4, NOW())`);
    [300, 200, 100].forEach((age, index) => cy.then(() => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${index ? ids.messages[0] : 0}, ${ids.topic}, ${ids.category}, 'ci-admin', 797, '', 'Cypress split rating ${stamp}', UNIX_TIMESTAMP() - ${age}, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    )).then((message) => {
      ids.messages.push(message.insertId);
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Message ${index} of the split.')`);
    }));
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.messages[0]}, last_post_id = ${ids.messages[2]} WHERE id = ${ids.topic}`));
  });

  // The form of the moderator sends these fields for a split into a new topic of the same category.
  const split = (mesid, mode) => cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=move`,
      form: true,
      body: { id: ids.topic, catid: ids.category, mesid, targetcategory: ids.category, targettopic: 0, targetid: '', subject: `Cypress split rating new ${stamp}`, mode, [token]: '1' },
      failOnStatusCode: false,
      followRedirect: false,
    });
  });

  const rating = (topic) => cy.task('queryDB', `SELECT rating, (SELECT COUNT(*) FROM #__kunena_rate WHERE topic_id = ${topic}) AS votes FROM #__kunena_topics WHERE id = ${topic}`)
    .then((rows) => ({ rating: Number(rows[0].rating), votes: Number(rows[0].votes) }));

  const expectSplit = (moved) => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${ids.category} AND id <> ${ids.topic}`).then((rows) => {
      expect(rows, 'the new topic').to.have.length(1);
      cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_messages WHERE thread = ${rows[0].id}`).then((count) => Number(count[0].n)).should('eq', moved);
      rating(rows[0].id).should('deep.eq', { rating: 0, votes: 0 });
    });
    cy.then(() => rating(ids.topic)).should('deep.eq', { rating: 4, votes: 1 });
  };

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress split rating ${stamp}', 'cypress-split-rating-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 95, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-split-rating-${stamp}', 'catid', ${category.insertId}, 0)`);
    });
    cy.then(() => addTopic());
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${ids.category}`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE FROM #__kunena_rate WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-split-rating-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('gives a reply split into a new topic no rating, and the old topic keeps its rating', () => {
    cy.then(() => split(ids.messages[1], 'selected'));
    expectSplit(1);
  });

  it('gives the newer replies split into a new topic no rating, and the old topic keeps its rating', () => {
    cy.then(() => split(ids.messages[2], 'newer'));
    expectSplit(1);
  });
});
