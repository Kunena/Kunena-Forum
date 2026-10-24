// A removal request of the privacy component of Joomla (com_privacy, request type "remove") removes the data of the user in the
// forum (plugin Privacy - Kunena). Besides the profile and the subscriptions of before, the data of 7.2 goes too: the drafts, the
// follows, the ignore list, the inbox (his side of it) and its blocks, the karma, the session.
describe('Kunena privacy: a removal request removes the data of 7.2 too', () => {
  const stamp = Date.now();
  const ids = {};
  const none = (label, sql) => cy.task('queryDB', sql).then((rows) => expect(Number(rows[0].n), label).to.eq(0));

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Forget', 'ci-forget-${stamp}', 'ci-forget-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((result) => {
      ids.user = result.insertId;
      const u = result.insertId;

      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${u}, 2)`);
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, personalText) VALUES (${u}, 'A personal text')`);
      cy.task('queryDB', `INSERT INTO #__kunena_drafts (user_id, catid, parent_id, topic_id, message_id, subject, message, fields, created_time, modified_time) VALUES (${u}, 3, 0, 0, 0, 'A draft', 'The text of a draft.', '', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_follow (user_id, followed_id, created_time) VALUES (${u}, 797, UNIX_TIMESTAMP())`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_ignore (user_id, ignored_id, created_time) VALUES (${u}, 798, UNIX_TIMESTAMP())`);
      cy.task('queryDB', `INSERT INTO #__kunena_inbox_blocks (user_id, blocked_id, created_time) VALUES (${u}, 797, UNIX_TIMESTAMP())`);
      cy.task('queryDB', `INSERT INTO #__kunena_karma (userid, target_userid, time) VALUES (${u}, 797, UNIX_TIMESTAMP())`);
      cy.task('queryDB', `INSERT INTO #__kunena_sessions (userid, allowed, lasttime, readtopics, currvisit) VALUES (${u}, 'na', UNIX_TIMESTAMP(), '0', UNIX_TIMESTAMP())`);
      cy.task('queryDB', `INSERT INTO #__kunena_inbox (user_id, sender_id, type, conversation_id, subject, body, created_time) VALUES (${u}, 798, 'message', 0, 'To him ${stamp}', 'A message to him.', UNIX_TIMESTAMP())`);
      cy.task('queryDB', `INSERT INTO #__kunena_inbox (user_id, sender_id, type, conversation_id, subject, body, created_time) VALUES (${u}, 0, 'notification', 0, 'Notification ${stamp}', '', UNIX_TIMESTAMP())`);
      // A confirmed removal request (the queryDB task removes the row after the test).
      cy.task('queryDB', `INSERT INTO #__privacy_requests (email, requested_at, status, request_type, confirm_token, confirm_token_created_at) VALUES ('ci-forget-${stamp}@example.com', NOW(), 1, 'remove', '', NULL)`).then((request) => {
        ids.request = request.insertId;
      });
    });
  });

  afterEach(() => {
    const u = ids.user;

    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${u}`);
    cy.task('queryDB', `DELETE FROM #__kunena_drafts WHERE user_id = ${u}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_follow WHERE user_id = ${u} OR followed_id = ${u}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_ignore WHERE user_id = ${u} OR ignored_id = ${u}`);
    cy.task('queryDB', `DELETE FROM #__kunena_inbox_blocks WHERE user_id = ${u} OR blocked_id = ${u}`);
    cy.task('queryDB', `DELETE FROM #__kunena_karma WHERE userid = ${u}`);
    cy.task('queryDB', `DELETE FROM #__kunena_sessions WHERE userid = ${u}`);
    cy.task('queryDB', `DELETE FROM #__kunena_inbox WHERE user_id = ${u} OR sender_id = ${u}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${u}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${u}`);
    cy.task('queryDB', `DELETE FROM #__action_logs WHERE extension = 'com_privacy.request' AND item_id = ${ids.request}`);
  });

  it('removes the drafts, follows, ignores, inbox, blocks, karma and session of the user', () => {
    cy.doAdministratorLogin();
    cy.then(() => cy.request(`/administrator/index.php?option=com_privacy&view=request&id=${ids.request}`)).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({ url: `/administrator/index.php?option=com_privacy&task=request.remove&id=${ids.request}&${token}=1`, followRedirect: false });
    });

    cy.then(() => {
      const u = ids.user;

      // The profile went before already: the request was processed.
      none('the profile', `SELECT COUNT(*) AS n FROM #__kunena_users WHERE userid = ${u}`);
      none('the drafts', `SELECT COUNT(*) AS n FROM #__kunena_drafts WHERE user_id = ${u}`);
      none('the follows', `SELECT COUNT(*) AS n FROM #__kunena_user_follow WHERE user_id = ${u}`);
      none('the ignore list', `SELECT COUNT(*) AS n FROM #__kunena_user_ignore WHERE user_id = ${u}`);
      none('the inbox blocks', `SELECT COUNT(*) AS n FROM #__kunena_inbox_blocks WHERE user_id = ${u}`);
      none('the karma', `SELECT COUNT(*) AS n FROM #__kunena_karma WHERE userid = ${u}`);
      none('the session', `SELECT COUNT(*) AS n FROM #__kunena_sessions WHERE userid = ${u}`);
      none('his inbox', `SELECT COUNT(*) AS n FROM #__kunena_inbox WHERE user_id = ${u} AND deleted_recipient = 0`);
    });
  });
});
