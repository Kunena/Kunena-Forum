// A menu item of a topic list with "Categories: Hide" and a list of categories showed only the topics of those categories: the
// value of "Hide" (2) was taken as "Show" (any value but -1), the list is "in" when the value is true.
describe('Kunena menu item of a topic list: hide the chosen categories', () => {
  const stamp = Date.now();
  const item = 140;
  const old = {};
  const ids = {};

  const addTopic = (category, name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category}, 'Cypress ${name} ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
  ).then((topic) => {
    ids[name] = topic.insertId;
  });

  before(() => {
    cy.task('queryDB', `SELECT params FROM #__menu WHERE id = ${item}`).then((rows) => {
      old.params = rows[0].params;

      const params = JSON.parse(old.params || '{}');

      params.topics_catselection = '2';
      params.topics_categories = ['3'];

      return cy.task('queryDB', `UPDATE #__menu SET params = '${JSON.stringify(params)}' WHERE id = ${item}`);
    });
  });

  after(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__menu SET params = '${old.params}' WHERE id = ${item}`));
  });

  beforeEach(() => {
    addTopic(3, 'hidden');
    addTopic(4, 'shown');
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE subject LIKE 'Cypress % ${stamp}'`);
  });

  it('leaves out the topics of the hidden category', () => {
    cy.request(`/index.php?option=com_kunena&view=topics&mode=replies&Itemid=${item}&sel=720`).then((page) => {
      const body = String(page.body);

      expect(body, 'a topic of another category').to.include(`Cypress shown ${stamp}`);
      expect(body, 'a topic of the hidden category').not.to.include(`Cypress hidden ${stamp}`);
    });
  });
});
