/**
 * The editor of the forms of Kunena (CKEditor 5 with the BBCode of Kunena), through its API (window.KunenaEditor, built from
 * editor/ckeditor5): the specs do not depend on the HTML of CKEditor. The commands start with "editor_".
 *
 *   cy.editor_ready(id)              waits until the template uses CKEditor and the editor of the textarea is ready
 *   cy.editor_instance(id)           the editor (getData, setData, insertText, removeText, updateElement, editor...)
 *   cy.editor_data(id)               the BBCode of the editor
 *   cy.editor_posted(id)             the value which the form sends (the message as it was written until the text is changed)
 *   cy.editor_source(on, id)         shows the source (the BBCode as text) or the formatted text again
 *   cy.editor_editable(id)           the element of the formatted text
 *   cy.editor_selectAll(id)          selects the whole text
 *   cy.editor_execute(command, ...)  runs a command of CKEditor on the selection (bold, subscript, link...)
 *   cy.editor_button(name, id)       a button of the toolbar: a button of Kunena by its name (spoiler, polls...), else by its label
 */

const instance = (win, id) => (win.KunenaEditor ? win.KunenaEditor.get(id) : null);

Cypress.Commands.add('editor_ready', (id = 'message') => cy.window().should((win) => {
  expect(win.Joomla.getOptions('com_kunena.editor_type'), 'the editor of the template').to.eq('ckeditor');
  expect(instance(win, id), `the editor of #${id}`).to.not.equal(null);
}).wait(100));

Cypress.Commands.add('editor_instance', (id = 'message') => cy.window().then((win) => instance(win, id)));

Cypress.Commands.add('editor_data', (id = 'message') => cy.window().then((win) => instance(win, id).getData()));

Cypress.Commands.add('editor_posted', (id = 'message') => cy.window().then((win) => instance(win, id).updateElement()));

Cypress.Commands.add('editor_source', (on, id = 'message') => cy.window().then((win) => {
  instance(win, id).setSourceMode(on);
}));

Cypress.Commands.add('editor_editable', (id = 'message') => cy.window().then((win) => Cypress.$(instance(win, id).editor.ui.getEditableElement())));

Cypress.Commands.add('editor_selectAll', (id = 'message') => cy.window().then((win) => {
  const { editor } = instance(win, id);

  editor.editing.view.focus();
  editor.model.change((writer) => writer.setSelection(editor.model.document.getRoot(), 'in'));
}));

Cypress.Commands.add('editor_execute', (command, ...args) => cy.window().then((win) => {
  instance(win, 'message').editor.execute(command, ...args);
}));

Cypress.Commands.add('editor_button', (name, id = 'message') => cy.window().then((win) => {
  const root = instance(win, id).editor.ui.view.element;
  const kunena = root.querySelector(`[data-kunena-button="${name}"]`);

  if (kunena) {
    return Cypress.$(kunena);
  }

  // A button of CKEditor: by its label (the tooltip of the button), as in English.
  return Cypress.$([...root.querySelectorAll('.ck-toolbar button')].filter((button) => button.getAttribute('data-cke-tooltip-text')?.startsWith(name) || button.querySelector('.ck-button__label')?.textContent === name));
}));
