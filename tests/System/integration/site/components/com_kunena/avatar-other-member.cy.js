// The avatar tab of the profile form of another member (a moderator or an administrator edits it): the upload and the remove
// button change the avatar of that member (the field kunena_userid of the form), not the one of the moderator who sends them. A
// member may not change the avatar of another member, and such a request changes his own avatar neither.
describe('Kunena avatars: the avatar of the profile which is edited', () => {
  const base = '/index.php?option=com_kunena';
  const admin = { name: Cypress.expose('username'), password: Cypress.expose('password'), id: 797 };
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const folder = `${Cypress.expose('cmsPath')}/media/kunena/avatars/users`;
  // The avatars which the test puts on the disk (the content of an avatar file is not looked at when it is shown).
  const content = 'not really a picture';
  // The upload is a picture (a PNG of 1 x 1): the upload of an avatar refuses a file which is not one.
  const png = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
  const old = {};

  const avatarOf = (id) => cy.task('queryDB', `SELECT avatar FROM #__kunena_users WHERE userid = ${id}`).then((rows) => rows[0].avatar);
  const token = () => cy.request(`${base}&view=home`).then((page) => /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x');
  // The bytes of the picture go from a page of the forum, as the uploader sends them (a request of Cypress does not send the
  // bytes of a file as they are).
  const upload = (userid) => token().then((value) => cy.visit(`${base}&view=home`).then((win) => {
    const bytes = win.Uint8Array.from(win.atob(png), (c) => c.charCodeAt(0));
    const query = new win.URLSearchParams({ userid, filename: 'me.png', size: bytes.length, mime: 'image/png', hash: 'abc', chunkStart: 0, chunkEnd: bytes.length });
    const root = win.location.pathname.split('/index.php')[0];

    return cy.wrap(win.fetch(`${root}${base}&view=user&task=upload&format=json&${value}=1&${query}`, {
      method: 'POST',
      body: new win.Blob([bytes], { type: 'application/octet-stream' }),
      credentials: 'same-origin',
    }).then((response) => ({ status: response.status })));
  }));
  const remove = (userid) => token().then((value) => cy.request({
    method: 'POST',
    url: `${base}&view=user&task=removeavatar&format=json&${value}=1&userid=${userid}`,
    failOnStatusCode: false,
  }));
  // Both members have an avatar of their own, on the disk and in the table.
  const giveAvatars = () => [admin, user].forEach(({ id }) => {
    cy.writeFile(`${folder}/avatar${id}.png`, content);
    cy.task('queryDB', `UPDATE #__kunena_users SET avatar = 'users/avatar${id}.png' WHERE userid = ${id}`);
  });

  beforeEach(() => {
    [admin, user].forEach(({ id }) => avatarOf(id).then((value) => {
      old[id] = value;
    }));
    giveAvatars();
  });

  afterEach(() => {
    cy.then(() => [admin, user].forEach(({ id }) => {
      cy.task('queryDB', `UPDATE #__kunena_users SET avatar = ${old[id] === null ? 'NULL' : `'${old[id]}'`} WHERE userid = ${id}`);
      cy.task('deleteRelativePath', `media/kunena/avatars/users/avatar${id}.png`);
    }));
  });

  it('removes the avatar of the member whose profile an administrator edits, and keeps his own', () => {
    cy.doFrontendLogin(admin.name, admin.password, false);
    remove(user.id).its('status').should('eq', 200);
    avatarOf(user.id).should('eq', '');
    avatarOf(admin.id).should('eq', `users/avatar${admin.id}.png`);
    cy.readFile(`${folder}/avatar${admin.id}.png`).should('eq', content);
  });

  it('uploads the avatar of the member whose profile an administrator edits, and keeps his own', () => {
    cy.task('queryDB', `UPDATE #__kunena_users SET avatar = '' WHERE userid = ${user.id}`);
    cy.doFrontendLogin(admin.name, admin.password, false);
    upload(user.id);
    avatarOf(user.id).should('eq', `users/avatar${user.id}.png`);
    avatarOf(admin.id).should('eq', `users/avatar${admin.id}.png`);
    cy.readFile(`${folder}/avatar${admin.id}.png`).should('eq', content);
  });

  it('does not let a member change the avatar of another member, nor his own by that request', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    upload(admin.id).its('status').should('eq', 403);
    remove(admin.id).its('status').should('eq', 403);
    avatarOf(admin.id).should('eq', `users/avatar${admin.id}.png`);
    avatarOf(user.id).should('eq', `users/avatar${user.id}.png`);
    cy.readFile(`${folder}/avatar${user.id}.png`).should('eq', content);
  });
});
