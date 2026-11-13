// The option topicShowModerators shows or hides the line "Moderators: ..." at the bottom of a topic.
describe('Kunena topic: the option which shows the moderators', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { id: 798 };
  const ids = {};
  let optionBefore;

  const page = () => `${base}&view=topic&catid=${ids.category}&id=${ids.topic}`;

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress topic mods ${stamp}', 'cypress-topic-mods-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 98, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;

      return cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-topic-mods-${stamp}', 'catid', ${ids.category}, 0)`);
    }).then(() => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, 'Cypress topic mods ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, '')`,
    )).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, ${ids.category}, 'ci-admin', 797, '', 'Cypress topic mods ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;

      return cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${ids.message}, 'x')`);
    }).then(() => cy.task(
      'queryDB',
      `UPDATE #__kunena_topics SET first_post_id = ${ids.message}, last_post_id = ${ids.message} WHERE id = ${ids.topic}`,
    )).then(() => cy.task(
      'queryDB',
      // ci-user moderates the category (role 1 = moderator).
      `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${user.id}, ${ids.category}, 1, UNIX_TIMESTAMP(), 0, '')`,
    ));
  });

  afterEach(() => {
    cy.kunena_setOption('topicShowModerators', optionBefore ?? null);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-topic-mods-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('shows the moderators of the category at the bottom of the topic when the option is on', () => {
    cy.kunena_setOption('topicShowModerators', '1').then((old) => {
      optionBefore = old;
    });
    cy.task('queryDB', `SELECT name, username FROM #__users WHERE id = ${user.id}`).then((rows) => {
      cy.visit(page());
      cy.contains('.kunena-topic-item .float-end > div', 'Moderators:').find('a').should('have.length', 1).invoke('text').then((text) => {
        expect([rows[0].name, rows[0].username], 'the link to the moderator').to.include(text.trim());
      });
    });
  });

  it('does not show the moderators when the option is off', () => {
    cy.kunena_setOption('topicShowModerators', '0').then((old) => {
      optionBefore = old;
    });
    cy.visit(page());
    cy.get('.kunena-topic-item > h1').should('be.visible');
    cy.get('.kunena-topic-item').should('not.contain.text', 'Moderators:');
  });
});
