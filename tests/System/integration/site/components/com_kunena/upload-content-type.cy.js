// A file is served by the forum (view=attachment) with the type of its content. A file with an allowed name (x.txt) and the
// content of an SVG picture or of an HTML page must not be served as a picture or a page shown in the browser: a script in it
// would run on the address of the forum (stored XSS through a link to the file).
describe('Kunena upload: the type of the content of a file', () => {
  const base = '/index.php?option=com_kunena';
  const folder = 'media/kunena/attachments/798';
  let token;

  const upload = (filename, mime, content) => {
    const size = Buffer.byteLength(content);

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=upload&format=json&${token}=1`,
      qs: { catid: 3, filename, size, mime, hash: 'abc', chunkStart: 0, chunkEnd: size },
      headers: { 'Content-Type': 'application/octet-stream' },
      body: content,
      failOnStatusCode: false,
    }).its('body');
  };

  beforeEach(() => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(`${base}&view=topic&layout=create&catid=3`).then((page) => {
      token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];
    });
  });

  afterEach(() => {
    cy.task('queryDB', 'DELETE FROM #__kunena_attachments WHERE userid = 798 AND mesid = 0');
    cy.task('deleteRelativePath', folder);
  });

  [
    ['an SVG picture with a script', 'picture.txt', '<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"><script>alert(document.domain)</script></svg>'],
    ['an HTML page with a script', 'page.txt', '<!DOCTYPE html><html><head><title>x</title></head><body><script>alert(document.domain)</script></body></html>'],
  ].forEach(([what, name, content]) => {
    it(`does not show ${what} in the browser`, () => {
      upload(name, 'text/plain', content).then((body) => {
        if (!body.success) {
          // Refused: nothing is stored, nothing can be shown.
          return;
        }

        cy.task('queryDB', 'SELECT id, filetype FROM #__kunena_attachments WHERE userid = 798 AND mesid = 0').then((rows) => {
          expect(rows, 'the stored file').to.have.length(1);

          cy.request({ url: `${base}&view=attachment&id=${rows[0].id}&format=raw`, failOnStatusCode: false }).then((response) => {
            const type = String(response.headers['content-type'] ?? '');
            const disposition = String(response.headers['content-disposition'] ?? '');

            const answer = `${rows[0].filetype} -> ${type} | ${disposition}`;

            expect(!/svg|html|xml/i.test(type) || /^attachment/i.test(disposition), `the answer for the file (${answer})`).to.eq(true);
          });
        });
      });
    });
  });
});
