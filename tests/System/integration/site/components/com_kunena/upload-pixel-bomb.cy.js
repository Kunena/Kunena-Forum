// A picture which is small on disk but has very many pixels (a 1-bit PNG of 30000 x 30000 is 107 KB, and about 900 MB when the
// server decodes it): the upload ended in a fatal error of PHP (memory exhausted) when the picture was made smaller. The forum now
// refuses a picture whose pixels do not fit in the memory of PHP, before it decodes it.
describe('Kunena upload: a picture with too many pixels', () => {
  const base = '/index.php?option=com_kunena';
  const folder = 'media/kunena/attachments/798';

  afterEach(() => {
    cy.task('queryDB', 'DELETE FROM #__kunena_attachments WHERE userid = 798 AND mesid = 0');
    cy.task('deleteRelativePath', folder);
  });

  it('refuses the picture without an error of the server, and keeps no file of it', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    // The page of the editor: the bytes of the picture go from it, like the uploader of the editor sends them (a request of
    // Cypress does not send the bytes of a file as they are).
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    cy.task('pixelBomb', 30000).then((base64) => cy.document().then((doc) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(doc.documentElement.innerHTML)[1];

      return cy.window().then((win) => {
        const bytes = win.Uint8Array.from(win.atob(base64), (c) => c.charCodeAt(0));
        const query = new win.URLSearchParams({ catid: 3, filename: 'bomb.png', size: bytes.length, mime: 'image/png', hash: 'abc', chunkStart: 0, chunkEnd: bytes.length });

        return cy.wrap(win.fetch(`/tests${base}&view=topic&task=upload&format=json&${token}=1&${query}`, {
          method: 'POST',
          body: new win.Blob([bytes], { type: 'application/octet-stream' }),
          credentials: 'same-origin',
        }).then(async (response) => ({ status: response.status, text: await response.text() })), { timeout: 60000 });
      });
    })).then((response) => {
      expect(response.status, 'the status').to.be.lessThan(500);
      expect(response.text, 'no fatal error').not.to.match(/Allowed memory size|Fatal error/);
      expect(JSON.parse(response.text).success, 'the upload').to.eq(false);
    });
    cy.task('queryDB', 'SELECT id FROM #__kunena_attachments WHERE userid = 798 AND mesid = 0').should('have.length', 0);
  });
});
