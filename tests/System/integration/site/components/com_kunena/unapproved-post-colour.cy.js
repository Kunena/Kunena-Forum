// In Aurelia a post which waits for approval had a 5% grey tint, or none at all in the default profile position (left): a
// moderator hardly saw it. Its post box now has the soft warning colour of Bootstrap (--bs-warning-bg-subtle).
describe('Kunena moderation: the colour of a post awaiting approval', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  // The compiled style sheet of Aurelia, which the site makes again from the SCSS of the checkout when it is missing. Only this
  // one file is deleted, never its folder: the folders of Kunena on the site are links into a checkout.
  const stylesheet = 'media/kunena/cache/aurelia/css/kunena.css';
  const ids = {};

  const addMessage = (parent, hold, userid, name, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, ${ids.category}, '${name}', ${userid}, '', 'Cypress unapproved colour ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, ${hold}, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`).then(() => message.insertId));
  const background = ($box) => $box[0].ownerDocument.defaultView.getComputedStyle($box[0]).backgroundColor;

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, review, params, iconset) VALUES (1, 'Cypress unapproved colour ${stamp}', 'cypress-unapproved-colour-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-unapproved-colour-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Cypress unapproved colour ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return addMessage(0, 0, 797, 'ci-admin', 'The question.');
    }).then((first) => {
      ids.first = first;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${first}, last_post_id = ${first} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 1, numPosts = 1 WHERE id = ${ids.category}`);

      // A reply of ci-user which waits for approval.
      return addMessage(first, 1, 798, 'ci-user', `The waiting reply ${stamp}.`);
    }).then((reply) => {
      ids.reply = reply;
    });

    cy.task('deleteRelativePath', stylesheet);
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    // The test site may use another template (Agora): the cookie shows this visitor the forum in Aurelia.
    cy.setCookie('kunena_template', 'aurelia');
  });

  afterEach(() => {
    cy.clearCookie('kunena_template');
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid IN (${ids.first}, ${ids.reply})`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id IN (${ids.first}, ${ids.reply})`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-unapproved-colour-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.kunena_fixUserCounters();
  });

  // The site compiles the style sheet again from the checkout which it points at next.
  after(() => {
    cy.task('deleteRelativePath', stylesheet);
  });

  it('tints the post box of the waiting reply, not that of the published post', () => {
    cy.then(() => cy.visit(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`));
    cy.get('#kunena.layout', { log: false }).should('not.have.class', 'agora');

    // The default profile position (left): the state class is on the post box.
    cy.get('.col-md-10.message-published').should('have.length', 1).then(($published) => {
      cy.get('.col-md-10.message-unapproved').should('have.length', 1).and('contain', `The waiting reply ${stamp}.`).then(($unapproved) => {
        const tint = background($unapproved);

        expect(tint, 'the background of the waiting reply').to.not.be.oneOf(['transparent', 'rgba(0, 0, 0, 0)']);
        expect(tint, 'the background of the waiting reply').to.not.equal(background($published));
      });
    });
  });
});
