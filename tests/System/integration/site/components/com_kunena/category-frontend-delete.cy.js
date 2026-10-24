// The deletion of a category on the site (task category.delete, for the administrators of the category): a user may not delete
// it, a category with subcategories is kept (they are deleted one by one), and an administrator deletes an empty category.
describe('Kunena categories: deleting a category on the site', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};

  const addCategory = (parent, name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (${parent}, '${name} ${stamp}', '${name}-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
  ).then((category) => {
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('${name}-${stamp}', 'catid', ${category.insertId}, 0)`);

    return cy.wrap(category.insertId);
  });
  const remove = (catid) => cy.request(`${base}&view=home`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({ method: 'POST', url: `${base}&view=category&task=delete`, form: true, body: { catid, [token]: '1' }, failOnStatusCode: false, followRedirect: false });
  });
  const exists = (catid) => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_categories WHERE id = ${catid}`).then((rows) => Number(rows[0].n) === 1);

  beforeEach(() => {
    addCategory(1, 'cypress-delete-empty').then((id) => {
      ids.empty = id;
      // A translation of the category, and a topic with a rating and a Joomla tag.
      cy.task('queryDB', `INSERT INTO #__kunena_categories_translations (category_id, language, name, description, headerdesc) VALUES (${id}, 'nl-NL', 'Vertaald ${stamp}', '', '')`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${id}, 'Cypress delete ${stamp}', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_rate (topic_id, userid, rate, time) VALUES (${topic.insertId}, 798, 4, NOW())`);
      cy.task('queryDB', `INSERT INTO #__contentitem_tag_map (type_alias, core_content_id, content_item_id, tag_id, tag_date, type_id) VALUES ('com_kunena.topic', 0, ${topic.insertId}, 999999, NOW(), 0)`);
    });
    addCategory(1, 'cypress-delete-parent').then((id) => {
      ids.parent = id;

      return addCategory(id, 'cypress-delete-child');
    }).then((id) => {
      ids.child = id;
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_categories_translations WHERE category_id = ${ids.empty}`);
    cy.task('queryDB', `DELETE FROM #__kunena_rate WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__contentitem_tag_map WHERE type_alias = 'com_kunena.topic' AND content_item_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    ['empty', 'parent', 'child'].forEach((name) => {
      cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-delete-${name}-${stamp}'`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids[name]}`);
    });
  });

  it('does not let a user delete a category', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.then(() => remove(ids.empty));
    cy.then(() => exists(ids.empty)).should('eq', true);
  });

  it('lets an administrator delete an empty category, and keeps a category with a subcategory', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.then(() => remove(ids.parent));
    cy.then(() => exists(ids.parent)).should('eq', true);
    cy.then(() => remove(ids.empty));
    cy.then(() => exists(ids.empty)).should('eq', false);

    // What belonged to it is gone too.
    cy.then(() => cy.task('queryDB', `SELECT (SELECT COUNT(*) FROM #__kunena_categories_translations WHERE category_id = ${ids.empty}) AS translations, (SELECT COUNT(*) FROM #__kunena_topics WHERE id = ${ids.topic}) AS topics, (SELECT COUNT(*) FROM #__kunena_rate WHERE topic_id = ${ids.topic}) AS ratings, (SELECT COUNT(*) FROM #__contentitem_tag_map WHERE type_alias = 'com_kunena.topic' AND content_item_id = ${ids.topic}) AS tags`)).then((rows) => {
      const left = Object.fromEntries(Object.entries(rows[0]).map(([key, value]) => [key, Number(value)]));

      expect(left, 'the rows of the deleted category').to.deep.eq({ translations: 0, topics: 0, ratings: 0, tags: 0 });
    });
  });
});
