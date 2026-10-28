// The tag [article]id[/article] shows a Joomla article in a message. It checked the access level of the article and that its
// category was published, not the article itself: an article which is not published (a draft, one in the trash, one which is
// published later) and the articles of a category of another access level were shown to every reader of the message.
describe('Kunena BBCode: the article tag shows only what the reader may read', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const words = {
    open: `ARTOPEN${stamp}`, draft: `ARTDRAFT${stamp}`, later: `ARTLATER${stamp}`, trash: `ARTTRASH${stamp}`, special: `ARTSPECIAL${stamp}`,
  };
  const ids = { category: 2 };

  // An article (of the category Uncategorised, 2, by default), with the state, the start of the publication and the text given.
  const addArticle = (name, state, publishUp) => cy.task(
    'queryDB',
    `INSERT INTO #__content (title, alias, introtext, \`fulltext\`, state, catid, created, created_by, modified, publish_up, images, urls, attribs, metadesc, metadata, access, language) VALUES ('Cypress article ${name} ${stamp}', 'cypress-article-${name}-${stamp}', '<p>${words[name]}</p>', '', ${state}, ${ids.category}, NOW(), 797, NOW(), ${publishUp}, '{}', '{}', '{}', '', '{}', 1, '*')`,
  ).then((article) => {
    ids[name] = article.insertId;
  });

  before(() => {
    addArticle('open', 1, 'NOW() - INTERVAL 1 DAY');
    addArticle('draft', 0, 'NOW() - INTERVAL 1 DAY');
    addArticle('later', 1, 'NOW() + INTERVAL 1 DAY');
    addArticle('trash', -2, 'NOW() - INTERVAL 1 DAY');
    // A published article of level Public in a category for the managers only (Special): the last category of the tree.
    cy.task('queryDB', "SELECT rgt FROM #__categories WHERE id = 1").then((rows) => {
      ids.rootRgt = Number(rows[0].rgt);

      return cy.task(
        'queryDB',
        `INSERT INTO #__categories (parent_id, lft, rgt, level, path, extension, title, alias, note, description, published, access, params, metadesc, metakey, metadata, created_user_id, created_time, modified_user_id, modified_time, language) VALUES (1, ${ids.rootRgt}, ${ids.rootRgt + 1}, 1, 'cypress-special-${stamp}', 'com_content', 'Cypress special ${stamp}', 'cypress-special-${stamp}', '', '', 1, 3, '{}', '', '', '{}', 797, NOW(), 797, NOW(), '*')`,
      );
    }).then((category) => {
      cy.task('queryDB', `UPDATE #__categories SET rgt = ${ids.rootRgt + 2} WHERE id = 1`);
      ids.category = category.insertId;
      addArticle('special', 1, 'NOW() - INTERVAL 1 DAY');
    });
    cy.then(() => {
      const text = ['open', 'draft', 'later', 'trash', 'special'].map((name) => `[article]${ids[name]}[/article]`).join(' ');

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress articles ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, '${text}', '', 0, UNIX_TIMESTAMP(), 797, '${text}', '', 0, '')`,
      ).then((topic) => {
        ids.topic = topic.insertId;

        return cy.task(
          'queryDB',
          `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', 'Cypress articles ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
        );
      }).then((message) => {
        ids.message = message.insertId;
        cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`);
        cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      });
    });
  });

  after(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__content WHERE alias LIKE 'cypress-article-%-${stamp}'`);
    cy.then(() => {
      if (ids.category !== 2) {
        cy.task('queryDB', `DELETE FROM #__categories WHERE id = ${ids.category}`);
        cy.task('queryDB', `UPDATE #__categories SET rgt = ${ids.rootRgt} WHERE id = 1`);
      }
    });
  });

  it('shows a published article, not one which is not published or is in a category of another access level', () => {
    cy.request(`${base}&view=topic&catid=3&id=${ids.topic}`).its('body').then((body) => {
      expect(body, 'the published article').to.contain(words.open);
      expect(body, 'the draft').not.to.contain(words.draft);
      expect(body, 'the article published later').not.to.contain(words.later);
      expect(body, 'the article in the trash').not.to.contain(words.trash);
      expect(body, 'the article of the category Special').not.to.contain(words.special);
    });
  });
});
