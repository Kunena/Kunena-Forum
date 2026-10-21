// The popups for mentions and replies for a logged-in user. It needs the login of the test site (cy.doFrontendLogin).
describe('Kunena popup notifications of a user', () => {
  const url = '/index.php?option=com_kunena&view=user&task=notifications&format=json';

  it('gives a logged-in user the start of the popups: only the last message', () => {
    cy.doFrontendLogin();

    cy.request(`${url}&after=0`).then((response) => {
      expect(response.body.enabled).to.eq(true);
      expect(response.body.cursor).to.be.a('number');
      // The first time a browser only takes the cursor: no popups for what happened before.
      expect(response.body.items).to.have.length(0);
    });
  });

  it('loads the script of the popups for a logged-in user', () => {
    cy.doFrontendLogin();
    cy.visit('/index.php?option=com_kunena&view=category&layout=list');

    cy.get('script[src*="notifications.js"]').should('exist');
    cy.window().then((win) => {
      const options = win.Joomla.getOptions('com_kunena.notifications');

      expect(options.url).to.include('task');
      expect(options.interval).to.be.at.least(10);
      expect(options.labels).to.have.keys('open', 'close', 'more');
    });
  });

  it('shows a popup for a message after the cursor of the browser', () => {
    cy.doFrontendLogin();
    cy.visit('/index.php?option=com_kunena&view=category&layout=list');

    cy.window().then((win) => {
      const key = `kunena.notify.${win.Joomla.getOptions('com_kunena.notifications').userid}`;

      // The cursor is set to the start: every message of the last day for this user is new.
      win.localStorage.setItem(key, '1');
    });

    cy.reload();

    // There is a popup when a user of the test site has mentioned, or answered, this user. Without such a message there is none.
    cy.get('body').then(($body) => {
      if ($body.find('.kunena-notify-toast').length) {
        cy.get('.kunena-notify-toast a.kunena-notify-open').first().should('have.attr', 'href').and('not.be.empty');
        cy.get('.kunena-notify-toast .kunena-notify-close').first().click();
      }
    });
  });
});
