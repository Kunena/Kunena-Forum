// A file sent with the post itself (the upload without JavaScript: a file field in the form) was stored under its own name with all
// its dots, only lowercased and without spaces: "evil.php.txt" stayed "evil.php.txt" in the folder of the attachments, which the
// site serves when the attachments are not protected. Some web servers run such a file as a script (only the last dot is the
// extension of the upload, the others are not checked). The upload of the editor makes the other dots underscores.
describe('Kunena upload without JavaScript: a name with two extensions', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const subject = `Cypress double extension ${stamp}`;
  const name = `evil${stamp}.php.txt`;
  const root = 'E:/www/apache/htdocs/tests';
  const old = {};

  beforeEach(() => {
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.floodProtection = value ?? null;
    });
    cy.kunena_setOption('attachmentProtection', '0').then((value) => {
      old.attachmentProtection = value ?? null;
    });
  });

  afterEach(() => {
    cy.kunena_setOption('floodProtection', old.floodProtection);
    cy.then(() => cy.kunena_setOption('attachmentProtection', old.attachmentProtection));
    cy.task('queryDB', `SELECT folder, filename FROM #__kunena_attachments WHERE filename_real = '${name}'`).then((rows) => {
      rows.forEach((row) => cy.task('deleteRelativePath', `${row.folder}/${row.filename}`));
    });
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE filename_real = '${name}'`);
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE '${subject}%'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.kunena_fixUserCounters();
  });

  // A post of the logged-in member with one file, sent as the form without its script sends it.
  const postWithFile = (title, content) => {
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    cy.document().then((doc) => {
      const token = [...doc.querySelectorAll('input[type="hidden"][value="1"]')].map((input) => input.name).find((field) => /^[0-9a-f]{32}$/.test(field));

      expect(token, 'the token of the form').to.match(/^[0-9a-f]{32}$/);

      return cy.window().then({ timeout: 60000 }, (win) => {
        const body = new win.FormData();

        body.append('catid', 3);
        body.append('subject', title);
        body.append('message', `A post with a file ${stamp}: ${content}`);
        body.append(token, '1');
        body.append('kattachment-1', new win.Blob([content], { type: 'text/plain' }), name);

        return win.fetch(`${Cypress.config('baseUrl').replace(/\/$/, '')}${base}&view=topic&task=post`, { method: 'POST', body, credentials: 'same-origin', redirect: 'manual' });
      });
    });
  };

  it('stores the file without the inner extension', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    postWithFile(subject, 'Just text.');
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      expect(Number(rows[0].n), 'the topic is posted').to.eq(1);
    });
    cy.task('queryDB', `SELECT a.folder, a.filename FROM #__kunena_attachments AS a WHERE a.filename_real = '${name}'`).then((rows) => {
      expect(rows, 'the file is stored').to.have.length(1);
      expect(rows[0].filename, 'the name on the disk').to.not.include('.php.');
      cy.readFile(`${root}/${rows[0].folder}/${rows[0].filename}`).should('eq', 'Just text.');
    });
  });

  it('gives a second file of the same name a name of its own', () => {
    // The name which is stored was checked against the name of the request: the second file overwrote the first.
    cy.doFrontendLogin(user.name, user.password, false);
    postWithFile(subject, 'The first file.');
    // Another text: the same text again is refused as a duplicate post.
    postWithFile(`${subject} 2`, 'The second file.');
    cy.task('queryDB', `SELECT a.folder, a.filename FROM #__kunena_attachments AS a WHERE a.filename_real = '${name}' ORDER BY a.id`).then((rows) => {
      expect(rows, 'both files are stored').to.have.length(2);
      expect(rows[1].filename, 'the name of the second file').to.not.eq(rows[0].filename);
      cy.readFile(`${root}/${rows[0].folder}/${rows[0].filename}`).should('eq', 'The first file.');
      cy.readFile(`${root}/${rows[1].folder}/${rows[1].filename}`).should('eq', 'The second file.');
    });
  });
});
