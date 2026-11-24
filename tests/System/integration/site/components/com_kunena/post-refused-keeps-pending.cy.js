// The uploads of a post are marked active (status 1) when the post is saved. They were marked before the checks of the post: a post
// which was refused (an address in the subject, too many links, spam...) left its uploads active without a message, and the scheduled
// task, which removes the pending uploads (status 0) after a day, never removed them: they stayed on the disk for good.
describe('Kunena post: a refused post leaves its uploads pending', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const file = `refused-${stamp}.txt`;
  const folder = `media/kunena/attachments/cypress-${stamp}`;
  const root = 'E:/www/apache/htdocs/tests';
  const ids = {};
  const old = {};

  beforeEach(() => {
    cy.kunena_setOption('urlSubjectTopic', '1').then((value) => {
      old.urlSubjectTopic = value ?? null;
    });
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.floodProtection = value ?? null;
    });
    // An upload of the member in the form, which waits for its message.
    cy.writeFile(`${root}/${folder}/${file}`, 'x');
    cy.task('queryDB', `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (0, ${user.id}, 0, '', 1, '${folder}', 'text/plain', '${file}', '${file}', '', 0, 0)`).then((result) => {
      ids.attachment = result.insertId;
    });
  });

  afterEach(() => {
    cy.kunena_setOption('urlSubjectTopic', old.urlSubjectTopic);
    cy.then(() => cy.kunena_setOption('floodProtection', old.floodProtection));
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE filename = '${file}'`);
    cy.task('deleteRelativePath', folder);
  });

  it('keeps the upload pending when the post is refused', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=topic&layout=create&catid=3`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: {
          catid: 3,
          subject: `See https://example.com/${stamp}`,
          message: `A topic with a file ${stamp}. [attachment=${ids.attachment}]${file}[/attachment]`,
          [`attachments[${ids.attachment}]`]: 1,
          [`attachment[${ids.attachment}]`]: 1,
          [token]: '1',
        },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_messages WHERE subject = 'See https://example.com/${stamp}'`).then((rows) => {
      expect(Number(rows[0].n), 'the post is refused').to.eq(0);
    });
    cy.task('queryDB', `SELECT mesid, status FROM #__kunena_attachments WHERE id = ${ids.attachment}`).then((rows) => {
      expect([Number(rows[0].mesid), Number(rows[0].status)], 'the upload (message, status)').to.deep.eq([0, 0]);
    });
  });
});
