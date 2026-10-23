// The lists of topics: a sticky topic stays at the top of its category (also when other topics are newer), the list of the
// favourite topics of a user shows his favourites only, and "mark all read" of a category sets the time from which its topics
// count as read for the user.
describe('Kunena topic lists: sticky, favourites and mark read', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};

  const addTopic = (name, ordering, age) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, 'Cypress list ${name} ${stamp}', 0, 0, 0, 0, ${ordering}, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - ${age}, 797, 'x', '', 0, UNIX_TIMESTAMP() - ${age}, 797, 'x', '', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, 'ci-admin', 797, '', 'Cypress list ${name} ${stamp}', UNIX_TIMESTAMP() - ${age}, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
  )).then(() => topic.insertId)));

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress lists ${stamp}', 'cypress-lists-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 90, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-lists-${stamp}', 'catid', ${category.insertId}, 0)`);

      return addTopic('sticky', 1, 7200);
    }).then((topic) => {
      ids.sticky = topic;

      return addTopic('newer', 0, 60);
    }).then((topic) => {
      ids.newer = topic;
      cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 2, numPosts = 2, last_topic_id = ${topic}, last_post_time = UNIX_TIMESTAMP() - 60 WHERE id = ${ids.category}`);
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
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-lists-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('shows the sticky topic above a newer topic in its category', () => {
    cy.request(`${base}&view=category&catid=${ids.category}`).its('body').then((body) => {
      const page = String(body);
      const sticky = page.indexOf(`Cypress list sticky ${stamp}`);
      const newer = page.indexOf(`Cypress list newer ${stamp}`);

      expect(sticky, 'the sticky topic on the page').to.be.greaterThan(-1);
      expect(newer, 'the newer topic on the page').to.be.greaterThan(-1);
      expect(sticky, 'the sticky topic first').to.be.lessThan(newer);
    });
  });

  it('lists the favourite topics of a user, and no other topic', () => {
    cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${user.id}, ${ids.newer}, ${ids.category}, 0, 0, 0, 1, 0, '')`);
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=topics&layout=user&mode=favorites&userid=${user.id}`).its('body').then((body) => {
      expect(String(body), 'the favourite topic').to.include(`Cypress list newer ${stamp}`);
      expect(String(body), 'a topic which is no favourite').to.not.include(`Cypress list sticky ${stamp}`);
    });
  });

  it('marks the topics of a category read for the user', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=category&catid=${ids.category}`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

      return cy.request({ url: `${base}&view=category&task=markread&catid=${ids.category}&${token}=1`, failOnStatusCode: false, followRedirect: false });
    });
    cy.task('queryDB', `SELECT allreadtime FROM #__kunena_user_categories WHERE user_id = ${user.id} AND category_id = ${ids.category}`).then((rows) => {
      expect(rows, 'the row of the user for the category').to.have.length(1);
      expect(Number(rows[0].allreadtime), 'the time of "all read"').to.be.greaterThan(Math.floor(Date.now() / 1000) - 600);
    });
  });
});
