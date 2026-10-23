// The files of the users: the name which the forum gives to an uploaded file must stay in the folder of the user, must not be a name
// that a web server runs as a script (x.php, x.php.txt), and a very long name must not break the upload. The files are sent like the
// uploader of the editor sends them (the body of the request is the file, the name and the type are in the address).
describe('Kunena upload: the names of the files', () => {
  const base = '/index.php?option=com_kunena';
  const folder = 'media/kunena/attachments/798';
  let token;

  const upload = (filename, mime, content, extra = {}) => {
    const size = Buffer.byteLength(content);

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=upload&format=json&${token}=1`,
      qs: { catid: 3, filename, size, mime, hash: 'abc', chunkStart: 0, chunkEnd: size, ...extra },
      headers: { 'Content-Type': 'application/octet-stream' },
      body: content,
      failOnStatusCode: false,
    }).its('body');
  };

  const storedNames = () => cy.task('queryDB', 'SELECT filename, filename_real, folder FROM #__kunena_attachments WHERE userid = 798 AND mesid = 0').then((rows) => rows);

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

  it('takes an upload with the number of a message (the upload while a message is edited)', () => {
    // Message 10 is a message of the user in the topic 9 of the test site. The check of the rights used an action which
    // does not exist, and every upload with a message gave a server error.
    upload('edited.txt', 'text/plain', 'x', { mesid: 10 }).its('success').should('eq', true);
  });

  it('keeps a name with directories in the folder of the user', () => {
    ['../../evil.txt', '..\\..\\evil2.txt', 'a/b/c.txt'].forEach((name) => upload(name, 'text/plain', 'x').its('success').should('eq', true));

    storedNames().then((rows) => {
      expect(rows).to.have.length(3);
      rows.forEach((row) => {
        expect(row.folder, 'the folder').to.eq(folder);
        expect(row.filename, 'the stored name').to.match(/^[A-Za-z0-9_.-]+$/);
      });
    });
    // No file outside the folder of the user.
    ['/evil.txt', '/evil2.txt', '/media/evil.txt', '/media/kunena/evil.txt', '/media/kunena/attachments/evil.txt'].forEach((path) => {
      cy.request({ url: path, failOnStatusCode: false }).its('status').should('be.gte', 400);
    });
  });

  it('refuses the names of scripts and of the settings of the web server', () => {
    ['x.php', 'x.txt.php', 'x.phtml', 'x.PhP', '.htaccess', 'x.html', 'x.svg', 'x.txt::$DATA', 'x.txt.', 'x'].forEach((name) => upload(name, 'text/plain', '<?php echo 1; ?>').its('success').should('eq', false));

    storedNames().then((rows) => expect(rows, 'no attachment of the refused names').to.have.length(0));
  });

  it('does not keep a dot of a script in the middle of the name', () => {
    upload('x.php.txt', 'text/plain', '<?php echo 1; ?>').its('success').should('eq', true);
    upload('archive.v2.final.txt', 'text/plain', 'x').its('success').should('eq', true);

    storedNames().then((rows) => {
      expect(rows).to.have.length(2);
      rows.forEach((row) => expect(row.filename.replace(/\.txt$/, ''), `the name without the extension (${row.filename})`).to.not.include('.'));
    });
  });

  it('shortens a name which is too long, and keeps the extension', () => {
    upload(`${'a'.repeat(300)}.txt`, 'text/plain', 'x').its('success').should('eq', true);

    storedNames().then((rows) => {
      expect(rows).to.have.length(1);
      expect(rows[0].filename.length, 'the length of the stored name').to.be.lessThan(140);
      expect(rows[0].filename).to.match(/\.txt$/);
    });
  });

  it('accepts a name with spaces and characters of other languages', () => {
    upload('spaces and ünïcode ☃.txt', 'text/plain', 'x').its('success').should('eq', true);
    upload('<img src=x onerror=__xss=1>.txt', 'text/plain', 'x').its('success').should('eq', false);
  });
});
