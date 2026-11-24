// A topic of a channel opened in the category which shows the channel (catid of the address): the count of its messages and its
// pages took the states which the user may see in the category of the address. A moderator of that category, who does not
// moderate the channel, got the unapproved (and, when moderators see them, the deleted) replies of the topic in the count: more
// pages than messages (the messages themselves are left out by their read check), and a last page without messages.
describe('Kunena channels: the pages of a topic of a channel', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};

  const addCategory = (name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress count ${name} ${stamp}', 'cypress-count-${name}-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 97, 1, '{}', 'default')`,
  ).then((category) => {
    ids[name] = category.insertId;
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-count-${name}-${stamp}', 'catid', ${category.insertId}, 0)`);
  });

  const addMessage = (parent, hold, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, ${ids.channel}, 'ci-admin', 797, '', 'Cypress count ${stamp}', UNIX_TIMESTAMP() + ${parent ? 1 : 0}, '127.0.0.1', 0, 0, ${hold}, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`)
    .then(() => message.insertId));

  beforeEach(() => {
    addCategory('own');
    addCategory('channel');
    // The category of ci-user shows the topics of the other category; he moderates his category only.
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET channels = 'THIS,${ids.channel}' WHERE id = ${ids.own}`));
    cy.then(() => cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${user.id}, ${ids.own}, 1, 0, 0, '')`));
    cy.then(() => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.channel}, 'Cypress count ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    )).then((topic) => {
      ids.topic = topic.insertId;
    });
    cy.then(() => addMessage(0, 0, 'The first message')).then((id) => {
      ids.first = id;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${id}, last_post_id = ${id} WHERE id = ${ids.topic}`);
    });
    // A reply waiting for approval, which only the moderators of the channel see.
    cy.then(() => addMessage(ids.first, 1, 'The unapproved reply'));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    ['own', 'channel'].forEach((name) => {
      cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-count-${name}-${stamp}'`);
      cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids[name]}`);
    });
  });

  it('counts only the messages he may see in the category of the topic', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => cy.request(`${base}&view=topic&catid=${ids.own}&id=${ids.topic}&limit=1`)).then((page) => {
      const body = String(page.body);

      expect(body, 'the first message').to.contain('The first message');
      expect(body, 'the unapproved reply').not.to.contain('The unapproved reply');
      // One message he may see: one page, no link to a second one.
      expect(/[?&;](limitstart|start)=1(&|"|$)/.test(body), 'a link to a second page').to.eq(false);
    });
  });
});
