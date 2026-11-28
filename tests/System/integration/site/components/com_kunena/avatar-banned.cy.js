// A banned user may not change his profile (the rule "edit" of the user), so he may not change his avatar through the upload of
// the profile form either: the ban box deletes a bad avatar, and the user must not be able to put another one at once. A user who
// is not banned can.
describe('Kunena avatars: a banned user keeps his avatar', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  // A picture (a PNG of 1 x 1): the upload of an avatar refuses a file which is not one.
  const png = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
  let old;

  const avatar = () => cy.task('queryDB', `SELECT avatar, CAST(banned AS CHAR) AS banned FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => rows[0]);
  // The bytes of the picture go from a page of the forum, as the uploader sends them (a request of Cypress does not send the
  // bytes of a file as they are).
  const upload = () => cy.visit(`${base}&view=home`).then((win) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(win.document.documentElement.innerHTML)?.[1] ?? 'x';
    const bytes = win.Uint8Array.from(win.atob(png), (c) => c.charCodeAt(0));
    const query = new win.URLSearchParams({ filename: 'me.png', size: bytes.length, mime: 'image/png', hash: 'abc', chunkStart: 0, chunkEnd: bytes.length });
    const root = win.location.pathname.split('/index.php')[0];

    return cy.wrap(win.fetch(`${root}${base}&view=user&task=upload&format=json&${token}=1&${query}`, {
      method: 'POST',
      body: new win.Blob([bytes], { type: 'application/octet-stream' }),
      credentials: 'same-origin',
    }).then((response) => ({ status: response.status })));
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
