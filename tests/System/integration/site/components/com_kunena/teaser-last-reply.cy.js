// A category can hide the replies from the guests (the teaser option): a guest reads the first message of a topic only. The
// index of the forum gave the start of the last message of each category as the title of the link to it, and the feed of a
// category (with the messages, option rssType post) gave the last message: the text of a reply went to every guest there.
describe('Kunena teaser: the last reply for the guests', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const words = { first: `FIRSTTEXT${stamp}`, reply: `REPLYTEXT${stamp}` };
  const subject = `Cypress teaser ${stamp}`;
  const feed = `${base}&view=category&format=feed&layout=feed&catid=3&type=rss`;
  const ids = {};
  const old = {};

  const option = (name, value) => cy.kunena_setOption(name, value).then((previous) => {
    old[name] = previous ?? null;
  });

  before(() => {
    option('teaser', '1');
    option('enableRss', '1');
    option('rssType', 'post');
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'The first ${words.first}', '', 0, UNIX_TIMESTAMP() + 1, 797, 'The reply ${words.reply}', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((first) => {
      ids.first = first.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${first.insertId}, 'The first ${words.first}')`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${first.insertId}, ${ids.topic}, 3, 'ci-admin', 797, '', 'Re: ${subject}', UNIX_TIMESTAMP() + 1, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((reply) => {
      ids.reply = reply.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${reply.insertId}, 'The reply ${words.reply}')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.first}, last_post_id = ${reply.insertId} WHERE id = ${ids.topic}`);
      // The topic is the last one of the category (the index shows the last post of each category).
      cy.task('queryDB', `UPDATE #__kunena_categories SET last_topic_id = ${ids.topic}, last_post_id = ${reply.insertId}, last_post_time = UNIX_TIMESTAMP() + 1 WHERE id = 3`);
    });
  });

  after(() => {
    ['teaser', 'enableRss', 'rssType'].forEach((name) => cy.kunena_setOption(name, old[name]));
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.kunena_fixCategoryCounters();
  });

  it('gives a guest the last topic without the text of its reply', () => {
    cy.request(`${base}&view=category&layout=list`).its('body').then((body) => {
      expect(body, 'the last topic on the index').to.contain(subject);
      expect(body, 'the reply on the index').not.to.contain(words.reply);
    });
    cy.request(feed).its('body').then((body) => {
      expect(body, 'the topic in the feed').to.contain(subject);
      expect(body, 'the reply in the feed').not.to.contain(words.reply);
    });
  });
});
