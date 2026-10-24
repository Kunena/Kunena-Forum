// The own status of a user (tasks user.status and user.statustext, as the form sends it) and following another user (user.follow, user.unfollow):
// - the status is one of 0-3 (another number is "online"), the text of the status is cut at 191 characters;
// - a user follows another one and stops it again, but does not follow a user whom he ignores.
describe('Kunena user: status and following', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const saved = {};
  const me = () => cy.task('queryDB', `SELECT status, status_text FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => ({ status: Number(rows[0].status), text: rows[0].status_text }));
  const follows = () => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_user_follow WHERE user_id = ${user.id} AND followed_id = 797`).then((rows) => Number(rows[0].n));
  const token = () => cy.request(`${base}&view=user`).then((page) => /"csrf\.token":"([0-9a-f]{32})"/.exec(page.body)?.[1] || /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1]);
  const send = (task, body = {}) => token().then((t) => cy.request({ method: 'POST', url: `${base}&view=user&task=${task}`, form: true, body: { ...body, [t]: '1' }, followRedirect: false }));

  beforeEach(() => {
    me().then((row) => {
      saved.status = row.status;
      saved.text = row.text;
    });
    cy.doFrontendLogin(user.name, user.password, false);
  });

  afterEach(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET status = ${saved.status}, status_text = ${saved.text === null ? 'NULL' : `'${saved.text.replace(/'/g, "''")}'`} WHERE userid = ${user.id}`));
    cy.task('queryDB', `DELETE FROM #__kunena_user_follow WHERE user_id = ${user.id}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_ignore WHERE user_id = ${user.id}`);
  });

  it('saves a known status and the text of the status, cut at 191 characters', () => {
    cy.then(() => send('status', { status: 2 }));
    cy.then(() => me()).its('status').should('eq', 2);
    cy.then(() => send('status', { status: 9 }));
    cy.then(() => me()).its('status').should('eq', 0);
    cy.then(() => send('statustext', { status_text: 'é'.repeat(200) }));
    cy.then(() => me()).its('text').should('eq', 'é'.repeat(191));
  });

  it('follows a user and stops it, and does not follow an ignored user', () => {
    cy.then(() => send('follow', { userid: 797 }));
    cy.then(() => follows()).should('eq', 1);
    cy.then(() => send('unfollow', { userid: 797 }));
    cy.then(() => follows()).should('eq', 0);
    cy.task('queryDB', `INSERT INTO #__kunena_user_ignore (user_id, ignored_id, created_time) VALUES (${user.id}, 797, UNIX_TIMESTAMP())`);
    cy.then(() => send('follow', { userid: 797 }));
    cy.then(() => follows()).should('eq', 0);
  });
});
