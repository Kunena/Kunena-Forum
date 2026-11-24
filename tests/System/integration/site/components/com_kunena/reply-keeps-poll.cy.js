// A new topic may have a poll (the right poll.create: the author of the topic). A reply which sent the fields of a poll took the poll of
// the topic (getPoll() gives the existing one) and wrote the new title and options over it, with no check of the right poll.edit,
// which refuses a poll with votes when the users may not edit their polls: the author of a topic could rewrite his poll and its
// votes with a reply. The edit of a post checks poll.edit.
describe('Kunena polls: a reply of the author of the topic', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const subject = `Cypress reply poll ${stamp}`;
  const ids = {};
  const old = {};

  beforeEach(() => {
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.floodProtection = value ?? null;
    });
    cy.kunena_setOption('allowUserEditPoll', '0').then((value) => {
      old.allowUserEditPoll = value ?? null;
    });
    // A topic of the member with a poll which has votes.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - 3600, ${user.id}, 'x', '', 0, UNIX_TIMESTAMP() - 3600, ${user.id}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-user', ${user.id}, '', '${subject}', UNIX_TIMESTAMP() - 3600, '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.first = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A topic with a poll ${stamp}.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      // The member owns the topic (as the post of a topic records it).
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${user.id}, ${ids.topic}, 3, 1, ${message.insertId}, 1, 0, 0, '')`);

      return cy.task('queryDB', `INSERT INTO #__kunena_polls (title, threadid, polltimetolive) VALUES ('The poll ${stamp}', ${ids.topic}, '1000-01-01 00:00:00')`);
    }).then((poll) => {
      ids.poll = poll.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_polls_options (pollid, text, votes) VALUES (${poll.insertId}, 'One', 3), (${poll.insertId}, 'Two', 1)`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET poll_id = ${poll.insertId} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.kunena_setOption('floodProtection', old.floodProtection);
    cy.then(() => cy.kunena_setOption('allowUserEditPoll', old.allowUserEditPoll));
    cy.task('queryDB', `DELETE FROM #__kunena_polls_options WHERE pollid IN (SELECT id FROM #__kunena_polls WHERE threadid = ${ids.topic})`);
    cy.task('queryDB', `DELETE FROM #__kunena_polls_users WHERE pollid IN (SELECT id FROM #__kunena_polls WHERE threadid = ${ids.topic})`);
    cy.task('queryDB', `DELETE FROM #__kunena_polls WHERE threadid = ${ids.topic}`);
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.kunena_fixUserCounters();
  });

  it('keeps the poll of the topic and its votes', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=topic&layout=reply&catid=3&id=${ids.topic}`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: {
          catid: 3,
          id: ids.topic,
          parentid: ids.first,
          subject: `Re: ${subject}`,
          message: `A reply ${stamp}.`,
          poll_title: `Rewritten ${stamp}`,
          'polloptionsID[0]': 'Yes',
          'polloptionsID[1]': 'No',
          [token]: '1',
        },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_messages WHERE thread = ${ids.topic}`).then((rows) => {
      expect(Number(rows[0].n), 'the reply is posted').to.eq(2);
    });
    cy.task('queryDB', `SELECT p.title, GROUP_CONCAT(o.text ORDER BY o.id) AS options, SUM(o.votes) AS votes FROM #__kunena_polls AS p LEFT JOIN #__kunena_polls_options AS o ON o.pollid = p.id WHERE p.id = ${ids.poll} GROUP BY p.id`).then((rows) => {
      expect(rows[0].title, 'the title of the poll').to.eq(`The poll ${stamp}`);
      expect(rows[0].options, 'the options').to.eq('One,Two');
      expect(Number(rows[0].votes), 'the votes').to.eq(4);
    });
  });
});
