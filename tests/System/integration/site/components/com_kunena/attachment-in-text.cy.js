// After going back and forward the browser gives the editor its text ([attachment=ID]) back, but not the list of the uploads
// (attachments[ID] / attachment[ID]). The save takes the own uploads which the text names, so they are not deleted as orphans.
describe('Kunena attachments: the uploads named in the text of a message', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress files in text ${stamp}`;
  const folder = 'media/kunena/attachments/798';
  const other = `cypress-other-${stamp}.txt`;
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

  const post = (message, fields = {}) => cy.request({
    method: 'POST',
    url: `${base}&view=topic&task=post`,
    form: true,
    body: { catid: 3, subject, message, ...fields, [token]: '1' },
    failOnStatusCode: false,
    followRedirect: false,
  });

  const newMessageId = () => cy.task('queryDB', `SELECT m.id FROM #__kunena_messages AS m INNER JOIN #__kunena_topics AS t ON t.id = m.thread WHERE t.subject = '${subject}'`)
    .then((rows) => {
      expect(rows, 'the new message').to.have.length(1);

      return Number(rows[0].id);
    });

  beforeEach(() => {
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.floodProtection = value;
    });
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(`${base}&view=topic&layout=create&catid=3`).then((page) => {
      token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];
    });
  });

  afterEach(() => {
    cy.kunena_setOption('floodProtection', old.floodProtection === undefined ? null : old.floodProtection);
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE a FROM #__kunena_attachments AS a INNER JOIN #__kunena_messages AS m ON m.id = a.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', 'DELETE FROM #__kunena_attachments WHERE userid = 798 AND mesid = 0');
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE filename = '${other}'`);
    cy.task('deleteRelativePath', folder);
    cy.kunena_fixUserCounters();
    cy.kunena_fixCategoryCounters();
  });

  it('links an own upload which only the text names', () => {
    upload(`intext-${stamp}.txt`).then((id) => {
      expect(id, 'the upload').to.be.ok;
      post(`A topic with a file. [attachment=${id}]intext.txt[/attachment]`);
      newMessageId().then((mesid) => {
        cy.task('queryDB', `SELECT mesid, status FROM #__kunena_attachments WHERE id = ${id}`).then((rows) => {
          expect(rows, 'the upload').to.have.length(1);
          expect(Number(rows[0].mesid), 'the message of the upload').to.equal(mesid);
          expect(Number(rows[0].status), 'the status of the upload').to.equal(1);
        });
      });
    });
  });

  it('does not take the upload of another user', () => {
    cy.task('queryDB', `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (0, 797, 0, '', 10, 'media/kunena/attachments/cypress-${stamp}', 'text/plain', '${other}', 'other.txt', '', 0, 0)`);
    cy.task('queryDB', `SELECT id FROM #__kunena_attachments WHERE filename = '${other}'`).then((rows) => {
      const { id } = rows[0];

      post(`A topic with a file of somebody else. [attachment=${id}]other.txt[/attachment]`);
      newMessageId();
      cy.task('queryDB', `SELECT mesid FROM #__kunena_attachments WHERE id = ${id}`).then((after) => {
        expect(after, 'the upload of the other user').to.have.length(1);
        expect(Number(after[0].mesid), 'the message of the upload').to.equal(0);
      });
    });
  });

  it('does not link an upload which the form lists unchecked', () => {
    upload(`unchecked-${stamp}.txt`).then((id) => {
      expect(id, 'the upload').to.be.ok;
      post(`A topic without the file. [attachment=${id}]unchecked.txt[/attachment]`, { [`attachments[${id}]`]: '1' });
      newMessageId().then((mesid) => {
        cy.task('queryDB', `SELECT mesid FROM #__kunena_attachments WHERE id = ${id}`).then((rows) => {
          if (rows.length) {
            expect(Number(rows[0].mesid), 'the message of the upload').not.to.equal(mesid);
          }
        });
      });
    });
  });
});
