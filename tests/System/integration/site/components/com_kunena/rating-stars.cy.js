// The stars of the rating of a topic, Kunena's own code (krating.js) in place of rating.js: the stars show the rating which the
// server gives (rating.js took the whole answer {success, data} for the number and showed no star), a click or the keyboard
// (arrows, Enter) gives a rating, the answer is a translated text (it was the language key), and a refused rating (the own topic)
// puts the stars back. The rows are inserted by the spec and removed after each test.
describe('Kunena ratings: the stars of a topic', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const rater = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};
  const options = {};

  const stars = () => cy.get('#krating .c-rating__item');
  const lit = () => cy.get('#krating .c-rating__item.is-active');
  const message = () => cy.get('#system-message-container .alert').last();
  const stored = () => cy.task('queryDB', `SELECT userid, rate FROM #__kunena_rate WHERE topic_id = ${ids.topic} ORDER BY userid`);
  const openTopic = () => {
    cy.visit(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`);
    stars().should('have.length', 5);
  };

  beforeEach(() => {
    cy.kunena_setOption('ratingEnabled', '1').then((previous) => {
      options.ratingEnabled = previous;
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, allowRatings, params, iconset) VALUES (1, 'Cypress rating stars ${stamp}', 'cypress-rating-stars-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-rating-stars-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Cypress rating stars ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, ${ids.category}, 'ci-admin', 797, '', 'Cypress rating stars ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((row) => {
      ids.message = row.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${row.insertId}, 'The topic to rate.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${row.insertId}, last_post_id = ${row.insertId} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 1, numPosts = 1 WHERE id = ${ids.category}`);
    });
  });

  afterEach(() => {
    if ('ratingEnabled' in options) {
      cy.kunena_setOption('ratingEnabled', options.ratingEnabled === undefined ? null : options.ratingEnabled);
      delete options.ratingEnabled;
    }
    cy.task('queryDB', `DELETE FROM #__kunena_rate WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-rating-stars-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('saves a rating with a click and shows it again after a reload', () => {
    cy.doFrontendLogin(rater.name, rater.password, false);
    openTopic();
    lit().should('have.length', 0);
    cy.get('#krating .c-rating').should('have.attr', 'role', 'radiogroup');

    stars().eq(3).click();
    message().should('contain', 'Your rating has been successfully saved').and('not.contain', 'COM_KUNENA_');
    stored().should('deep.eq', [{ userid: rater.id, rate: 4 }]);

    // The rating of the user is back in the stars (rating.js showed none).
    openTopic();
    lit().should('have.length', 4);
    stars().eq(3).should('have.attr', 'aria-checked', 'true').and('have.attr', 'tabindex', '0');
  });

  it('gives a rating with the keyboard', () => {
    cy.doFrontendLogin(rater.name, rater.password, false);
    openTopic();
    stars().first().focus().trigger('keydown', { key: 'ArrowRight' });
    cy.focused().should('have.attr', 'data-index', '1').trigger('keydown', { key: 'ArrowRight' });
    cy.focused().should('have.attr', 'data-index', '2');
    lit().should('have.length', 3);
    cy.focused().trigger('keydown', { key: 'Enter' });
    message().should('contain', 'Your rating has been successfully saved');
    stored().should('deep.eq', [{ userid: rater.id, rate: 3 }]);
  });

  it('puts the stars back when the rating is refused (the own topic)', () => {
    // A rating of 5 by another user: the author sees the average of the topic.
    cy.task('queryDB', `INSERT INTO #__kunena_rate (topic_id, userid, rate, time) VALUES (${ids.topic}, 796, 5, NOW())`);
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
    openTopic();
    lit().should('have.length', 5);

    stars().eq(1).click();
    message().should('contain', 'You cannot rate your own topic').and('not.contain', 'COM_KUNENA_');
    lit().should('have.length', 5);
    stored().should('deep.eq', [{ userid: 796, rate: 5 }]);
  });
});
