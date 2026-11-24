// The edit of a post tells the users who are newly mentioned in it (their inbox, and an e-mail). It told them before the checks of the
// edit and its save: an edit which was refused (an address in the subject, too many links, an empty text...) still notified them, of
// a text which is not on the forum, and the corrected edit, saved later, notified them again.
describe('Kunena edit: a refused edit mentions nobody', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const author = { name: Cypress.expose('username'), password: Cypress.expose('password'), id: 797 };
  const mentioned = 798;
  const ids = {};
  const old = {};

  beforeEach(() => {
    cy.kunena_setOption('mentionEmail', '0').then((value) => {
      old.mentionEmail = value ?? null;
    });
    cy.kunena_setOption('urlSubjectTopic', '1').then((value) => {
      old.urlSubjectTopic = value ?? null;
    });
    // A reply of the author in the public topic of the test site.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (11, 9, 3, '${author.name}', ${author.id}, '', 'Re: Which template do you use?', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A reply ${stamp}.')`);
    });
  });

  afterEach(() => {
    cy.kunena_setOption('mentionEmail', old.mentionEmail);
    cy.then(() => cy.kunena_setOption('urlSubjectTopic', old.urlSubjectTopic));
    cy.task('queryDB', `DELETE FROM #__kunena_inbox WHERE message_id = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('queryDB', "UPDATE #__kunena_topics t JOIN #__kunena_messages m ON m.id = 11 SET t.posts = 2, t.last_post_id = 11, t.last_post_userid = 798, t.last_post_time = m.time WHERE t.id = 9");
    cy.task('queryDB', `UPDATE #__kunena_users u SET posts = (SELECT COUNT(*) FROM #__kunena_messages m WHERE m.userid = u.userid AND m.hold = 0) WHERE u.userid = ${author.id}`);
    cy.kunena_fixCategoryCounters();
  });

  it('does not tell the mentioned user of an edit with an address in its subject', () => {
    cy.doFrontendLogin(author.name, author.password, false);
    cy.then(() => cy.request(`${base}&view=topic&layout=edit&catid=3&id=9&mesid=${ids.message}`)).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=edit`,
        form: true,
        body: {
          mesid: ids.message,
          subject: 'See https://example.com/offer',
          message: `A reply ${stamp}, [mention userid=${mentioned}]ci-user[/mention]`,
          [token]: '1',
        },
        followRedirect: false,
        failOnStatusCode: false,
      });
    });
    cy.task('queryDB', `SELECT message FROM #__kunena_messages_text WHERE mesid = ${ids.message}`).then((rows) => {
      expect(rows[0].message, 'the edit is refused').to.eq(`A reply ${stamp}.`);
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_inbox WHERE message_id = ${ids.message} AND user_id = ${mentioned}`).then((rows) => {
      expect(Number(rows[0].n), 'the notification of the mention').to.eq(0);
    });
  });
});
