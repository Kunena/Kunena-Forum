// The filter bar of the topic lists (fq: subject, fauthor: author, fstatus: status). A % or _ in the text is that character, not
// a wildcard, the author is found by name, and an unknown status filters nothing.
describe('Kunena topic lists: the filter bar', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const subjects = { done: `Cypress filter 100% done ${stamp}`, plain: `Cypress filter plain ${stamp}` };

  // A topic in the category of the test; replies are counted only (posts), the list does not show them.
  const addTopic = (subject, userid, name, extra) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, '${subject}', 0, 0, ${extra.locked}, 0, ${extra.ordering}, ${extra.posts}, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - 60, ${userid}, 'x', '', 0, UNIX_TIMESTAMP() - 60, ${userid}, 'x', '', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, '${name}', ${userid}, '', '${subject}', UNIX_TIMESTAMP() - 60, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
  ))));

  // The subjects of the test which the recent topics list shows with the filters.
  const listed = (filters) => cy.request(`${base}&view=topics&layout=default&mode=topics&${filters}`).its('body').then((body) => Object.keys(subjects).filter((key) => String(body).includes(subjects[key])));

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress filter ${stamp}', 'cypress-filter-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 93, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-filter-${stamp}', 'catid', ${category.insertId}, 0)`);
      addTopic(subjects.done, 797, 'ci-admin', { locked: 1, ordering: 0, posts: 2 });
      addTopic(subjects.plain, 798, 'ci-user', { locked: 0, ordering: 1, posts: 1 });
      cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 2, numPosts = 3, last_post_time = UNIX_TIMESTAMP() - 60 WHERE id = ${category.insertId}`);
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
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-filter-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('lists both topics without a filter, and with a status which does not exist', () => {
    listed('').should('deep.eq', ['done', 'plain']);
    listed('fstatus=nonsense').should('deep.eq', ['done', 'plain']);
  });

  it('filters on the subject, with % and _ as plain characters', () => {
    listed(`fq=${encodeURIComponent(`100% done ${stamp}`)}`).should('deep.eq', ['done']);
    listed(`fq=${encodeURIComponent(`filter%plain ${stamp}`)}`).should('deep.eq', []);
    listed(`fq=${encodeURIComponent(`filter_plain ${stamp}`)}`).should('deep.eq', []);
    listed(`fq=${encodeURIComponent(`filter plain ${stamp}`)}`).should('deep.eq', ['plain']);
  });

  it('filters on the author', () => {
    listed('fauthor=ci-user').should('deep.eq', ['plain']);
    listed('fauthor=ci-admin').should('deep.eq', ['done']);
    listed(`fauthor=nobody${stamp}`).should('deep.eq', []);
  });

  it('filters on the status', () => {
    listed('fstatus=locked').should('deep.eq', ['done']);
    listed('fstatus=sticky').should('deep.eq', ['plain']);
    listed('fstatus=noreplies').should('deep.eq', ['plain']);
    listed('fstatus=replied').should('deep.eq', ['done']);
  });
});
