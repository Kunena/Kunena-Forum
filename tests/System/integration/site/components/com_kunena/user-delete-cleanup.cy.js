// A user who is deleted in Joomla (the users page of the backend) loses his data of the forum: the profile row with its personal
// data, the avatar file, the subscriptions and the follows. (Before, only a deletion through the users page of Kunena removed the
// profile row; a deletion in Joomla left everything behind.)
describe('Kunena users: the deletion of a user in Joomla', () => {
  const root = 'E:/www/apache/htdocs/tests';
  let second;

  beforeEach(() => {
    cy.task(
      'queryDB',
      "INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Deleted', 'ci-deleted', 'ci-deleted@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798",
    ).then((result) => {
      second = result.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${second}, 2)`);
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, personalText, location, avatar) VALUES (${second}, 'A personal text', 'A town', 'users/avatar${second}.png')`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${second}, 9, 3, 0, 0, 0, 0, 1, '')`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_follow (user_id, followed_id, created_time) VALUES (${second}, 797, UNIX_TIMESTAMP())`);
      cy.writeFile(`${root}/media/kunena/avatars/users/avatar${second}.png`, 'not really a picture');
    });
  });

  afterEach(() => {
    // Whatever the test left (the user is deleted by the test itself when all goes well).
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE user_id = ${second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_follow WHERE user_id = ${second}`);
    cy.task('deleteRelativePath', `media/kunena/avatars/users/avatar${second}.png`);
    cy.task('queryDB', `DELETE FROM #__kunena_drafts WHERE user_id = ${second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_inbox_blocks WHERE user_id = ${second} OR blocked_id = ${second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_karma WHERE userid = ${second} OR target_userid = ${second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_sessions WHERE userid = ${second}`);
    cy.task('queryDB', `DELETE FROM #__kunena_inbox WHERE user_id = ${second} OR sender_id = ${second}`);
  });

  it('removes the profile, the avatar, the subscriptions and the follows of the user', () => {
    cy.doAdministratorLogin();
    cy.request('/administrator/index.php?option=com_users&view=users').then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: '/administrator/index.php?option=com_users&task=users.delete',
        form: true,
        body: { 'cid[]': second, [token]: '1' },
        followRedirect: false,
      });
    });

    cy.task('queryDB', `SELECT id FROM #__users WHERE id = ${second}`).then((rows) => expect(rows, 'the user of Joomla').to.have.length(0));
    cy.task('queryDB', `SELECT userid FROM #__kunena_users WHERE userid = ${second}`).then((rows) => expect(rows, 'the profile of the forum').to.have.length(0));
    cy.task('queryDB', `SELECT user_id FROM #__kunena_user_topics WHERE user_id = ${second}`).then((rows) => expect(rows, 'the subscriptions').to.have.length(0));
    cy.task('queryDB', `SELECT user_id FROM #__kunena_user_follow WHERE user_id = ${second}`).then((rows) => expect(rows, 'the follows').to.have.length(0));
    cy.readFile(`${root}/media/kunena/avatars/users/avatar${second}.png`, { log: false }).should('not.exist');
  });

  it('removes the drafts, the inbox, the blocks, the karma and the session of the user; the other side keeps its messages', () => {
    const rows = (sql) => cy.task('queryDB', sql).then((result) => result.length);

    cy.task('queryDB', `INSERT INTO #__kunena_drafts (user_id, catid, parent_id, topic_id, message_id, subject, message, fields, created_time, modified_time) VALUES (${second}, 3, 0, 0, 0, 'A draft', 'The private text of a draft.', '', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())`);
    cy.task('queryDB', `INSERT INTO #__kunena_inbox_blocks (user_id, blocked_id, created_time) VALUES (${second}, 797, UNIX_TIMESTAMP())`);
    cy.task('queryDB', `INSERT INTO #__kunena_karma (userid, target_userid, time) VALUES (${second}, 797, UNIX_TIMESTAMP())`);
    cy.task('queryDB', `INSERT INTO #__kunena_sessions (userid, allowed, lasttime, readtopics, currvisit) VALUES (${second}, 'na', UNIX_TIMESTAMP(), '0', UNIX_TIMESTAMP())`);
    // His inbox: a message from ci-user and a notification; and a message he sent to ci-user.
    cy.task('queryDB', `INSERT INTO #__kunena_inbox (user_id, sender_id, type, conversation_id, subject, body, created_time) VALUES (${second}, 798, 'message', 0, 'To him', 'A message to him.', UNIX_TIMESTAMP())`).then((result) => {
      cy.wrap(result.insertId).as('toHim');
    });
    cy.task('queryDB', `INSERT INTO #__kunena_inbox (user_id, sender_id, type, conversation_id, subject, body, created_time) VALUES (${second}, 0, 'notification', 0, 'A reply', 'A notification.', UNIX_TIMESTAMP())`);
    cy.task('queryDB', `INSERT INTO #__kunena_inbox (user_id, sender_id, type, conversation_id, subject, body, created_time) VALUES (798, ${second}, 'message', 0, 'From him', 'A message from him.', UNIX_TIMESTAMP())`).then((result) => {
      cy.wrap(result.insertId).as('fromHim');
    });

    cy.doAdministratorLogin();
    cy.request('/administrator/index.php?option=com_users&view=users').then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({ method: 'POST', url: '/administrator/index.php?option=com_users&task=users.delete', form: true, body: { 'cid[]': second, [token]: '1' }, followRedirect: false });
    });

    cy.then(() => rows(`SELECT id FROM #__kunena_drafts WHERE user_id = ${second}`)).should('eq', 0);
    cy.then(() => rows(`SELECT user_id FROM #__kunena_inbox_blocks WHERE user_id = ${second} OR blocked_id = ${second}`)).should('eq', 0);
    cy.then(() => rows(`SELECT id FROM #__kunena_karma WHERE userid = ${second}`)).should('eq', 0);
    cy.then(() => rows(`SELECT userid FROM #__kunena_sessions WHERE userid = ${second}`)).should('eq', 0);
    // Nothing is left in his inbox; the message of ci-user stays in the sent messages of ci-user, his message in the inbox of ci-user.
    cy.then(() => rows(`SELECT id FROM #__kunena_inbox WHERE user_id = ${second} AND deleted_recipient = 0`)).should('eq', 0);
    cy.get('@fromHim').then((id) => rows(`SELECT id FROM #__kunena_inbox WHERE id = ${id} AND deleted_recipient = 0`)).should('eq', 1);
  });
});
