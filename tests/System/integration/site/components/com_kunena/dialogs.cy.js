// The dialogs of the forum are native <dialog> elements (dialog.js), not the modals of Bootstrap: its modal script is not loaded.
// A modal (the report of a message, the status message of the user) is opened by a button with an invoker command, the browser
// makes it modal and gives the focus back to the button when it closes; the quick reply is a panel of the page (not modal), whose
// button tells whether it is open. The spec only reads the database: it opens topics which already exist.
describe('Kunena dialogs: native dialogs instead of the modals of Bootstrap', () => {
  const base = '/index.php?option=com_kunena';

  // A topic of the administrator (797), which the user may report and answer.
  const topicOfAdmin = () => cy.task(
    'queryDB',
    'SELECT t.id, t.category_id, t.first_post_id FROM #__kunena_topics AS t INNER JOIN #__kunena_categories AS c ON c.id = t.category_id WHERE t.hold = 0 AND t.moved_id = 0 AND t.locked = 0 AND c.published = 1 AND c.locked = 0 AND t.first_post_userid = 797 ORDER BY t.id LIMIT 1',
  ).then((rows) => {
    expect(rows.length, 'a topic of the administrator').to.equal(1);

    return rows[0];
  });

  it('does not load the modal script of Bootstrap', () => {
    topicOfAdmin().then((topic) => {
      cy.request(`${base}&view=topic&catid=${topic.category_id}&id=${topic.id}`).its('body').then((body) => {
        expect(body, 'the modal script of Bootstrap').to.not.match(/bootstrap\/js\/modal(\.min)?\.js/);
        expect(body, 'the dialogs of the forum').to.match(/<script[^>]*core\/js\/dialog(-min)?\.js[^>]*defer/);
        expect(body, 'no modal of Bootstrap').to.not.contain('class="modal fade');
      });
    });
  });

  describe('as a user', () => {
    beforeEach(() => {
      cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    });

    it('reports a message in a modal dialog, which gives the focus back when it closes', () => {
      topicOfAdmin().then((topic) => {
        cy.visit(`${base}&view=topic&catid=${topic.category_id}&id=${topic.id}`);
        cy.location('href').as('page');

        cy.get(`button[command="show-modal"][commandfor="report${topic.first_post_id}"]`).filter(':visible').first().as('report');
        cy.get('@report').click();
        cy.get(`#report${topic.first_post_id}`).should('have.attr', 'open');
        cy.get(`#report${topic.first_post_id}`).should(($dialog) => {
          expect($dialog[0].matches(':modal'), 'a modal dialog').to.eq(true);
          expect($dialog.attr('aria-labelledby'), 'its name').to.eq(`reportModalLabel${topic.first_post_id}`);
        });
        cy.get(`#report${topic.first_post_id} textarea[name="text"]`).should('be.visible');
        cy.screenshot('dialog-report', { capture: 'viewport' });

        // Cancel closes the dialog without sending the report.
        cy.get(`#report${topic.first_post_id} button[formmethod="dialog"]`).click();
        cy.get(`#report${topic.first_post_id}`).should('not.have.attr', 'open');
        cy.get('@page').then((page) => cy.location('href').should('eq', page));
        cy.focused().should('have.attr', 'commandfor', `report${topic.first_post_id}`);

        // The close button of the header too.
        cy.get('@report').click();
        cy.get(`#report${topic.first_post_id} .btn-close[command="close"]`).click();
        cy.get(`#report${topic.first_post_id}`).should('not.have.attr', 'open');
      });
    });

    it('opens the quick reply as a panel of the page', () => {
      topicOfAdmin().then((topic) => {
        const id = `kreply${topic.first_post_id}_form`;

        cy.visit(`${base}&view=topic&catid=${topic.category_id}&id=${topic.id}`);
        cy.get(`[data-kunena-dialog-toggle="${id}"]`).filter(':visible').first().as('toggle');
        cy.get('@toggle').should('have.attr', 'aria-expanded', 'false').and('have.attr', 'aria-controls', id);
        cy.get('@toggle').click();
        cy.get(`#${id}`).should('have.attr', 'open');
        cy.get(`#${id}`).should(($dialog) => {
          expect($dialog[0].matches(':modal'), 'not modal: the page stays usable').to.eq(false);
        });
        cy.get('@toggle').should('have.attr', 'aria-expanded', 'true');
        cy.focused().should('match', `#${id} textarea, #${id} input`);
        cy.screenshot('dialog-quickreply', { capture: 'viewport' });

        // Cancel empties the form and closes the panel.
        cy.get(`#${id} textarea[name="message"]`).type('Cypress dialog text');
        cy.get(`#${id} input[type="reset"]`).click();
        cy.get(`#${id}`).should('not.have.attr', 'open');
        cy.get(`#${id} textarea[name="message"]`).should('have.value', '');
        cy.get('@toggle').should('have.attr', 'aria-expanded', 'false');
      });
    });

    it('edits the status message in a modal dialog', () => {
      cy.visit(`${base}&view=category&layout=list`);
      // The button is in the menu of the user (the close buttons of the dialog name it too).
      cy.get('button[command="show-modal"][commandfor="statusTextModal"]').as('status');
      cy.get('@status').parents('.dropdown, .btn-group').first().find('[data-bs-toggle="dropdown"]').first().click();
      cy.get('@status').should('be.visible').click();
      cy.get('#statusTextModal').should('have.attr', 'open');
      cy.get('#statusTextModal').should(($dialog) => {
        expect($dialog[0].matches(':modal'), 'a modal dialog').to.eq(true);
      });
      cy.focused().should('have.attr', 'name', 'status_text');
      cy.screenshot('dialog-status', { capture: 'viewport' });
      cy.get('#statusTextModal button[command="close"]').last().click();
      cy.get('#statusTextModal').should('not.have.attr', 'open');
    });
  });
});
