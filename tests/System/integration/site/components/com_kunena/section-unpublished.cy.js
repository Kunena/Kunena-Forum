// A section (or category) which is not published hides what is in it: its subcategories and their topics are not shown either -
// not on their own pages and not in the list of the recent topics. Before, only the section itself was refused, and the topics of
// its categories stayed open to everybody by their addresses and in the lists.
describe('Kunena categories: an unpublished section hides its categories', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Insection ${stamp}`;
  const ids = {};

  const addCategory = (parent, name, published) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (${parent}, '${name} ${stamp}', '${name}-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, ${published}, '{}', 'default')`,
  ).then((category) => {
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('${name}-${stamp}', 'catid', ${category.insertId}, 0)`);

    return cy.wrap(category.insertId);
  });

  beforeEach(() => {
    addCategory(0, 'cypress-section', 0).then((section) => {
      ids.section = section;

      return addCategory(section, 'cypress-insection', 1);
    }).then((category) => {
      ids.category = category;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category}, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, 'ci-admin', 797, '', '${subject}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Text in the section.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    ['cypress-section', 'cypress-insection'].forEach((name) => cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = '${name}-${stamp}'`));
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id IN (${ids.section}, ${ids.category})`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id IN (${ids.section}, ${ids.category})`);
  });

  it('does not show a visitor the topic of a category in an unpublished section', () => {
    cy.then(() => cy.request({ url: `${base}&view=topic&catid=${ids.category}&id=${ids.topic}`, failOnStatusCode: false })).then((response) => {
      expect(response.status, 'the page of the topic').to.be.oneOf([401, 403, 404]);
    });
    cy.request(`${base}&view=topics&mode=topics`).its('body').should('not.include', subject);
  });

  it('shows it when the section is published', () => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET published = 1 WHERE id = ${ids.section}`));
    cy.then(() => cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`)).its('body').should('include', subject);
  });
});
