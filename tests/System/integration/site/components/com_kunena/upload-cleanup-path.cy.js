// When an upload fails, the uploader removes its temporary file. It removed the file whose name the request gave instead (the name
// of the upload, which is a path from the root of the site), so a member who may attach files could delete a file of the site, for
// example the attachment of another user, by sending a failing upload with that path as its name.
describe('Kunena upload: a failed upload removes no file of the site', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const root = 'E:/www/apache/htdocs/tests';
  const sentinel = `tmp/cypress-sentinel-${stamp}.txt`;

  afterEach(() => {
    cy.task('deleteRelativePath', sentinel);
  });

  it('keeps the file which the name of a failed upload points to', () => {
    cy.writeFile(`${root}/${sentinel}`, 'A file of the site.');
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=topic&layout=reply&catid=3&id=9`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      // A part which ends after the size of the file: the upload fails at once.
      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=upload&format=json&${token}=1`,
        qs: { catid: 3, filename: sentinel, size: 1, mime: 'text/plain', hash: 'abc', chunkStart: 0, chunkEnd: 5 },
        headers: { 'Content-Type': 'application/octet-stream' },
        body: 'x',
        failOnStatusCode: false,
      });
    });
    cy.readFile(`${root}/${sentinel}`).should('eq', 'A file of the site.');
  });
});
