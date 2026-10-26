// A draft keeps at most 200000 characters of text (more than any message): a user has up to 50 drafts and the text was kept twice
// in each one without a limit, so one user could fill the database with them (the drafts have no flood protection).
describe('Kunena drafts: the size of a draft', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress big draft ${stamp}`;

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_drafts WHERE subject = '${subject}'`);
  });

  it('keeps no more than 200000 characters of the text', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(`${base}&view=topic&layout=create&catid=3`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=savedraft`,
        form: true,
        body: { catid: 3, subject, message: 'a'.repeat(300000), [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });

    cy.task('queryDB', `SELECT CHAR_LENGTH(message) AS text, CHAR_LENGTH(fields) AS fields FROM #__kunena_drafts WHERE subject = '${subject}'`).then((rows) => {
      expect(rows, 'the draft').to.have.length(1);
      expect(Number(rows[0].text), 'the text').to.be.at.most(200000);
      expect(Number(rows[0].fields), 'the fields (with the text)').to.be.at.most(201000);
    });
  });
});
