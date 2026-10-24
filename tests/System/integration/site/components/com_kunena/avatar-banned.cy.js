// A banned user may not change his profile (the rule "edit" of the user), so he may not change his avatar through the upload of
// the profile form either: the ban box deletes a bad avatar, and the user must not be able to put another one at once. A user who
// is not banned can.
describe('Kunena avatars: a banned user keeps his avatar', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  // The upload of an avatar looks at the extension of the name (the content is not looked at).
  const content = 'not really a picture';
  let old;

  const avatar = () => cy.task('queryDB', `SELECT avatar, CAST(banned AS CHAR) AS banned FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => rows[0]);
  const upload = () => cy.request(`${base}&view=home`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({
      method: 'POST',
      url: `${base}&view=user&task=upload&format=json&${token}=1`,
      qs: { filename: 'me.png', size: content.length, mime: 'image/png', hash: 'abc', chunkStart: 0, chunkEnd: content.length },
      headers: { 'Content-Type': 'application/octet-stream' },
      body: content,
      failOnStatusCode: false,
    });
  });

  beforeEach(() => {
    avatar().then((row) => {
      old = row;
    });
  });

  afterEach(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET avatar = ${old.avatar === null ? 'NULL' : `'${old.avatar}'`}, banned = '${old.banned}' WHERE userid = ${user.id}`));
    cy.task('deleteRelativePath', `media/kunena/avatars/users/avatar${user.id}.png`);
  });

  it('lets a user who is not banned upload his avatar', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    upload();
    avatar().its('avatar').should('eq', `users/avatar${user.id}.png`);
  });

  it('does not let a banned user change his avatar', () => {
    cy.task('queryDB', `UPDATE #__kunena_users SET avatar = '', banned = DATE_ADD(NOW(), INTERVAL 1 DAY) WHERE userid = ${user.id}`);
    cy.doFrontendLogin(user.name, user.password, false);
    upload();
    avatar().its('avatar').should('eq', '');
  });
});
