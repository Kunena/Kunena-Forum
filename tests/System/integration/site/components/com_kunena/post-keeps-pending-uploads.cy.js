// After a post or an edit, the uploads of the user which no message has (mesid 0) were deleted, all of them: also the files which
// he had just uploaded in another form (another tab, another topic) and had not posted yet, which were then missing from that post.
// For a guest (user 0) the uploads of every guest were deleted. The scheduled task leaves a pending upload alone for a day
// (KunenaPendingAttachment), and so does the post now.
describe('Kunena post: the pending uploads of another form', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const root = 'E:/www/apache/htdocs/tests';
  const folder = `media/kunena/attachments/cypress-pending-${stamp}`;
  const file = `pending-${stamp}.txt`;
  const subject = `Cypress pending uploads ${stamp}`;
  const ids = {};
  const old = {};

  beforeEach(() => {
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.floodProtection = value ?? null;
    });
    // A file which the member uploaded a minute ago in another form.
    cy.writeFile(`${root}/${folder}/${file}`, 'An upload of another form.');
    cy.task('queryDB', `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (0, ${user.id}, 0, '', 26, '${folder}', 'text/plain', '${file}', '${file}', '', 0, 0)`).then((result) => {
      ids.attachment = result.insertId;
    });
  });

  afterEach(() => {
    cy.kunena_setOption('floodProtection', old.floodProtection);
    cy.task('deleteRelativePath', folder);
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE filename = '${file}'`);
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

  it('keeps an upload of another form when the member posts', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=topic&layout=create&catid=3`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: { catid: 3, subject, message: `A topic without files ${stamp}.`, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      expect(Number(rows[0].n), 'the topic is posted').to.eq(1);
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_attachments WHERE id = ${ids.attachment}`).then((rows) => {
      expect(Number(rows[0].n), 'the upload of the other form').to.eq(1);
    });
    cy.readFile(`${root}/${folder}/${file}`).should('eq', 'An upload of another form.');
  });
});
