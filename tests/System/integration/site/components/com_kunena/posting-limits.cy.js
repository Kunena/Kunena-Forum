// The limits of writing: the time between two topics (flood protection) for a user and not for a moderator, the time in which a user may
// change his own message, and a subject which is empty or too long. The forms are sent like the browser sends them (with a token).
describe('Kunena posting: the limits', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const old = {};
  const created = [];

  const tokenOf = () => cy.request(`${base}&view=topic&layout=create&catid=3`).then((page) => /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1]);

  const postTopic = (subject, message = 'A message of the test.') => tokenOf().then((token) => cy.request({
    method: 'POST',
    url: `${base}&view=topic&task=post`,
    form: true,
    body: { catid: 3, subject, message, [token]: '1' },
    failOnStatusCode: false,
    followRedirect: false,
  }));

  const topicsWith = (like) => cy.task('queryDB', `SELECT id, subject FROM #__kunena_topics WHERE subject LIKE '${like}%'`);

  before(() => {
    ['floodProtection', 'userEdit', 'userEditTime', 'userEditTimeGrace'].forEach((name) => cy.kunena_setOption(name, null).then((value) => {
      old[name] = value;
    }));
  });

  after(() => {
    Object.entries(old).forEach(([name, value]) => cy.kunena_setOption(name, value ?? ''));
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE 'Cypress limit %${stamp}%'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
  });

  it('stops a second topic of a user within the time of the flood protection, but not of a moderator', () => {
    cy.kunena_setOption('floodProtection', '300');
    // The flood protection goes by the IP address: the posts of the earlier tests (all from this machine) are moved out of its time.
    cy.task('queryDB', "UPDATE #__kunena_messages SET time = time - 600 WHERE ip = '127.0.0.1' AND time > UNIX_TIMESTAMP() - 600");

    cy.doFrontendLogin(user.name, user.password, false);
    postTopic(`Cypress limit flood one ${stamp}`);
    postTopic(`Cypress limit flood two ${stamp}`);
    topicsWith(`Cypress limit flood`).then((rows) => expect(rows.map((row) => row.subject), 'the topics of the user').to.deep.eq([`Cypress limit flood one ${stamp}`]));

    cy.clearAllCookies();
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    postTopic(`Cypress limit modflood one ${stamp}`);
    postTopic(`Cypress limit modflood two ${stamp}`);
    topicsWith('Cypress limit modflood').then((rows) => expect(rows, 'the topics of the moderator').to.have.length(2));
  });

  it('lets a user change his own message in the time which is allowed, and not later', () => {
    cy.kunena_setOption('floodProtection', '0');
    cy.kunena_setOption('userEdit', '1');
    cy.kunena_setOption('userEditTime', '300');
    cy.kunena_setOption('userEditTimeGrace', '0');

    // Two topics of the user: one written now, one an hour ago.
    const make = (name, age) => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress limit ${name} ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - ${age}, ${user.id}, 'x', 'ci-user', 0, UNIX_TIMESTAMP() - ${age}, ${user.id}, 'x', 'ci-user', 0, '')`,
    ).then((topic) => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-user', ${user.id}, '', 'Cypress limit ${name} ${stamp}', UNIX_TIMESTAMP() - ${age}, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'The first text.')`).then(() => cy.task(
      'queryDB',
      `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
    )).then(() => ({ topic: topic.insertId, message: message.insertId }))));

    cy.doFrontendLogin(user.name, user.password, false);

    const edit = (set, text) => cy.request(`${base}&view=topic&catid=3&id=${set.topic}`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=edit`,
        form: true,
        body: { catid: 3, id: set.topic, mesid: set.message, subject: `Cypress limit edited ${stamp}`, message: text, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    const textOf = (set) => cy.task('queryDB', `SELECT message FROM #__kunena_messages_text WHERE mesid = ${set.message}`).then((rows) => rows[0].message);

    make('recent', 60).then((recent) => {
      edit(recent, 'Changed in time.');
      textOf(recent).should('eq', 'Changed in time.');
    });
    make('late', 3600).then((late) => {
      edit(late, 'Changed too late.');
      textOf(late).should('eq', 'The first text.');
    });
  });

  it('refuses a subject which is empty, and does not break on one which is very long', () => {
    cy.kunena_setOption('floodProtection', '0');
    cy.doFrontendLogin(user.name, user.password, false);

    postTopic('   ').its('status').should('be.lessThan', 500);
    topicsWith('   ').then((rows) => expect(rows, 'a topic with an empty subject').to.have.length(0));

    postTopic(`Cypress limit long ${stamp} ${'x'.repeat(400)}`).its('status').should('be.lessThan', 500);
    topicsWith(`Cypress limit long ${stamp}`).then((rows) => rows.forEach((row) => expect(row.subject.length, 'the length of the subject which was kept').to.be.at.most(255)));
  });
});
