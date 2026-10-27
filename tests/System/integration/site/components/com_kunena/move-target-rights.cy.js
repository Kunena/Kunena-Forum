// A moderator of a category moves a topic into another category, or merges it into another topic. The target needed only the
// right to read it: a moderator of one category put topics in a category in which he may not write (a locked category of the
// announcements), and posts in a topic which he may not answer, of a category which he does not moderate.
describe('Kunena moderation: the rights on the target of a move', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const target = { category: 4, topic: 2 };
  const old = {};
  let topic;

  const addTopic = () => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress move ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
  ).then((result) => {
    topic = result.insertId;

    return cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic}, 3, 'ci-admin', 797, '', 'Cypress move ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    );
  }).then((message) => {
    cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`);
    cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic}`);
  });

  const move = (body) => cy.request(`${base}&view=topic&layout=moderate&catid=3&id=${topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=move`,
      form: true,
      body: { id: topic, catid: 3, [token]: '1', ...body },
      failOnStatusCode: false,
      followRedirect: false,
    });
  });

  const where = () => cy.task('queryDB', `SELECT category_id, moved_id FROM #__kunena_topics WHERE id = ${topic}`).then((rows) => rows[0]);
  const targetPosts = () => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_messages WHERE thread = ${target.topic}`).then((rows) => Number(rows[0].n));

  before(() => {
    cy.task('queryDB', `SELECT locked FROM #__kunena_categories WHERE id = ${target.category}`).then((rows) => {
      old.locked = Number(rows[0].locked);
    });
    // The user moderates the category 3 only.
    cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${user.id}, 3, 1, 0, 0, '') ON DUPLICATE KEY UPDATE role = 1`);
  });

  after(() => {
    cy.task('queryDB', `UPDATE #__kunena_user_categories SET role = 0 WHERE user_id = ${user.id} AND category_id = 3`);
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET locked = ${old.locked} WHERE id = ${target.category}`));
    cy.kunena_fixCategoryCounters();
  });

  beforeEach(() => {
    addTopic();
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE 'Cypress move ${stamp}' OR moved_id = ${topic}`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    // A merge would have put the message in the target topic.
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${target.topic} AND m.subject = 'Cypress move ${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${target.topic} AND subject = 'Cypress move ${stamp}'`);
  });

  it('does not move a topic into a category in which the moderator may not write', () => {
    cy.task('queryDB', `UPDATE #__kunena_categories SET locked = 1 WHERE id = ${target.category}`);
    cy.doFrontendLogin(user.name, user.password, false);
    move({ targetcategory: target.category });
    where().its('category_id').should('eq', 3);
  });

  it('does not merge a topic into a topic which the moderator may not answer', () => {
    cy.task('queryDB', `UPDATE #__kunena_categories SET locked = 1 WHERE id = ${target.category}`);
    cy.doFrontendLogin(user.name, user.password, false);
    targetPosts().then((before) => {
      move({ targettopic: target.topic });
      targetPosts().should('eq', before);
    });
  });

  // The moderation of the topic lists moves the chosen topics with a task of its own.
  it('does not move topics of a list into a category in which the moderator may not write', () => {
    cy.task('queryDB', `UPDATE #__kunena_categories SET locked = 1 WHERE id = ${target.category}`);
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=category&catid=3`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      cy.request({
        method: 'POST',
        url: `${base}&view=topics&task=move`,
        form: true,
        body: { [`topics[${topic}]`]: '1', target: target.category, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    where().its('category_id').should('eq', 3);
  });

  it('moves a topic into a category in which the moderator may write', () => {
    cy.task('queryDB', `UPDATE #__kunena_categories SET locked = 0 WHERE id = ${target.category}`);
    cy.doFrontendLogin(user.name, user.password, false);
    move({ targetcategory: target.category });
    where().its('category_id').should('eq', target.category);
  });
});
