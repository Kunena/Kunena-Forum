// The page of a topic gave the social sites (og:title, twitter:title) its subject escaped twice: a share of "Q&A" read "Q&amp;A"
// (the subject was HTML, Joomla escapes the meta tags again).
describe('Kunena topic: the subject for the social sites', () => {
  const stamp = Date.now();
  const subject = `Q&A topic ${stamp}`;
  const ids = {};

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 798, 'A first text', '', 0, UNIX_TIMESTAMP(), 798, 'A first text', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-user', 798, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A first text')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('has the subject once escaped in og:title and twitter:title', () => {
    cy.then(() => cy.request(`/index.php?option=com_kunena&view=topic&catid=3&id=${ids.topic}`)).then((page) => {
      const body = String(page.body);
      const og = /<meta property="og:title" content="([^"]*)"/.exec(body);
      const twitter = /<meta name="twitter:title" content="([^"]*)"/.exec(body);

      expect(og && og[1], 'og:title').to.eq(`Q&amp;A topic ${stamp}`);
      expect(twitter && twitter[1], 'twitter:title').to.eq(`Q&amp;A topic ${stamp}`);
    });
  });
});
