// A voter sees the results of the poll. When a vote may be changed ("Single Vote Only" = No), the footer of the results has a
// "Change Vote" link once the time between votes has passed, and a notice before that. Nothing is shown in a topic which is
// locked, in a poll which has ended, or when only one vote is allowed.
describe('Kunena polls: change a vote', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};
  const later = 'You can change your vote when the time between votes has passed.';

  const addTopic = (subject, locked, timeToLive) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, ${locked}, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
  ))).then(() => cy.task('queryDB', `INSERT INTO #__kunena_polls (title, threadid, polltimetolive) VALUES ('A poll', ${topic.insertId}, '${timeToLive}')`))
    .then((poll) => cy.task('queryDB', `INSERT INTO #__kunena_polls_options (pollid, text, votes) VALUES (${poll.insertId}, 'One', 1), (${poll.insertId}, 'Two', 0)`)
      .then((options) => cy.task('queryDB', `UPDATE #__kunena_topics SET poll_id = ${poll.insertId} WHERE id = ${topic.insertId}`).then(() => ({
        topic: topic.insertId,
        poll: poll.insertId,
        first: options.insertId,
        second: options.insertId + 1,
      })))));

  // The user voted for the option "One" of the poll some time ago.
  const votedAgo = (set, interval) => cy.task('queryDB', `INSERT INTO #__kunena_polls_users (pollid, userid, votes, lasttime, lastvote) VALUES (${set.poll}, ${user.id}, 1, UTC_TIMESTAMP() - INTERVAL ${interval}, ${set.first})`);
  const visitTopic = (set) => cy.visit(`${base}&view=topic&catid=3&id=${set.topic}`);
  const noChange = () => {
    cy.get('#poll-results').should('exist');
    cy.get('a.kpoll-vote-link').should('not.exist');
    cy.get('.kpoll-change-vote-later').should('not.exist');
  };

  const old = {};

  before(() => {
    cy.kunena_setOption('pollEnabled', '1').then((value) => {
      old.pollEnabled = value;
    });
    cy.kunena_setOption('pollTimeBtVotes', '00:15:00').then((value) => {
      old.pollTimeBtVotes = value;
    });
    cy.task('queryDB', 'SELECT allowPolls FROM #__kunena_categories WHERE id = 3').then((rows) => {
      old.allowPolls = rows[0].allowPolls;
    });
    cy.task('queryDB', 'UPDATE #__kunena_categories SET allowPolls = 1 WHERE id = 3');
  });

  after(() => {
    cy.kunena_setOption('pollEnabled', old.pollEnabled ?? '1');
    cy.then(() => cy.kunena_setOption('pollTimeBtVotes', old.pollTimeBtVotes ?? '00:15:00'));
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET allowPolls = ${Number(old.allowPolls)} WHERE id = 3`));
  });

  beforeEach(() => {
    cy.kunena_setOption('pollAllowVoteOne', '0').then((value) => {
      old.pollAllowVoteOne = value;
    });
    addTopic(`Cypress poll change open ${stamp}`, 0, '1000-01-01 00:00:00').then((set) => {
      ids.open = set;

      return addTopic(`Cypress poll change locked ${stamp}`, 1, '1000-01-01 00:00:00');
    }).then((set) => {
      ids.locked = set;

      return addTopic(`Cypress poll change ended ${stamp}`, 0, '2001-01-01 00:00:00');
    }).then((set) => {
      ids.ended = set;
    });
  });

  afterEach(() => {
    cy.kunena_setOption('pollAllowVoteOne', old.pollAllowVoteOne === undefined ? null : old.pollAllowVoteOne);
    cy.task('queryDB', `SELECT id, poll_id FROM #__kunena_topics WHERE subject LIKE 'Cypress poll change % ${stamp}'`).then((rows) => {
      rows.forEach(({ id, poll_id: poll }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_polls_users WHERE pollid = ${poll}`);
        cy.task('queryDB', `DELETE FROM #__kunena_polls_options WHERE pollid = ${poll}`);
        cy.task('queryDB', `DELETE FROM #__kunena_polls WHERE id = ${poll}`);
      });
    });
  });

  it('shows the results with a "Change Vote" link after the time between votes, which opens the vote form', () => {
    cy.then(() => votedAgo(ids.open, '20 MINUTE'));
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => visitTopic(ids.open));

    cy.get('#poll-results').should('exist');
    cy.get('#kpoll-button-vote').should('not.exist');
    cy.get('.kpoll-change-vote-later').should('not.exist');
    cy.get('#poll-results tfoot td a.kpoll-vote-link')
      .should('have.length', 1)
      .and('contain.text', 'Change Vote')
      .and('have.attr', 'href')
      .and('not.match', />$/);

    cy.get('a.kpoll-vote-link').click();
    cy.get('#kpoll-button-vote').should('have.value', 'Change Vote');
    cy.then(() => cy.get(`input.kpoll-boxvote[value="${ids.open.first}"]`).should('be.checked'));
    cy.then(() => cy.get(`input.kpoll-boxvote[value="${ids.open.second}"]`).should('not.be.checked'));
  });

  it('shows a notice and no link before the time between votes has passed', () => {
    cy.then(() => votedAgo(ids.open, '5 MINUTE'));
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => visitTopic(ids.open));

    cy.get('#poll-results tfoot td .kpoll-change-vote-later').should('contain.text', later);
    cy.get('a.kpoll-vote-link').should('not.exist');
  });

  it('shows neither the link nor the notice in a locked topic or a poll which has ended', () => {
    cy.then(() => votedAgo(ids.locked, '5 MINUTE'));
    cy.then(() => votedAgo(ids.ended, '5 MINUTE'));
    cy.doFrontendLogin(user.name, user.password, false);

    cy.then(() => visitTopic(ids.locked));
    noChange();
    cy.then(() => visitTopic(ids.ended));
    noChange();
  });

  it('shows neither the link nor the notice when only one vote is allowed', () => {
    cy.kunena_setOption('pollAllowVoteOne', '1');
    cy.then(() => votedAgo(ids.open, '20 MINUTE'));
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => visitTopic(ids.open));

    noChange();
  });
});
