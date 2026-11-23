// A file sent with the post itself (the upload without JavaScript: a file field in the form, also what a browser without scripts
// sends). Without the protection of the attachments (the default) the post failed with the error of the database "Incorrect integer
// value: '' for column 'protected'": the protection was stored as false, which the query gives as ''.
describe('Kunena upload without JavaScript: a post with a file', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const subject = `Cypress upload without script ${stamp}`;
  const name = `notes${stamp}.txt`;
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
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
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

  it('posts the topic with its file when the attachments are not protected', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    cy.document().then((doc) => {
      const token = [...doc.querySelectorAll('input[type="hidden"][value="1"]')].map((input) => input.name).find((field) => /^[0-9a-f]{32}$/.test(field));

      expect(token, 'the token of the form').to.match(/^[0-9a-f]{32}$/);

      return cy.window().then({ timeout: 60000 }, (win) => {
        const body = new win.FormData();

        body.append('catid', 3);
        body.append('subject', subject);
        body.append('message', `A post with a file ${stamp}.`);
        body.append(token, '1');
        body.append('kattachment-1', new win.Blob(['Just text.'], { type: 'text/plain' }), name);

        return win.fetch(`${Cypress.config('baseUrl').replace(/\/$/, '')}${base}&view=topic&task=post`, { method: 'POST', body, credentials: 'same-origin', redirect: 'manual' });
      });
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      expect(Number(rows[0].n), 'the topic is posted').to.eq(1);
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      expect(Number(rows[0].n), 'the topic is posted').to.eq(1);
    });
    cy.task('queryDB', `SELECT a.folder, a.filename FROM #__kunena_attachments AS a WHERE a.filename_real = '${name}'`).then((rows) => {
      expect(rows, 'the file is stored').to.have.length(1);
      cy.readFile(`${root}/${rows[0].folder}/${rows[0].filename}`).should('eq', 'Just text.');
    });
  });
});
