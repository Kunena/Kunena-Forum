// The avatar tab of the profile form of another member (a moderator or an administrator edits it): the upload and the remove
// button change the avatar of that member (the field kunena_userid of the form), not the one of the moderator who sends them. A
// member may not change the avatar of another member, and such a request changes his own avatar neither.
describe('Kunena avatars: the avatar of the profile which is edited', () => {
  const base = '/index.php?option=com_kunena';
  const admin = { name: Cypress.expose('username'), password: Cypress.expose('password'), id: 797 };
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const folder = `${Cypress.expose('cmsPath')}/media/kunena/avatars/users`;
  // The upload of an avatar looks at the extension of the name (the content is not looked at).
  const content = 'not really a picture';
  const old = {};

  const avatarOf = (id) => cy.task('queryDB', `SELECT avatar FROM #__kunena_users WHERE userid = ${id}`).then((rows) => rows[0].avatar);
  const token = () => cy.request(`${base}&view=home`).then((page) => /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x');
  const upload = (userid) => token().then((value) => cy.request({
    method: 'POST',
    url: `${base}&view=user&task=upload&format=json&${value}=1`,
    qs: { userid, filename: 'me.png', size: content.length, mime: 'image/png', hash: 'abc', chunkStart: 0, chunkEnd: content.length },
    headers: { 'Content-Type': 'application/octet-stream' },
    body: content,
    failOnStatusCode: false,
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
