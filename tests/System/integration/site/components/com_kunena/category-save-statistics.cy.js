// The save of a category (the frontend form of a category administrator) does not take the statistics of the category from the
// request: the number of topics and posts and the last topic stay as they are. A forged last topic would show the subject of a
// topic of another category - one closed to the visitors - on the forum index.
describe('Kunena categories: the save keeps the statistics', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};

  const addCategory = (name, access) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress stats ${name} ${stamp}', 'cypress-stats-${name}-${stamp}', '', '', '', '', '', 'joomla.level', ${access}, 1, 1, 8, 1, 96, 1, '{}', 'default')`,
  ).then((category) => {
    ids[name] = category.insertId;
    cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-stats-${name}-${stamp}', 'catid', ${category.insertId}, 0)`);
  });

  beforeEach(() => {
    addCategory('own', 1);
    // A category for the access level Special, with a topic of which the subject is a secret for the visitors.
    addCategory('closed', 3);
    cy.then(() => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.closed}, 'Secret ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    )).then((topic) => {
      ids.secret = topic.insertId;
    });
    // ci-user is an administrator of his own category only.
    cy.then(() => cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${user.id}, ${ids.own}, 2, 0, 0, '')`));
  });

  afterEach(() => {
    ['own', 'closed'].forEach((name) => {
      cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-stats-${name}-${stamp}'`);
      cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids[name]}`);
    });
  });

  it('keeps the numbers and the last topic of the category when a category administrator saves it', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => cy.request(`${base}&view=home`)).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

      return cy.request({
        method: 'POST',
        url: `${base}&view=category&catid=${ids.own}`,
        form: true,
        body: {
          task: 'save',
          catid: ids.own,
          [token]: '1',
          parentid: 1,
          name: `Cypress stats own ${stamp}`,
          published: 1,
          numTopics: 999,
          numPosts: 999,
          last_topic_id: ids.secret,
          last_post_id: 1,
          last_post_time: 2000000000,
        },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.then(() => cy.task('queryDB', `SELECT name, numTopics, numPosts, last_topic_id, last_post_id, last_post_time FROM #__kunena_categories WHERE id = ${ids.own}`)).then((rows) => {
      expect(rows[0].name, 'the category was saved').to.eq(`Cypress stats own ${stamp}`);
      expect(rows[0], 'the statistics').to.deep.include({ numTopics: 0, numPosts: 0, last_topic_id: 0, last_post_id: 0, last_post_time: 0 });
    });
    cy.clearAllCookies();
    cy.request(`${base}&view=category&layout=list`).its('body').should('not.include', `Secret ${stamp}`);
  });

  it('shows a last topic on the index only to those who may read it', () => {
    // A last topic which is not the category's own (statistics which are out of date, or were changed by hand).
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 1, numPosts = 1, last_topic_id = ${ids.secret}, last_post_time = UNIX_TIMESTAMP() WHERE id = ${ids.own}`));
    ['category&layout=list', 'home'].forEach((view) => cy.request(`${base}&view=${view}`).its('body').should('not.include', `Secret ${stamp}`));

    // A topic which the visitor may read is shown.
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET access = 1 WHERE id = ${ids.closed}`));
    // The categories which a visitor may read are kept in his session.
    cy.clearAllCookies();
    cy.request(`${base}&view=category&layout=list`).its('body').should('include', `Secret ${stamp}`);
  });
});
