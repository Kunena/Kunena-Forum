// The shadow which a moved topic leaves in its old category is no new topic: opening it opens the moved topic, so the shadow was
// never read, and the index showed "1 New" in the old category until the member marked it read.
describe('Kunena index: the shadow of a moved topic', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const old = {};

  const makeCategory = (name, params, order) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress ${name} ${stamp}', 'cypress-${name}-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, ${order}, 1, '${params}', 'default')`,
  ).then((category) => cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-${name}-${stamp}', 'catid', ${category.insertId}, 0)`).then(() => category.insertId));

  // A topic of the administrator, posted now; with a moved id, the shadow of a topic moved to another category.
  const makeTopic = (category, movedId = 0) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category}, 'Cypress topic ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, ${movedId}, 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, '')`,
  ).then((topic) => movedId ? topic : cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${category}, 'ci-admin', ${admin}, '', 'Cypress topic ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`)));

  // The text after the name of the category on the index, up to the end of its heading (the count of new topics is there).
  const heading = (body, name) => {
    const start = body.indexOf(`Cypress ${name} ${stamp}</a>`);

    expect(start, `the category ${name} on the index`).to.be.greaterThan(-1);

    return body.slice(start, body.indexOf('</h3>', start));
  };

  before(() => {
    cy.kunena_setOption('showNew', '1').then((value) => {
      old.showNew = value ?? null;
    });
  });

  after(() => {
    cy.kunena_setOption('showNew', old.showNew);
  });

  beforeEach(() => {
    // The topic is in the category "new", its shadow in the category "old".
    makeCategory('new', '{}', 90).then((id) => makeTopic(id)).then(() => cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = 'Cypress topic ${stamp}'`)).then((rows) => {
      makeCategory('old', '{}', 91).then((id) => makeTopic(id, rows[0].id));
    });
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = 'Cypress topic ${stamp}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias LIKE 'cypress-%-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id IN (SELECT id FROM #__kunena_categories WHERE name LIKE 'Cypress % ${stamp}')`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE name LIKE 'Cypress % ${stamp}'`);
  });

  it('counts the moved topic as new in its new category only', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=category&layout=list`).its('body').then((body) => {
      expect(heading(body, 'new'), 'the category of the topic').to.contain('knewchar');
      expect(heading(body, 'old'), 'the category of the shadow').not.to.contain('knewchar');
    });
  });
});
