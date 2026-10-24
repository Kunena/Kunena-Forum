// The suggestions of similar topics while a new topic is written (task topic.similar, template option similarTopics): topics with a
// word of the subject are suggested, the topics of a category closed to the writer are not, short words and a request without
// the token give nothing.
describe('Kunena similar topics', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const word = `quokka${stamp}`;
  const ids = {};

  const addCategory = (name, access) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress similar ${name} ${stamp}', 'cypress-similar-${name}-${stamp}', '', '', '', '', '', 'joomla.level', ${access}, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
  ).then((category) => {
    ids[name] = category.insertId;
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-similar-${name}-${stamp}', 'catid', ${category.insertId}, 0)`);

    return cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'The ${name} ${word} garden', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    );
  });

  const similar = (subject, withToken = true) => cy.request(`${base}&view=home`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({
      url: `${base}&view=topic&task=similar&format=json&subject=${encodeURIComponent(subject)}${withToken ? `&${token}=1` : ''}`,
      failOnStatusCode: false,
    });
  });

  beforeEach(() => {
    addCategory('public', 1);
    addCategory('closed', 3);
  });

  afterEach(() => {
    ['public', 'closed'].forEach((name) => {
      cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-similar-${name}-${stamp}'`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids[name]}`);
    });
  });

  it('suggests the topic with the word, and not the one in a closed category', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    similar(`How do I keep a ${word}?`).then((response) => {
      const subjects = (response.body.data ?? []).map((topic) => topic.subject);

      expect(subjects, 'the suggestions').to.include(`The public ${word} garden`);
      expect(subjects, 'the suggestions').to.not.include(`The closed ${word} garden`);
    });
  });

  it('suggests nothing for short words, and refuses a request without the token', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    similar('a an of').its('body.data').should('have.length', 0);
    similar(`How do I keep a ${word}?`, false).its('status').should('eq', 403);
  });
});
