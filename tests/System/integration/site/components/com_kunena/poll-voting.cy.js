// The rules of a vote: a guest does not vote, a vote counts for an option of the poll of the topic only, a poll which has ended does
// not get votes, and a topic which is locked does not get votes. The votes are counted in the options and in the table of the voters.
describe('Kunena polls: the votes', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};

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
    .then((poll) => cy.task('queryDB', `INSERT INTO #__kunena_polls_options (pollid, text, votes) VALUES (${poll.insertId}, 'One', 0), (${poll.insertId}, 'Two', 0)`)
      .then((options) => cy.task('queryDB', `UPDATE #__kunena_topics SET poll_id = ${poll.insertId} WHERE id = ${topic.insertId}`).then(() => ({
        topic: topic.insertId,
        poll: poll.insertId,
        first: options.insertId,
        second: options.insertId + 1,
      })))));

  const totalVotes = (poll) => cy.task('queryDB', `SELECT COALESCE(SUM(votes), 0) AS total FROM #__kunena_polls_options WHERE pollid = ${poll}`).then((rows) => Number(rows[0].total));
  const vote = (set, option, withToken = true) => cy.request(`${base}&view=topic&catid=3&id=${set.topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=vote`,
      form: true,
      body: { id: set.topic, catid: 3, kpollradio: option, ...(withToken ? { [token]: '1' } : {}) },
      failOnStatusCode: false,
      followRedirect: false,
    });
  });

  const old = {};

  before(() => {
    cy.kunena_setOption('pollEnabled', '1').then((value) => {
      old.pollEnabled = value;
    });
    cy.task('queryDB', 'SELECT allowPolls FROM #__kunena_categories WHERE id = 3').then((rows) => {
      old.allowPolls = rows[0].allowPolls;
    });
    cy.task('queryDB', 'UPDATE #__kunena_categories SET allowPolls = 1 WHERE id = 3');
  });

  after(() => {
    cy.kunena_setOption('pollEnabled', old.pollEnabled ?? '1');
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET allowPolls = ${Number(old.allowPolls)} WHERE id = 3`));
  });

  beforeEach(() => {
    addTopic(`Cypress poll open ${stamp}`, 0, '1000-01-01 00:00:00').then((set) => {
      ids.open = set;

      return addTopic(`Cypress poll locked ${stamp}`, 1, '1000-01-01 00:00:00');
    }).then((set) => {
      ids.locked = set;

      return addTopic(`Cypress poll ended ${stamp}`, 0, '2001-01-01 00:00:00');
    }).then((set) => {
      ids.ended = set;
    });
  });

  afterEach(() => {
    if ('pollAllowVoteOne' in old) {
      cy.kunena_setOption('pollAllowVoteOne', old.pollAllowVoteOne === undefined ? null : old.pollAllowVoteOne);
      delete old.pollAllowVoteOne;
    }
    cy.task('queryDB', `SELECT id, poll_id FROM #__kunena_topics WHERE subject LIKE 'Cypress poll % ${stamp}'`).then((rows) => {
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

  it('does not count the vote of a guest', () => {
    vote(ids.open, ids.open.first);
    totalVotes(ids.open.poll).should('eq', 0);
  });

  it('counts one vote of a user for an option of the poll', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    vote(ids.open, ids.open.first);
    totalVotes(ids.open.poll).should('eq', 1);
    cy.task('queryDB', `SELECT userid, lastvote FROM #__kunena_polls_users WHERE pollid = ${ids.open.poll}`).then((rows) => {
      expect(rows).to.have.length(1);
      expect(Number(rows[0].userid)).to.eq(user.id);
      expect(Number(rows[0].lastvote)).to.eq(ids.open.first);
    });
  });

  it('does not count a vote without a token or for an option of another poll', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    vote(ids.open, ids.open.first, false);
    vote(ids.open, ids.locked.first);
    vote(ids.open, 99999999);
    vote(ids.open, 0);
    totalVotes(ids.open.poll).should('eq', 0);
    totalVotes(ids.locked.poll).should('eq', 0);
  });

  it('keeps the number of votes of a user at one when he votes again', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    vote(ids.open, ids.open.first);
    vote(ids.open, ids.open.second);
    vote(ids.open, ids.open.second);
    totalVotes(ids.open.poll).should('eq', 1);
  });

  it('lets a user change his vote after the time between votes, also a day later, and not before', () => {
    const lastVote = () => cy.task('queryDB', `SELECT lastvote FROM #__kunena_polls_users WHERE pollid = ${ids.open.poll} AND userid = ${user.id}`).then((rows) => Number(rows[0].lastvote));
    const votedAgo = (interval) => cy.task('queryDB', `INSERT INTO #__kunena_polls_users (pollid, userid, votes, lasttime, lastvote) VALUES (${ids.open.poll}, ${user.id}, 1, UTC_TIMESTAMP() - INTERVAL ${interval}, ${ids.open.first}) ON DUPLICATE KEY UPDATE lasttime = UTC_TIMESTAMP() - INTERVAL ${interval}, lastvote = ${ids.open.first}`);

    // A vote may be changed (not only one vote), 15 minutes after the last one (the default time between votes).
    cy.kunena_setOption('pollAllowVoteOne', '0').then((value) => {
      old.pollAllowVoteOne = value;
    });
    cy.kunena_setOption('pollTimeBtVotes', '00:15:00');
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_polls_options SET votes = 1 WHERE id = ${ids.open.first}`));
    cy.doFrontendLogin(user.name, user.password, false);

    // 5 minutes ago: too early.
    cy.then(() => votedAgo('5 MINUTE'));
    cy.then(() => vote(ids.open, ids.open.second));
    cy.then(() => lastVote()).should('eq', ids.open.first);

    // A day and 5 minutes ago: long enough (the hours of the difference started again at 0 after a day).
    cy.then(() => votedAgo('1 DAY - INTERVAL 5 MINUTE'));
    cy.then(() => vote(ids.open, ids.open.second));
    cy.then(() => lastVote()).should('eq', ids.open.second);
  });

  // The votes of the option went up before the row of the voter was added: with requests at the same time each request saw no
  // vote of the user and counted his vote in the option, and only the row of the first one was added.
  it('counts one vote of a user who sends his vote several times at the same time', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&catid=3&id=${ids.open.topic}`);
    cy.document().then((doc) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(doc.documentElement.innerHTML)[1];

      return cy.window().then((win) => Promise.all(Array.from({ length: 8 }, () => {
        const body = new win.FormData();

        body.append('id', ids.open.topic);
        body.append('catid', 3);
        body.append('kpollradio', ids.open.first);
        body.append(token, '1');

        // From the page: its address is not prefixed with the base address like the ones of cy.request.
        return win.fetch(`${Cypress.config('baseUrl').replace(/\/$/, '')}${base}&view=topic&task=vote`, { method: 'POST', body, credentials: 'same-origin' });
      })));
    });
    totalVotes(ids.open.poll).should('eq', 1);
  });

  it('does not count a vote in a topic which is locked or a poll which has ended', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    vote(ids.locked, ids.locked.first);
    vote(ids.ended, ids.ended.first);
    totalVotes(ids.locked.poll).should('eq', 0);
    totalVotes(ids.ended.poll).should('eq', 0);
  });
});
