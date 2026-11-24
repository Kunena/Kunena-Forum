// The edit of a reply by a moderator (or by the author of the topic, when the users may change the subject) set the subject of the
// topic to the subject of the reply, also when the edit did not change it: a reply keeps the subject the topic had when it was
// written ("Re: ..."), so a fix of a typo in an old reply renamed the topic back.
describe('Kunena edit: a reply renames the topic only when its subject is changed', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const old = {};

  beforeEach(() => {
    cy.task('queryDB', 'SELECT subject FROM #__kunena_topics WHERE id = 9').then((rows) => {
      old.subject = rows[0].subject;
    });
    // An old reply of ci-user, with the subject the topic had then.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (11, 9, 3, 'ci-user', 798, '', 'Old subject ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A reply ${stamp}.')`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_history WHERE message_id = ${ids.message}`);
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_topics t JOIN #__kunena_messages m ON m.id = 11 SET t.subject = '${old.subject.replaceAll("'", "\'")}', t.posts = 2, t.last_post_id = 11, t.last_post_userid = 798, t.last_post_time = m.time WHERE t.id = 9`));
    cy.task('queryDB', 'UPDATE #__kunena_users u SET posts = (SELECT COUNT(*) FROM #__kunena_messages m WHERE m.userid = u.userid AND m.hold = 0) WHERE u.userid = 798');
    cy.kunena_fixCategoryCounters();
  });

  it('keeps the subject of the topic when a moderator edits the text of an old reply', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.then(() => cy.request(`${base}&view=topic&layout=edit&catid=3&id=9&mesid=${ids.message}`)).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=edit`,
        form: true,
        body: { mesid: ids.message, subject: `Old subject ${stamp}`, message: `A reply ${stamp}, without the typo.`, [token]: '1' },
        followRedirect: false,
        failOnStatusCode: false,
      });
    });
    cy.task('queryDB', `SELECT message FROM #__kunena_messages_text WHERE mesid = ${ids.message}`).its('0.message').should('eq', `A reply ${stamp}, without the typo.`);
    cy.then(() => cy.task('queryDB', 'SELECT subject FROM #__kunena_topics WHERE id = 9')).its('0.subject').should('eq', old.subject);
  });
});
