// The number of the files of a message is limited by the option attachmentLimit (the uploader of the editor stops there). A request
// which puts more uploads into a new message must not make a message with more files.
describe('Kunena attachments: the maximum number of files of a message', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress files limit ${stamp}`;
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
  }).its('body.data.id');

  beforeEach(() => {
    cy.kunena_setOption('attachmentLimit', '3').then((value) => {
      old.attachmentLimit = value;
    });
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.floodProtection = value;
    });
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(`${base}&view=topic&layout=create&catid=3`).then((page) => {
      token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];
    });
  });

  afterEach(() => {
    ['attachmentLimit', 'floodProtection'].forEach((name) => cy.kunena_setOption(name, old[name] === undefined ? null : old[name]));
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE a FROM #__kunena_attachments AS a INNER JOIN #__kunena_messages AS m ON m.id = a.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', 'DELETE FROM #__kunena_attachments WHERE userid = 798 AND mesid = 0');
    cy.task('deleteRelativePath', folder);
    cy.kunena_fixUserCounters();
    cy.kunena_fixCategoryCounters();
  });

  it('keeps no more files in a new message than the maximum', () => {
    const ids = [];

    [1, 2, 3, 4, 5].forEach((i) => upload(`limit${i}.txt`).then((id) => ids.push(id)));
    cy.then(() => {
      expect(ids.filter(Boolean), 'the uploads').to.have.length(5);
      const files = {};

      ids.forEach((id) => {
        files[`attachments[${id}]`] = '1';
        files[`attachment[${id}]`] = '1';
      });

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: { catid: 3, subject, message: 'A topic with files.', ...files, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_attachments AS a INNER JOIN #__kunena_messages AS m ON m.id = a.mesid INNER JOIN #__kunena_topics AS t ON t.id = m.thread WHERE t.subject = '${subject}'`).then((rows) => {
      expect(Number(rows[0].n), 'the files of the message').to.be.within(1, 3);
    });
  });
});
