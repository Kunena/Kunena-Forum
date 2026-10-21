// A logged-in user writes a topic and a reply with a link. It needs the login of the test site (cy.doFrontendLogin).
describe('Kunena topic posting', () => {
  const stamp = Date.now();
  const subject = `Cypress topic ${stamp}`;

  // The editor of the form keeps the text in its own frame: the text is set through the editor.
  const writeMessage = (text) => {
    cy.get('#postform textarea[name="message"]').should('exist');
    cy.window().should('have.property', 'sceditor');
    cy.window().then((win) => {
      const textarea = win.document.querySelector('#postform textarea[name="message"]');
      const editor = win.sceditor.instance(textarea);

      editor.val(text);
      editor.focus();
      editor.insertText(' ');
    });
    cy.get('#form_submit_button').should('not.be.disabled');
  };

  it('writes a topic', () => {
    cy.doFrontendLogin();

    cy.task(
      'queryDB',
      'SELECT c.id FROM #__kunena_categories AS c WHERE c.published = 1 AND c.pubAccess = 1 AND c.locked = 0 AND c.parent_id > 0 ORDER BY c.id ASC LIMIT 1',
    ).then((categories) => {
      expect(categories, 'a published forum category is available').not.to.be.empty;

      cy.visit(`/index.php?option=com_kunena&view=topic&layout=create&catid=${categories[0].id}`);

      cy.get('#postform input[name="subject"]').type(subject, { parseSpecialCharSequences: false });
      writeMessage('The text of the first message.');
      cy.get('#form_submit_button').click();

      cy.get('.kunena-topic-item > h1').should('contain.text', subject);
      cy.contains('The text of the first message.').should('be.visible');
    });
  });

  it('writes a reply with a link', () => {
    cy.doFrontendLogin();

    cy.task(
      'queryDB',
      `SELECT t.id, t.category_id FROM #__kunena_topics AS t WHERE t.subject = '${subject}' LIMIT 1`,
    ).then((topics) => {
      expect(topics, 'the topic of the first test is there').not.to.be.empty;

      cy.visit(`/index.php?option=com_kunena&view=topic&layout=reply&catid=${topics[0].category_id}&id=${topics[0].id}`);

      writeMessage('A reply with a link: https://example.com/');
      cy.get('#form_submit_button').click();

      cy.contains('A reply with a link').should('be.visible');

      // The link is a link, or a card (the card needs the page of the other site).
      cy.get('.topic-item-messages a[href^="https://example.com"]').should('exist');
    });
  });
});
