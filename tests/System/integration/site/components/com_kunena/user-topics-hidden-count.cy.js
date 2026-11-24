// The topics of a member (view=topics&layout=user) counted his topics in the categories which the visitor may not read: the list
// left them out, but the number of the topics and the pages showed them, so a guest learned how much a member wrote in the staff
// categories (and the pages came out short or empty).
describe('Kunena topics of a member: the topics of a category which the visitor may not read', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { id: 798 };
  const ids = {};

  beforeEach(() => {
    // A category for the access level Special (not for guests nor for registered users), with a recent topic of ci-user.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress staff ${stamp}', 'cypress-staff-${stamp}', '', '', '', '', '', 'joomla.level', 3, 1, 1, 8, 1, 90, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Cypress staff topic ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, '')`,
      ).then((topic) => {
        ids.topic = topic.insertId;
        cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner) VALUES (${user.id}, ${topic.insertId}, ${category.insertId}, 1, 0, 1)`);
      });
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE subject = 'Cypress staff topic ${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE alias = 'cypress-staff-${stamp}'`);
  });

  it('does not count that topic for a guest', () => {
    ['started', 'posted', 'default'].forEach((mode) => {
      cy.request(`${base}&view=topics&layout=user&mode=${mode}&userid=${user.id}`).then((response) => {
        const body = String(response.body);
        const count = body.match(/data-ktopics-count[^>]*>([\s\S]*?)<\/small>/);

        expect(body, `mode ${mode}: the topic`).not.to.include(`Cypress staff topic ${stamp}`);
        expect(count, `mode ${mode}: the count of the topics`).not.to.equal(null);
        expect(count[1].trim(), `mode ${mode}: the count of the topics`).to.equal('No topics');
      });
    });
  });
});
