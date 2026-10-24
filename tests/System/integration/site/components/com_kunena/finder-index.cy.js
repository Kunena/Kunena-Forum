// The Smart Search index of the forum (plugin Finder - Kunena), made by the indexer of the console like the scheduler of Joomla
// does: the address of a message opens the message and does not carry the placeholder of the console for the root of the site,
// the key of an item is the one which the plugin removes when a message is deleted, and the messages of a category where the
// users see only their own topics (a helpdesk) are not in the results of everybody. The index of the test site is empty: the
// test empties it again, and switches the plugin back off.
describe('Kunena Smart Search: the index of the messages', () => {
  const stamp = Date.now();
  const secret = `Indexsecret${stamp}`;
  const ids = {};
  const finderTables = ['links', 'links_terms', 'logging', 'taxonomy_map', 'terms', 'tokens', 'tokens_aggregate', 'types'];

  beforeEach(() => {
    cy.task('queryDB', "SELECT enabled FROM #__extensions WHERE type = 'plugin' AND folder = 'finder' AND element = 'kunena'").then((rows) => {
      ids.enabled = rows[0].enabled;
    });
    cy.task('queryDB', "UPDATE #__extensions SET enabled = 1 WHERE type = 'plugin' AND folder = 'finder' AND element = 'kunena'");
    // A helpdesk with a question of ci-user.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress helpdesk ${stamp}', 'cypress-index-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{"ownTopicsOnly":"1"}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-index-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, '${secret}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 798, 'x', '', 0, UNIX_TIMESTAMP(), 798, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, 'ci-user', 798, '', '${secret}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'My private question.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
    });
    // A category in an unpublished section, with a topic.
    cy.task('queryDB', `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (0, 'Cypress off ${stamp}', 'cypress-off-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 0, '{}', 'default')`).then((section) => {
      ids.section = section.insertId;

      return cy.task('queryDB', `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (${section.insertId}, 'Cypress in off ${stamp}', 'cypress-inoff-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`);
    }).then((category) => {
      ids.inoff = category.insertId;

      return cy.task('queryDB', `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Indexoff${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`);
    }).then((topic) => {
      ids.offTopic = topic.insertId;

      return cy.task('queryDB', `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.inoff}, 'ci-admin', 797, '', 'Indexoff${stamp}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`);
    }).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'In a section which is off.')`));
    // A deleted reply and a reply which waits for approval in topic 9 (a public category); the queryDB task removes them.
    [['Indexdeleted', 2], ['Indexwaiting', 1]].forEach(([name, hold]) => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (10, 9, 3, 'ci-user', 798, '', '${name}${stamp}', UNIX_TIMESTAMP(), '', 0, 0, ${hold}, 0, 0, 0, 0, 0, '')`,
    ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Not for the search.')`)));
  });

  afterEach(() => {
    // The index as it was: empty, with the root of the taxonomy.
    finderTables.forEach((table) => cy.task('queryDB', `DELETE FROM #__finder_${table}`));
    cy.task('queryDB', 'DELETE FROM #__finder_taxonomy WHERE id > 1');
    cy.task('queryDB', 'UPDATE #__finder_taxonomy SET lft = 0, rgt = 1 WHERE id = 1');
    cy.task('queryDB', 'ALTER TABLE #__finder_taxonomy AUTO_INCREMENT = 2');
    cy.then(() => cy.task('queryDB', `UPDATE #__extensions SET enabled = ${Number(ids.enabled)} WHERE type = 'plugin' AND folder = 'finder' AND element = 'kunena'`));
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id IN (${ids.topic}, ${ids.offTopic})`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id IN (${ids.section}, ${ids.inoff})`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-index-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('indexes a message with its key and an address which opens it, and keeps the helpdesk out of the results', () => {
    cy.task('joomlaCli', ['finder:index'], { timeout: 300000 });

    // Message 10 of the test site: the first post of topic 9 (a public category).
    cy.task('queryDB', "SELECT url, route, state FROM #__finder_links WHERE url LIKE '%option=com_kunena%id=10' OR route LIKE '%mesid=10&%' OR route LIKE '%mesid=10'").then((rows) => {
      expect(rows, 'the message of the test site in the index').to.have.length(1);
      expect(rows[0].url, 'the key of the item').to.eq('index.php?option=com_kunena&view=topic&id=10');
      expect(rows[0].route, 'the address of the message').to.match(/^index\.php\?option=com_kunena&view=topic&catid=3&id=9&mesid=10/);
      expect(Number(rows[0].state), 'in the results').to.eq(1);
    });
    cy.task('queryDB', `SELECT state FROM #__finder_links WHERE title = '${secret}'`).then((rows) => {
      expect(rows, 'the helpdesk question in the index').to.have.length(1);
      expect(Number(rows[0].state), 'the helpdesk question in the results').to.eq(0);
    });
    ['Indexdeleted', 'Indexwaiting', 'Indexoff'].forEach((name) => cy.task('queryDB', `SELECT state FROM #__finder_links WHERE title = '${name}${stamp}'`).then((rows) => {
      expect(rows, `${name}: in the index`).to.have.length(1);
      expect(Number(rows[0].state), `${name}: in the results`).to.eq(0);
    }));
    cy.task('queryDB', "SELECT COUNT(*) AS n FROM #__finder_links WHERE url LIKE '%set/by/console%' OR route LIKE '%set/by/console%'").its('0.n').should('eq', 0);
  });
});
