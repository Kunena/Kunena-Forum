// A topic merged into another topic brings its messages, but its author does not become an owner of the other topic: the owner
// of a topic (#__kunena_user_topics.owner) may edit its poll and its icon. After the merge the author of the merged topic has his
// posts in the target topic, and owner 0.
describe('Kunena moderation: a merge does not make the author of the merged topic an owner', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = 798;
  const admin = 797;
  const ids = {};

  const addMessage = (parent, thread, userid, name, subject, age) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${thread}, ${ids.category}, '${name}', ${userid}, '', '${subject}', UNIX_TIMESTAMP() - ${age}, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => {
    cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Text of ${subject}.')`);

    return cy.wrap(message.insertId);
  });

  const addTopic = (userid, name, subject, age) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - ${age}, ${userid}, 'x', '', 0, UNIX_TIMESTAMP() - ${age}, ${userid}, 'x', '', 0, '')`,
  ).then((topic) => addMessage(0, topic.insertId, userid, name, subject, age).then((message) => {
    cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message}, last_post_id = ${message} WHERE id = ${topic.insertId}`);
    cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${userid}, ${topic.insertId}, ${ids.category}, 1, ${message}, 1, 0, 0, '')`);

    return cy.wrap({ topic: topic.insertId, message });
  }));

  const ownerRow = (userid, topic) => cy.task('queryDB', `SELECT posts, owner FROM #__kunena_user_topics WHERE user_id = ${userid} AND topic_id = ${topic}`).then((rows) => rows.map((row) => ({ posts: Number(row.posts), owner: Number(row.owner) })));

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress merge ${stamp}', 'cypress-merge-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-merge-${stamp}', 'catid', ${category.insertId}, 0)`);
    });
    // The target: a topic of the administrator with a reply of ci-user. The source: a topic of ci-user.
    cy.then(() => addTopic(admin, 'ci-admin', `Merge target ${stamp}`, 600)).then((target) => {
      ids.target = target.topic;

      return addMessage(target.message, target.topic, user, 'ci-user', `Re: Merge target ${stamp}`, 500);
    }).then((reply) => {
      cy.task('queryDB', `UPDATE #__kunena_topics SET posts = 2, last_post_id = ${reply}, last_post_userid = ${user} WHERE id = ${ids.target}`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${user}, ${ids.target}, ${ids.category}, 1, ${reply}, 0, 0, 0, '')`);
    });
    cy.then(() => addTopic(user, 'ci-user', `Merge source ${stamp}`, 400)).then((source) => {
      ids.source = source.topic;
      cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 2, numPosts = 3 WHERE id = ${ids.category}`);
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
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id IN (${ids.source}, ${ids.target})`);
    cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-merge-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.kunena_fixUserCounters();
  });

  const merge = () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.then(() => cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.source}`)).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=move`,
        form: true,
        body: { catid: ids.category, id: ids.source, targetcategory: ids.category, targettopic: -1, targetid: ids.target, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
  };

  it('gives the author of the merged topic his posts in the target, and no ownership of it', () => {
    merge();
    cy.then(() => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_messages WHERE thread = ${ids.target}`)).its('0.n').should('eq', 3);
    cy.then(() => ownerRow(user, ids.target)).should('deep.eq', [{ posts: 2, owner: 0 }]);
    cy.then(() => ownerRow(admin, ids.target)).should('deep.eq', [{ posts: 1, owner: 1 }]);
  });

  it('makes no owner of the target of a user who had not written in it', () => {
    // ci-user has no reply in the target: the merge makes a new row for him.
    cy.then(() => cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.target} AND m.userid = ${user}`));
    cy.then(() => cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.target} AND userid = ${user}`));
    cy.then(() => cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.target} AND user_id = ${user}`));
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_topics SET posts = 1, last_post_id = first_post_id, last_post_userid = ${admin} WHERE id = ${ids.target}`));
    merge();
    cy.then(() => ownerRow(user, ids.target)).should('deep.eq', [{ posts: 1, owner: 0 }]);
  });

  it('makes the author of the first post of a split topic its owner', () => {
    // The reply of ci-user in the target goes to a new topic: he started it.
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.then(() => cy.task('queryDB', `SELECT id FROM #__kunena_messages WHERE thread = ${ids.target} AND userid = ${user}`)).then((rows) => {
      ids.reply = rows[0].id;

      return cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.target}`);
    }).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=move`,
        form: true,
        body: { catid: ids.category, id: ids.target, mesid: ids.reply, targetcategory: ids.category, targettopic: 0, targetid: '', subject: `Split ${stamp}`, mode: 'selected', [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.then(() => cy.task('queryDB', `SELECT thread FROM #__kunena_messages WHERE id = ${ids.reply}`)).then((rows) => {
      expect(Number(rows[0].thread), 'the reply is in a new topic').to.not.eq(ids.target);
      ownerRow(user, Number(rows[0].thread)).should('deep.eq', [{ posts: 1, owner: 1 }]);
    });
  });

  it('makes the author of an older merged topic the owner, as his post becomes the first one of the target', () => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_messages SET time = UNIX_TIMESTAMP() - 900 WHERE thread = ${ids.source}`));
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_time = UNIX_TIMESTAMP() - 900, last_post_time = UNIX_TIMESTAMP() - 900 WHERE id = ${ids.source}`));
    merge();
    cy.then(() => cy.task('queryDB', `SELECT first_post_userid FROM #__kunena_topics WHERE id = ${ids.target}`)).its('0.first_post_userid').should('eq', user);
    cy.then(() => ownerRow(user, ids.target)).should('deep.eq', [{ posts: 2, owner: 1 }]);
    cy.then(() => ownerRow(admin, ids.target)).should('deep.eq', [{ posts: 1, owner: 0 }]);
  });
});
