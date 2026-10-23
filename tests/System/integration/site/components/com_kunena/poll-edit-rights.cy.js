// The poll of a topic is changed with the edit form of the topic. Only the owner of the topic (and a moderator) may change it:
// a user who edits his own reply in the topic of another user must not change the poll of that topic with the same form.
describe('Kunena polls: who may change the poll of a topic', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const ids = {};
  const old = {};

  const addMessage = (parent, author, subject) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, 3, 'author', ${author}, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A text.')`).then(() => message.insertId));

  const pollOf = () => cy.task('queryDB', `SELECT p.title, GROUP_CONCAT(o.text ORDER BY o.id) AS options FROM #__kunena_polls AS p LEFT JOIN #__kunena_polls_options AS o ON o.pollid = p.id WHERE p.id = ${ids.poll} GROUP BY p.id`).then((rows) => rows[0]);

  // The edit form of a message sends the poll fields with it.
  const edit = (mesid) => cy.request(`${base}&view=topic&catid=3&id=${ids.topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=edit`,
      form: true,
      body: {
        catid: 3,
        id: ids.topic,
        mesid,
        subject: 'Re: a poll',
        message: 'The text of the reply, changed.',
        poll_title: 'A poll of somebody else',
        [`polloptionsID[${ids.option}]`]: 'Changed option',
        'polloptionsID[new1]': 'An option of somebody else',
        [token]: '1',
      },
      failOnStatusCode: false,
      followRedirect: false,
    });
  });

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
    // A topic of the administrator with a poll without votes, and a reply of the user.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress poll rights ${stamp}', 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return addMessage(0, admin, `Cypress poll rights ${stamp}`);
    }).then((first) => {
      ids.first = first;

      return addMessage(first, user.id, 'Re: a poll');
    }).then((reply) => {
      ids.reply = reply;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.first}, last_post_id = ${reply} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${admin}, ${ids.topic}, 3, 1, ${ids.first}, 1, 0, 0, ''), (${user.id}, ${ids.topic}, 3, 1, ${reply}, 0, 0, 0, '')`);

      return cy.task('queryDB', `INSERT INTO #__kunena_polls (title, threadid, polltimetolive) VALUES ('The poll of the owner', ${ids.topic}, '1000-01-01 00:00:00')`);
    }).then((poll) => {
      ids.poll = poll.insertId;
      cy.task('queryDB', `UPDATE #__kunena_topics SET poll_id = ${poll.insertId} WHERE id = ${ids.topic}`);

      return cy.task('queryDB', `INSERT INTO #__kunena_polls_options (pollid, text, votes) VALUES (${poll.insertId}, 'Yes', 0), (${poll.insertId}, 'No', 0)`);
    }).then((options) => {
      ids.option = options.insertId;
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_polls_options WHERE pollid = ${ids.poll}`);
    cy.task('queryDB', `DELETE FROM #__kunena_polls WHERE id = ${ids.poll}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('keeps the poll when a user edits his own reply in the topic of another user', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    edit(ids.reply);
    cy.task('queryDB', `SELECT message FROM #__kunena_messages_text WHERE mesid = ${ids.reply}`).then((rows) => {
      expect(rows[0].message, 'the reply itself is saved').to.eq('The text of the reply, changed.');
    });
    pollOf().then((poll) => {
      expect(poll.title, 'the title of the poll').to.eq('The poll of the owner');
      expect(poll.options, 'the options of the poll').to.eq('Yes,No');
    });
  });

  it('lets the owner of the topic change his poll, also its title', () => {
    // The user is the owner of the topic now.
    cy.task('queryDB', `UPDATE #__kunena_messages SET userid = ${user.id} WHERE id = ${ids.first}`);
    cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_userid = ${user.id} WHERE id = ${ids.topic}`);
    cy.task('queryDB', `UPDATE #__kunena_user_topics SET owner = (user_id = ${user.id}) WHERE topic_id = ${ids.topic}`);
    cy.doFrontendLogin(user.name, user.password, false);
    edit(ids.first);
    pollOf().then((poll) => {
      expect(poll.title, 'the title of the poll').to.eq('A poll of somebody else');
      // The form sends all the options: an option which it does not send (here "No") is removed.
      expect(poll.options, 'the options of the poll').to.eq('Changed option,An option of somebody else');
    });
  });

  it('lets a moderator change the poll', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    edit(ids.first);
    pollOf().then((poll) => expect(poll.title, 'the title of the poll').to.eq('A poll of somebody else'));
  });
});
