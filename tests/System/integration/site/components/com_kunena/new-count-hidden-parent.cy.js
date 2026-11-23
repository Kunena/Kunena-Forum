// A category which the user may not read, with a subcategory he may read: the topics of the hidden category are never new for him.
// The number of new topics of the category above them counted them (the list of the subcategories keeps a hidden category which has
// a readable subcategory), so a member saw "1 New" for a topic in a category of the staff.
describe('Kunena index: the new topics of a hidden category', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const old = {};
  const ids = {};

  // A category below the given one, readable by the access level (1 public, 3 special: the staff).
  const makeCategory = (name, parent, access, order) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (${parent}, 'Cypress ${name} ${stamp}', 'cypress-${name}-${stamp}', '', '', '', '', '', 'joomla.level', ${access}, 1, 1, 8, 1, ${order}, 1, '{}', 'default')`,
  ).then((category) => cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-${name}-${stamp}', 'catid', ${category.insertId}, 0)`).then(() => category.insertId));

  // A topic of the administrator, posted now.
  const makeTopic = (category) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category}, 'Cypress topic ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${category}, 'ci-admin', ${admin}, '', 'Cypress topic ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`)));

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
    // Section 1 > top (public) > staff (special) > open (public). The topic is in the category of the staff.
    makeCategory('top', 1, 1, 90).then((top) => {
      ids.top = top;

      return makeCategory('staff', top, 3, 1);
    }).then((staff) => {
      ids.staff = staff;
      makeCategory('open', staff, 1, 1);
      makeTopic(staff);
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

  it('does not count the topic of the hidden category as new for a member', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=category&layout=list`).its('body').then((body) => {
      expect(heading(body, 'top'), 'the category above it').not.to.contain('knewchar');
    });
  });
});
