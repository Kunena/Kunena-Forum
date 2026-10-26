// The files which a user uploads in the editor wait for his message (mesid 0) until the task of the site removes those which were
// never posted. Their number has a limit (twice the files of a message, 50 when the files of a message have no limit): a user
// uploaded file after file without posting, as many as he wanted, and filled the disk of the server.
describe('Kunena upload: the files which wait for a message', () => {
  const base = '/index.php?option=com_kunena';
  const folder = 'media/kunena/attachments/798';
  const old = {};
  let token;

  const upload = (filename) => cy.request({
    method: 'POST',
    url: `${base}&view=topic&task=upload&format=json&${token}=1`,
    qs: { catid: 3, filename, size: 1, mime: 'text/plain', hash: 'abc', chunkStart: 0, chunkEnd: 1 },
    headers: { 'Content-Type': 'application/octet-stream' },
    body: 'x',
    failOnStatusCode: false,
  }).its('body');
  const pending = () => cy.task('queryDB', 'SELECT COUNT(*) AS n FROM #__kunena_attachments WHERE userid = 798 AND mesid = 0').then((rows) => Number(rows[0].n));

  beforeEach(() => {
    cy.task('queryDB', 'DELETE FROM #__kunena_attachments WHERE userid = 798 AND mesid = 0');
    cy.kunena_setOption('attachmentLimit', '1').then((value) => {
      old.attachmentLimit = value ?? null;
    });
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(`${base}&view=topic&layout=create&catid=3`).then((page) => {
      token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];
    });
  });

  afterEach(() => {
    cy.kunena_setOption('attachmentLimit', old.attachmentLimit);
    cy.task('queryDB', 'DELETE FROM #__kunena_attachments WHERE userid = 798 AND mesid = 0');
    cy.task('deleteRelativePath', folder);
  });

  it('keeps no more waiting files of a user than twice the files of a message', () => {
    upload('one.txt').its('success').should('eq', true);
    upload('two.txt').its('success').should('eq', true);
    upload('three.txt').its('success').should('eq', false);
    pending().should('eq', 2);
  });
});
