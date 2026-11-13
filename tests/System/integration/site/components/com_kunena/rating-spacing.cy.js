// On a phone the rating stars of a topic were right on top of its buttons (Reply, Subscribe...): a tap meant for a button saved
// a rating. Below 768px the stars are on their own line under the title, a button high away from the actions; on a wide screen
// they stay inline in the title. Both templates: Agora makes the title a flex row.
describe('Kunena ratings: the stars of a topic apart from its buttons', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const options = {};

  // The space between the bottom of the stars and the top of the actions after the title, and where the stars are in the title.
  const measure = () => cy.get('#krating .c-rating__item').should('exist').then(() => cy.document()).then((doc) => {
    const title = doc.querySelector('.kunena-topic-item > h1').getBoundingClientRect();
    const stars = doc.querySelector('#krating .c-rating').getBoundingClientRect();
    const actions = doc.querySelector('.kunena-topic-item > h1 + div').getBoundingClientRect();

    return { gap: actions.top - stars.bottom, starsTop: stars.top, titleBottom: title.bottom };
  });

  const openTopic = (template, width, height) => {
    cy.kunena_setOption('template', template).then((previous) => {
      if (!('template' in options)) {
        options.template = previous;
      }
    });
    cy.clearCookie('kunena_template');
    cy.viewport(width, height);
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.then(() => cy.visit(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`));
    cy.get('#kunena.layout').should(template === 'agora' ? 'have.class' : 'not.have.class', 'agora');
  };

  beforeEach(() => {
    cy.kunena_setOption('ratingEnabled', '1').then((previous) => {
      options.ratingEnabled = previous;
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, allowRatings, params, iconset) VALUES (1, 'Cypress rating spacing ${stamp}', 'cypress-rating-spacing-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-rating-spacing-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Cypress rating spacing ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, ${ids.category}, 'ci-admin', 797, '', 'Cypress rating spacing ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'The topic to rate.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 1, numPosts = 1 WHERE id = ${ids.category}`);
    });
  });

  afterEach(() => {
    ['ratingEnabled', 'template'].forEach((name) => {
      if (name in options) {
        cy.kunena_setOption(name, options[name] === undefined ? null : options[name]);
        delete options[name];
      }
    });
    cy.task('queryDB', `DELETE FROM #__kunena_rate WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-rating-spacing-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  ['aurelia', 'agora'].forEach((template) => {
    it(`${template}: on a phone the stars are on their own line, a button high above the actions`, () => {
      openTopic(template, 375, 812);
      measure().then(({ gap }) => {
        expect(gap, 'the space between the stars and the actions').to.be.at.least(32);
      });
    });

    it(`${template}: on a wide screen the stars stay in the title`, () => {
      openTopic(template, 1280, 900);
      measure().then(({ gap, starsTop, titleBottom }) => {
        expect(starsTop, 'the top of the stars, above the bottom of the title').to.be.below(titleBottom);
        expect(gap, 'the space between the stars and the actions').to.be.below(32);
      });
    });
  });
});
