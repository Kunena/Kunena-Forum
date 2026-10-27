// The types of the uploads come from the configuration (fileTypes, avatarTypes): a type of a script (php) in that list made the
// upload store a script in the media folder, which a server without the .htaccess of the folder (nginx, IIS) runs. The files of
// the inbox refuse such types whatever the configuration says; the attachments and the avatars do that too now.
describe('Kunena uploads: no types of scripts, whatever the configuration says', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const old = {};
  let token;

  const upload = (view, filename, extra = {}) => cy.request({
    method: 'POST',
    url: `${base}&view=${view}&task=upload&format=json&${token}=1`,
    qs: { filename, size: 13, mime: 'text/plain', hash: 'abc', chunkStart: 0, chunkEnd: 13, ...extra },
    headers: { 'Content-Type': 'application/octet-stream' },
    body: '<?php echo 1;',
    failOnStatusCode: false,
  }).its('body');

  before(() => {
    cy.kunena_setOption('fileTypes', 'txt,php,phtml').then((value) => {
      old.fileTypes = value ?? null;
    });
    cy.kunena_setOption('avatarTypes', 'png,php').then((value) => {
      old.avatarTypes = value ?? null;
    });
    cy.task('queryDB', `SELECT avatar FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      old.avatar = rows[0].avatar;
    });
  });

  after(() => {
    cy.kunena_setOption('fileTypes', old.fileTypes);
    cy.kunena_setOption('avatarTypes', old.avatarTypes);
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET avatar = ${old.avatar === null ? 'NULL' : `'${old.avatar}'`} WHERE userid = ${user.id}`));
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE userid = ${user.id} AND mesid = 0`);
    cy.task('deleteRelativePath', `media/kunena/attachments/${user.id}`);
    cy.task('deleteRelativePath', `media/kunena/avatars/users/avatar${user.id}.php`);
  });

  beforeEach(() => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=topic&layout=create&catid=3`).then((page) => {
      token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];
    });
  });

  it('does not keep a php file as an attachment', () => {
    upload('topic', 'script.php', { catid: 3 }).then((body) => {
      expect(body.data && body.data.id, 'the attachment').to.be.undefined;
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_attachments WHERE userid = ${user.id} AND filename LIKE '%.php'`).its('0.n').should('eq', 0);
  });

  it('does not keep a php file as an avatar', () => {
    upload('user', 'avatar.php');
    cy.task('queryDB', `SELECT avatar FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      expect(String(rows[0].avatar ?? ''), 'the avatar').not.to.match(/\.php$/);
    });
  });
});
