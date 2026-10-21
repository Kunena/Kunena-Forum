// A private message in the inbox of the plugin Kunena - Inbox: a link in it is a link to that address, and nothing in the text is
// taken for HTML (a quote in an address must not end the attribute). It needs the login of the test site and the plugin; without the
// plugin the spec is skipped.
describe('Kunena inbox: a message with a link', () => {
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  let pluginEnabled = false;

  before(() => {
    cy.task('queryDB', "SELECT enabled FROM #__extensions WHERE type = 'plugin' AND folder = 'kunena' AND element = 'inbox'").then((rows) => {
      pluginEnabled = rows.length > 0 && String(rows[0].enabled) === '1';
    });
  });

  const open = (body) => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_inbox (user_id, sender_id, type, conversation_id, subject, body, created_time) VALUES (${user.id}, 1, 'message', 1, 'Cypress message', '${body.replaceAll("'", "\'")}', UNIX_TIMESTAMP())`,
    );
    cy.doFrontendLogin(user.name, user.password);
    cy.visit('/index.php?option=com_kunena&view=user&layout=default');
    cy.contains('joomla-tab button, .nav-tabs a, .nav-tabs button', /Inbox/i).filter(':visible').first().click();
    cy.contains('[data-open]', 'Cypress message').click();
  };

  beforeEach(function skipWithoutPlugin() {
    if (!pluginEnabled) {
      this.skip();
    }
  });

  it('shows an address in a message as a link to that address', () => {
    open('See https://example.com/page?a=1&b=2 please');

    cy.get('#inbox a[href^="https://example.com"]').should('have.attr', 'href', 'https://example.com/page?a=1&b=2');
  });

  it('does not let a quote in an address add attributes to the link', () => {
    open('https://example.com/x"onmouseover="window.__xss=1"x="');

    cy.get('#inbox a[href^="https://example.com"]').then(($link) => {
      const names = [...$link[0].attributes].map((attribute) => attribute.name);

      expect(names, 'the attributes of the link').to.have.members(['rel', 'target', 'href']);
    });
  });
});
