// The page of the link to unsubscribe in the e-mail of a followed member (a new topic of a member whom the user follows) named that
// member escaped twice: "Tom & Jerry" read as "Tom &amp; Jerry" (the name was HTML, the layout escaped it again).
describe('Kunena e-mail: the unsubscribe page of a followed member', () => {
  const stamp = Date.now();
  const followed = 798;
  const follower = 797;
  const realName = `Tom & Jerry ${stamp}`;
  const old = {};
  let secret;

  const sign = (type, id, uid) => {
    const encoder = new TextEncoder();

    return crypto.subtle.importKey('raw', encoder.encode(secret), { name: 'HMAC', hash: 'SHA-256' }, false, ['sign'])
      .then((key) => crypto.subtle.sign('HMAC', key, encoder.encode(`kunena-unsubscribe|${type}|${id}|${uid}`)))
      .then((buffer) => [...new Uint8Array(buffer)].map((byte) => byte.toString(16).padStart(2, '0')).join(''));
  };

  before(() => {
    cy.readFile('E:/www/apache/htdocs/tests/configuration.php').then((text) => {
      secret = /public \$secret = '([^']+)'/.exec(text)[1];
    });
    cy.kunena_setOption('username', '0').then((value) => {
      old.username = value ?? null;
    });
    cy.task('queryDB', `SELECT name FROM #__users WHERE id = ${followed}`).then((rows) => {
      old.name = rows[0].name;
    });
    cy.task('queryDB', `UPDATE #__users SET name = '${realName}' WHERE id = ${followed}`);
  });

  after(() => {
    cy.kunena_setOption('username', old.username);
    cy.then(() => cy.task('queryDB', `UPDATE #__users SET name = '${old.name}' WHERE id = ${followed}`));
  });

  it('names the member once escaped', () => {
    cy.then(() => cy.wrap(sign('follow', followed, follower))).then((sig) => {
      cy.request(`/index.php?option=com_kunena&view=topic&layout=unsubscribe&type=follow&id=${followed}&uid=${follower}&sig=${sig}`).then((page) => {
        const body = String(page.body);

        expect(body, 'the name of the member').to.include(`Tom &amp; Jerry ${stamp}`);
        expect(body, 'a name escaped twice').not.to.include('Tom &amp;amp; Jerry');
      });
    });
  });
});
