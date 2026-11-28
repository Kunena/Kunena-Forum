// A [video] of a message is shown in SCEditor as the player of its provider (an iframe). The editor keeps only the iframes of the
// addresses which it allows: the players of the forum were not among them (only one address of YouTube, with https:, which the
// player of the forum does not start with), so the iframe was removed and the [video] was gone from the message at its next save.
describe('Kunena SCEditor: a video', () => {
  // The form is shown with SCEditor: the template Aurelia with SCEditor, whatever the test site runs, put back afterwards.
  before(() => cy.editor_useSceditor());
  after(() => cy.editor_restore());

  const base = '/index.php?option=com_kunena';

  beforeEach(() => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  });

  [
    ['youtube', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
    ['vimeo', 'https://vimeo.com/76979871'],
    ['dailymotion', 'https://www.dailymotion.com/video/x7tgad0'],
  ].forEach(([provider, address]) => {
    it(`keeps a video of ${provider} in the text of the message`, () => {
      const bbcode = `Before [video=${provider}]${address}[/video] after`;

      cy.visit(`${base}&view=topic&layout=create&catid=3`);
      cy.window().should('have.property', 'sceditor');
      cy.get('#postform .sceditor-container iframe').should('exist');
      cy.window().then((win) => {
        const instance = win.sceditor.instance(win.document.querySelector('#postform textarea[name="message"]'));

        // The text is shown in the editor (the player of the video) and taken back as BBCode, as on the submit of the form.
        instance.val(bbcode);
        expect(instance.inSourceMode(), 'the editor shows the formatted text').to.equal(false);
        expect(instance.val().trim()).to.equal(bbcode);
      });
    });
  });
});
