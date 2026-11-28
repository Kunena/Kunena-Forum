// The feed of a category:
// - its channel has the title and the description of the category and links to the page of the category (it had the title of the
//   menu item, "Home", no description, and the host alone as its link: the code which sets them did not run for a feed).
describe('Kunena feeds: the feed of a category', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const feed = (catid, extra = '') => cy.request(`${base}&view=category&catid=${catid}&format=feed&layout=feed&type=rss${extra}`).its('body');
  const old = {};
  const ids = [];

  const addTopic = (subject, minutesAgo) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - ${minutesAgo} * 60, 798, 'x', '', 0, UNIX_TIMESTAMP() - ${minutesAgo} * 60, 798, 'x', '', 0, '')`,
  ).then((topic) => {
    ids.push(topic.insertId);

    return cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-user', 798, '', '${subject}', UNIX_TIMESTAMP() - ${minutesAgo} * 60, '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`).then(() => cy.task(
      'queryDB',
      `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
    )));
  });

  before(() => {
    cy.task('queryDB', 'SELECT topicOrdering FROM #__kunena_categories WHERE id = 3').then((rows) => {
      old.ordering = rows[0].topicOrdering;
    });
  });

  after(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET topicOrdering = '${old.ordering}' WHERE id = 3`));
  });

  afterEach(() => {
    ids.forEach((id) => {
      cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
      cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
      cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
    });
    ids.length = 0;
  });

  it('has the title and the link of the category (and the feed of the topics links to its list)', () => {
    feed(1).then((body) => {
      const channel = body.slice(0, body.indexOf('<item>'));

      expect(channel, 'the title').to.match(/<title>[^<]*Community[^<]*<\/title>/);
      expect(channel, 'the title of the menu item').to.not.include('<title>Home</title>');
      expect(channel, 'the link of the category').to.match(/<link>https?:\/\/[^<\/]+\/[^<]+<\/link>/);
    });
    cy.request(`${base}&view=topics&format=feed&layout=feed&type=rss&mode=topics`).its('body').then((body) => {
      const channel = body.slice(0, body.indexOf('<item>'));

      expect(channel, 'the link of the list').to.match(/<link>https?:\/\/[^<\/]+\/[^<]+<\/link>/);
    });
  });
});
