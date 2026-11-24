// The time in which a member may edit his post (userEditTime) counts from his post or from his own last edit. It counted from any
// edit: the edit of a moderator (a correction, a removed link) gave the author a new edit time for his old post, also to undo it.
describe('Kunena edit: the edit of a moderator gives the author no new edit time', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};
  const old = {};

  beforeEach(() => {
    [['floodProtection', '0'], ['userEdit', '1'], ['userEditTime', '300'], ['userEditTimeGrace', '0']].forEach(([name, value]) => {
      cy.kunena_setOption(name, value).then((previous) => {
        old[name] = previous ?? null;
      });
    });
    // A reply of the member of an hour ago, which a moderator (ci-admin) edited a minute ago.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (11, 9, 3, '${user.name}', ${user.id}, '', 'Re: Which template do you use?', UNIX_TIMESTAMP() - 3600, '127.0.0.1', 0, 0, 0, 0, 0, 0, 797, UNIX_TIMESTAMP() - 60, 'Link removed')`,
    ).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A reply ${stamp}.')`);
    });
  });

  afterEach(() => {
    Object.keys(old).forEach((name) => cy.kunena_setOption(name, old[name]));
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_history WHERE message_id = ${ids.message}`);
    cy.task('queryDB', 'UPDATE #__kunena_topics t JOIN #__kunena_messages m ON m.id = 11 SET t.posts = 2, t.last_post_id = 11, t.last_post_userid = 798, t.last_post_time = m.time WHERE t.id = 9');
    cy.task('queryDB', `UPDATE #__kunena_users u SET posts = (SELECT COUNT(*) FROM #__kunena_messages m WHERE m.userid = u.userid AND m.hold = 0) WHERE u.userid = ${user.id}`);
    cy.kunena_fixCategoryCounters();
  });

  it('does not let the author edit his old post after the edit of a moderator', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=topic&catid=3&id=9`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=edit`,
        form: true,
        body: { mesid: ids.message, subject: 'Re: Which template do you use?', message: `The link again ${stamp}.`, [token]: '1' },
        followRedirect: false,
        failOnStatusCode: false,
      });
    });
    cy.task('queryDB', `SELECT message FROM #__kunena_messages_text WHERE mesid = ${ids.message}`).its('0.message').should('eq', `A reply ${stamp}.`);
  });
});
