// Screenshots for docs/7.2: options of the Aurelia template, as the administrator of the test site sees them on the forum. The options
// of the template are in a file of the template: the task kunenaTemplateParams of cypress.docs.config.js changes them, and the file is
// put back after the tests. Run it with cypress.docs.config.js.
import { addMessage, forum, lightScheme, open, restoreTemplateOptions, templateOptions } from './helpers.mjs';

describe('Documentation: options of the template', () => {
  const base = '/index.php?option=com_kunena';
  let paramsBefore;
  let perPageBefore;

  before(() => {
    cy.task('kunenaTemplateParamsFile').then((text) => {
      paramsBefore = text;
    });
    cy.kunena_setOption('messagesPerPage', '2').then((old) => {
      perPageBefore = old;
    });
  });

  after(() => {
    restoreTemplateOptions(paramsBefore);
    cy.kunena_setOption('messagesPerPage', perPageBefore ?? null);
  });

  beforeEach(() => {
    lightScheme();
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  it('the buttons to make a section and a category', () => {
    open(`${base}&view=category&layout=list`);
    // The buttons show when the mouse is over the row; for the picture they are shown.
    cy.document().then((doc) => {
      const style = doc.createElement('style');

      style.textContent = '.kcat-new-top .btn { opacity: 1 !important; }';
      doc.head.appendChild(style);
    });
    forum('category-new-buttons', '#kunena', { maxHeight: 560 });
  });

  it('the name of the topic icon under each icon', () => {
    templateOptions({ topicIconLabels: '1' });
    open(`${base}&view=topic&layout=create&catid=3`);
    forum('topic-icon-labels', '#postform', { maxHeight: 330 });
  });

  it('the first post of a topic on every page', () => {
    templateOptions({ stickyFirstPost: '1' });
    ['I agree with this idea.', 'A dark mode would be nice.', 'Please add a way to sort the topics.'].forEach((text) => {
      addMessage(8, 798, 'ci-user', text, 'Re: Feature ideas for the next version');
    });
    open(`${base}&view=topic&layout=default&catid=3&id=8`);
    cy.contains('.pagination a', /^2$/).first().click();
    forum('first-post-on-every-page', ['.kunena-sticky-first', '.topic-item-messages']);
  });
});
