// Enter in a field of the posting form sends the form with its first submit button. The hidden buttons Insert All, Remove All and
// Set all private were submit buttons before the button Submit: Enter in the subject clicked Insert All, which put every uploaded file
// into the message (and marked the files as in the text). They are plain buttons now, and Enter sends the form with Submit. The
// submit is stopped in the page, so nothing is posted; the rows and the files of the upload are removed after the test.
describe('Kunena posting form: Enter in the subject with uploaded files', () => {
  const create = '/index.php?option=com_kunena&view=topic&layout=create&catid=3';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const folder = `media/kunena/attachments/${user.id}`;

  // A small PNG, drawn in the page.
  const png = () => cy.window().then((win) => new Cypress.Promise((resolve) => {
    const canvas = win.document.createElement('canvas');

    canvas.width = 40;
    canvas.height = 30;
    canvas.getContext('2d').fillRect(0, 0, 20, 30);
    canvas.toBlob((blob) => blob.arrayBuffer().then((buffer) => resolve(Cypress.Buffer.from(buffer))), 'image/png');
  }));

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE userid = ${user.id} AND mesid = 0`);
    cy.task('deleteRelativePath', folder);
  });

  it('sends the form with Submit and inserts no file', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(create);
    cy.window().should('have.property', 'KunenaUploader');
    cy.intercept({ method: 'POST', url: /upload/ }).as('upload');

    png().then((contents) => {
      cy.get('#fileupload').selectFile({ contents, fileName: `cypress-enter-${stamp}.png`, mimeType: 'image/png' }, { force: true });
    });
    cy.wait('@upload').its('response.body.success').should('eq', true);
    cy.get('input[name^="attachments["]').should('have.length', 1);

    // The submit of the form is recorded and stopped: the test posts nothing.
    cy.get('#postform').then(($form) => {
      $form[0].addEventListener('submit', (event) => {
        event.preventDefault();
        $form[0].dataset.cypressSubmitter = event.submitter ? (event.submitter.id || event.submitter.textContent.trim()) : 'none';
      });
    });

    cy.get('#postform input[name="subject"]').type(`Cypress Enter ${stamp}{enter}`);

    cy.get('#postform').should('have.attr', 'data-cypress-submitter', 'form_submit_button');
    cy.window().then((win) => {
      const editor = win.KunenaEditor ? win.KunenaEditor.get('message') : null;
      const text = editor ? editor.getData() : win.document.getElementById('message').value;

      expect(text, 'the message').to.not.contain('[attachment=');
    });
    cy.get('#insert-all').should('not.have.class', 'btn-success');
  });
});
