// The files of the posting form count against the option attachmentLimit (the uploader stops there). A file which the server refuses
// (a type which is not allowed) is no file of the message: it counted all the same, so with a limit of one file the user could not
// upload the file he wanted after a refused one ("too many files"). The rows and the files of the upload are removed after the test.
describe('Kunena posting form: a refused upload does not count as a file of the message', () => {
  const create = '/index.php?option=com_kunena&view=topic&layout=create&catid=3';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const folder = `media/kunena/attachments/${user.id}`;
  const old = {};

  // A small PNG, drawn in the page.
  const png = () => cy.window().then((win) => new Cypress.Promise((resolve) => {
    const canvas = win.document.createElement('canvas');

    canvas.width = 40;
    canvas.height = 30;
    canvas.getContext('2d').fillRect(0, 0, 20, 30);
    canvas.toBlob((blob) => blob.arrayBuffer().then((buffer) => resolve(Cypress.Buffer.from(buffer))), 'image/png');
  }));

  beforeEach(() => {
    cy.kunena_setOption('attachmentLimit', '1').then((value) => {
      old.attachmentLimit = value;
    });
  });

  afterEach(() => {
    cy.kunena_setOption('attachmentLimit', old.attachmentLimit === undefined ? null : old.attachmentLimit);
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE userid = ${user.id} AND mesid = 0`);
    cy.task('deleteRelativePath', folder);
  });

  it('uploads a file after a refused one with a limit of one file', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(create);
    cy.window().should('have.property', 'KunenaUploader');
    cy.intercept({ method: 'POST', url: /upload/ }).as('upload');

    // A program is no allowed type: the server refuses it.
    cy.get('#fileupload').selectFile({ contents: Cypress.Buffer.from('MZ'), fileName: `cypress-refused-${stamp}.exe`, mimeType: 'application/octet-stream' }, { force: true });
    cy.wait('@upload').its('response.body.success').should('not.eq', true);

    png().then((contents) => {
      cy.get('#fileupload').selectFile({ contents, fileName: `cypress-count-${stamp}.png`, mimeType: 'image/png' }, { force: true });
    });
    cy.wait('@upload').its('response.body.success').should('eq', true);
    cy.get('#alert_max_file').should('not.exist');
    cy.get('input[name^="attachments["]').should('have.length', 1);
  });
});
