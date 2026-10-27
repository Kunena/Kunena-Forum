// The channels of a category are other categories of which the topics are shown in it. A moderator of the category saw the
// unapproved and deleted messages of its channels, which he does not moderate:
// - the search of the unapproved (show=1) and deleted (show=2) messages looked in the channels of the categories which he moderates;
// - the list of the topics of the category took the topics of the channels with the states which he may see in the category.
// Both use the rights of the user in the category of each message now.
describe('Kunena channels: the unapproved messages of a channel', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const word = `zqxheld${stamp}`;
  const ids = {};

  const addCategory = (name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress channel ${name} ${stamp}', 'cypress-channel-${name}-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 97, 1, '{}', 'default')`,
  ).then((category) => {
    ids[name] = category.insertId;
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-channel-${name}-${stamp}', 'catid', ${category.insertId}, 0)`);
  });

  // A topic of ci-admin of which the first message waits for the approval (hold 1) or was deleted (hold 2).
  const addHeld = (name, hold) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids[name]}, 'Cypress held ${name} ${hold} ${stamp}', 0, 0, 0, ${hold}, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids[name]}, 'ci-admin', 797, '', 'Cypress held ${name} ${hold} ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, ${hold}, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A held message ${word}')`)));

  const found = (show) => cy.request({ url: `${base}&view=search&task=results&query=${word}&searchdate=all&show=${show}`, failOnStatusCode: false }).then((response) => {
    expect(response.status, 'the status').to.be.lessThan(500);

    return ['own', 'channel'].filter((name) => String(response.body).includes(`Cypress held ${name} ${show} ${stamp}`));
  });

  beforeEach(() => {
    addCategory('own');
    addCategory('channel');
    // The category of ci-user shows the messages of the other category too.
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET channels = 'THIS,${ids.channel}' WHERE id = ${ids.own}`));
    cy.then(() => cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${user.id}, ${ids.own}, 1, 0, 0, '')`));
    [1, 2].forEach((hold) => {
      cy.then(() => addHeld('own', hold));
      cy.then(() => addHeld('channel', hold));
    });
  });

  afterEach(() => {
    cy.then(() => {
      if (ids.note) {
        cy.task('queryDB', `DELETE FROM #__kunena_private_post_map WHERE private_id = ${ids.note}`);
        cy.task('queryDB', `DELETE FROM #__kunena_private_user_map WHERE private_id = ${ids.note}`);
        cy.task('queryDB', `DELETE FROM #__kunena_private WHERE id = ${ids.note}`);
        ids.note = 0;
      }
    });
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE 'Cypress held % ${stamp}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    ['own', 'channel'].forEach((name) => {
      cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-channel-${name}-${stamp}'`);
      cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids[name]}`);
    });
  });

  it('finds the unapproved and deleted messages of his category only', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    found(1).should('deep.eq', ['own']);
    found(2).should('deep.eq', ['own']);
  });

  // The private part of a post (for its author and the moderators of its category) was shown to the moderators of the category
  // of the address, which may be a category of which the topic is a channel.
  it('shows no private part of a post of the channel, opened in his category', () => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.channel}, 'Cypress held private ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    ).then((topic) => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.channel}, 'ci-admin', 797, '', 'Cypress held private ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      ids.topic = topic.insertId;
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'The open message')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`);
      cy.task('queryDB', `INSERT INTO #__kunena_private (parent_id, author_id, created_at, attachments, subject, body, params) VALUES (0, 797, NOW(), 0, '', 'zqxnote${stamp}', '{"receivers":{"users":[797],"posts":[${message.insertId}]}}')`).then((note) => {
        ids.note = note.insertId;
        cy.task('queryDB', `INSERT INTO #__kunena_private_user_map (private_id, user_id) VALUES (${note.insertId}, 797)`);
        cy.task('queryDB', `INSERT INTO #__kunena_private_post_map (private_id, message_id) VALUES (${note.insertId}, ${message.insertId})`);
      });
    }));
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => cy.request(`${base}&view=topic&catid=${ids.own}&id=${ids.topic}`)).then((page) => {
      expect(page.body, 'the open message').to.contain('The open message');
      expect(page.body, 'the private part').not.to.contain(`zqxnote${stamp}`);
    });
  });

  it('lists in his category the unapproved topics of his category only', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => cy.request(`${base}&view=category&catid=${ids.own}`)).then((page) => {
      expect(page.body, 'the unapproved topic of his category').to.contain(`Cypress held own 1 ${stamp}`);
      expect(page.body, 'the unapproved topic of the channel').not.to.contain(`Cypress held channel 1 ${stamp}`);
      expect(page.body, 'the deleted topic of the channel').not.to.contain(`Cypress held channel 2 ${stamp}`);
    });
  });
});
