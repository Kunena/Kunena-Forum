// The page of a poll (view=topic&layout=poll) had the question escaped twice in its title and its description: "Q&A" showed as
// "Q&amp;A" in the tab of the browser (the question was made HTML by the parser, then Joomla escaped the title again).
describe('Kunena poll page: the question in the title', () => {
  const stamp = Date.now();
  const question = `Q&A poll ${stamp}`;
  const ids = {};

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress poll topic ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 798, 'x', '', 0, UNIX_TIMESTAMP(), 798, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_polls (title, threadid) VALUES ('${question}', ${topic.insertId})`).then((poll) => {
        ids.poll = poll.insertId;
        cy.task('queryDB', `UPDATE #__kunena_topics SET poll_id = ${poll.insertId} WHERE id = ${topic.insertId}`);
      });
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_polls WHERE title = '${question}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE subject = 'Cypress poll topic ${stamp}'`);
  });

  it('has the question once escaped in the title and the description', () => {
    cy.then(() => cy.request(`/index.php?option=com_kunena&view=topic&layout=poll&catid=3&id=${ids.topic}`)).then((page) => {
      const body = String(page.body);
      const title = /<title>([^<]*)<\/title>/.exec(body);
      const description = /<meta name="description" content="([^"]*)"/.exec(body);

      expect(title && title[1], 'the title').to.include(`Q&amp;A poll ${stamp}`);
      expect(description && description[1], 'the description').to.include(`Q&amp;A poll ${stamp}`);
      expect(body, 'a question escaped twice').not.to.include('Q&amp;amp;A');
    });
  });
});
