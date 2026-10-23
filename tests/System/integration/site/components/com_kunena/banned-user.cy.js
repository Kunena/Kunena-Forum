// A user who is banned may read the forum, but may not write: no topic, no reply, no change of a message, no thank you, no draft and
// no upload. The requests are sent with a valid token (as the forms would send them), so only the ban can stop them.
describe('Kunena permissions: a banned user', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};
  let oldBan;

  const tables = ['topics', 'messages', 'messages_text', 'drafts', 'thankyou', 'attachments', 'user_topics'];
  const snapshot = () => cy.task('queryDB', `CHECKSUM TABLE ${tables.map((table) => `#__kunena_${table}`).join(', ')}`).then((rows) => rows.map((row) => row.Checksum).join('-'));

  const ban = (value) => cy.task('queryDB', `UPDATE #__kunena_users SET banned = '${value}' WHERE userid = ${user.id}`);

  beforeEach(() => {
    cy.task('queryDB', `SELECT CAST(banned AS CHAR) AS banned FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      oldBan = rows[0].banned;
    });
    // A message of the user, in the topic 9.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (10, 9, 3, 'ci-user', ${user.id}, '', 'Re: Cypress banned ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      ids.own = message.insertId;

      return cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'My own message.')`);
    });
    cy.doFrontendLogin(user.name, user.password, false);
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.own}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.own}`);
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE 'Cypress banned %${stamp}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_drafts WHERE user_id = ${user.id}`);
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE userid = ${user.id} AND mesid = 0`);
    cy.task('queryDB', `DELETE FROM #__kunena_thankyou WHERE userid = ${user.id}`);
    cy.task('queryDB', `UPDATE #__kunena_user_topics SET subscribed = 0 WHERE user_id = ${user.id} AND topic_id = 9`);
    cy.then(() => ban(oldBan));
    cy.task('deleteRelativePath', `media/kunena/attachments/${user.id}`);
    // The counters of the users and the rows of the topics which are gone.
    cy.kunena_fixUserCounters();
  });

  const writeTopic = (token) => cy.request({
    method: 'POST',
    url: `${base}&view=topic&task=post`,
    form: true,
    body: { catid: 3, subject: `Cypress banned topic ${stamp}`, message: 'A topic of a banned user.', [token]: '1' },
    failOnStatusCode: false,
    followRedirect: false,
  });

  const token = () => cy.request(`${base}&view=topic&catid=3&id=9`).then((response) => /name="([0-9a-f]{32})" value="1"/.exec(response.body)[1]);

  it('can write a topic when he is not banned (so the test can see a change)', () => {
    token().then((value) => {
      snapshot().then((start) => {
        writeTopic(value);
        snapshot().then((end) => expect(end, 'the data after a topic of a user who is not banned').to.not.eq(start));
      });
    });
  });

  it('writes nothing, changes nothing and uploads nothing', () => {
    ban('9999-12-31 23:59:59');

    token().then((value) => {
      snapshot().then((start) => {
        const form = (task, body) => cy.request({ method: 'POST', url: `${base}&view=topic&task=${task}`, form: true, body: { ...body, [value]: '1' }, failOnStatusCode: false, followRedirect: false });

        writeTopic(value);
        form('post', { catid: 3, id: 9, parentid: 10, subject: `Re: Cypress banned reply ${stamp}`, message: 'A reply of a banned user.' });
        form('edit', { catid: 3, id: 9, mesid: ids.own, subject: `Re: Cypress banned ${stamp}`, message: 'Changed by a banned user.' });
        form('savedraft', { catid: 3, subject: `Cypress banned draft ${stamp}`, message: 'A draft of a banned user.' });
        cy.request({ url: `${base}&view=topic&catid=3&id=9&mesid=10&task=thankyou&${value}=1`, failOnStatusCode: false, followRedirect: false });
        cy.request({ url: `${base}&view=topic&catid=3&id=9&mesid=${ids.own}&task=delete&${value}=1`, failOnStatusCode: false, followRedirect: false });
        cy.request({
          method: 'POST',
          url: `${base}&view=topic&task=upload&format=json&${value}=1`,
          qs: { catid: 3, filename: 'banned.txt', size: 1, mime: 'text/plain', hash: 'abc', chunkStart: 0, chunkEnd: 1 },
          headers: { 'Content-Type': 'application/octet-stream' },
          body: 'x',
          failOnStatusCode: false,
        }).its('body.success').should('eq', false);

        snapshot().then((end) => expect(end, 'the data after the requests of a banned user').to.eq(start));
      });
    });
  });
});
