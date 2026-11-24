// "Let users edit the subject" (allowChangeSubject) set to No: the forms show the subject without a field to change it (a disabled
// field, or a hidden one with the subject). The server took the posted subject anyway: a member changed the subject of his post with
// a request of his own, and a reply had a subject of its own.
describe('Kunena post: the subject cannot be changed when the forum says so', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const subject = 'Re: Which template do you use?';
  const ids = {};
  const old = {};

  const token = (url) => cy.request(url).then((page) => /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1]);

  beforeEach(() => {
    cy.kunena_setOption('allowChangeSubject', '0').then((value) => {
      old.allowChangeSubject = value ?? null;
    });
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.floodProtection = value ?? null;
    });
    // A reply of the member in the public topic of the test site.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (11, 9, 3, '${user.name}', ${user.id}, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A reply ${stamp}.')`);
    });
  });

  afterEach(() => {
    cy.kunena_setOption('allowChangeSubject', old.allowChangeSubject);
    cy.then(() => cy.kunena_setOption('floodProtection', old.floodProtection));
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = 9 AND (m.id = ${ids.message} OR x.message LIKE '%${stamp}%')`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = 9 AND id > 11 AND id NOT IN (SELECT mesid FROM #__kunena_messages_text)`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_history WHERE message_id = ${ids.message}`);
    cy.task('queryDB', `UPDATE #__kunena_topics t JOIN #__kunena_messages m ON m.id = 11 SET t.subject = '${subject}', t.posts = 2, t.last_post_id = 11, t.last_post_userid = 798, t.last_post_time = m.time WHERE t.id = 9`);
    cy.task('queryDB', `UPDATE #__kunena_user_topics SET posts = 2, last_post_id = 11 WHERE user_id = ${user.id} AND topic_id = 9`);
    cy.task('queryDB', `UPDATE #__kunena_users u SET posts = (SELECT COUNT(*) FROM #__kunena_messages m WHERE m.userid = u.userid AND m.hold = 0) WHERE u.userid = ${user.id}`);
    cy.kunena_fixCategoryCounters();
  });

  it('keeps the subject of an edited post', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    token(`${base}&view=topic&layout=edit&catid=3&id=9&mesid=${ids.message}`).then((name) => cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=edit`,
      form: true,
      body: { mesid: ids.message, subject: `Changed ${stamp}`, message: `A reply ${stamp}, edited.`, [name]: '1' },
      followRedirect: false,
      failOnStatusCode: false,
    }));
    cy.task('queryDB', `SELECT m.subject, x.message FROM #__kunena_messages m JOIN #__kunena_messages_text x ON x.mesid = m.id WHERE m.id = ${ids.message}`).then((rows) => {
      expect(rows[0].message, 'the edit is saved').to.eq(`A reply ${stamp}, edited.`);
      expect(rows[0].subject, 'the subject').to.eq(subject);
    });
  });

  it('gives a reply the subject of the message it answers', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    token(`${base}&view=topic&layout=reply&catid=3&id=9&mesid=${ids.message}`).then((name) => cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=post`,
      form: true,
      body: { parentid: ids.message, catid: 3, subject: `Own ${stamp}`, message: `Another reply ${stamp}.`, [name]: '1' },
      followRedirect: false,
      failOnStatusCode: false,
    }));
    cy.task('queryDB', `SELECT m.subject FROM #__kunena_messages m JOIN #__kunena_messages_text x ON x.mesid = m.id WHERE x.message = 'Another reply ${stamp}.'`).then((rows) => {
      expect(rows.length, 'the reply is saved').to.eq(1);
      expect(rows[0].subject, 'the subject').to.eq(subject);
    });
  });
});
