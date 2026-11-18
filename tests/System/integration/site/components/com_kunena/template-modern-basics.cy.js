// The basics of a modern page in the templates: a ring for the keyboard focus in Aurelia (its style removed the outline of every link
// and gave nothing back), the scripts of the forum deferred (they stopped the rendering of the page in the head), the autocomplete of
// the account fields of the profile (a password manager filled the saved password in the first field of the new password only, and
// the profile was not saved), the split buttons of the topic as buttons (the label was a link without address, which the keyboard
// did not reach), and the Enter of an input method (Japanese, Chinese) which takes no name in the user search.
describe('Kunena templates: focus, scripts, autofill, buttons and input methods', () => {
  const plain = '/index.php?option=com_kunena';

  const firstTopic = () => cy.task(
    'queryDB',
    'SELECT t.id, t.category_id FROM #__kunena_topics AS t INNER JOIN #__kunena_categories AS c ON c.id = t.category_id WHERE t.hold = 0 AND t.moved_id = 0 AND c.published = 1 ORDER BY t.id LIMIT 1',
  ).then((rows) => rows[0]);

  it('defers the scripts of the forum', () => {
    firstTopic().then((topic) => {
      cy.request(`${plain}&view=topic&catid=${topic.category_id}&id=${topic.id}`).its('body').then((body) => {
        const scripts = body.match(/<script\b[^>]*\bsrc="[^"]*com_kunena\/template\/[^"]*"[^>]*>/g) || [];

        expect(scripts.length, 'the scripts of the template').to.be.greaterThan(0);
        scripts.forEach((script) => {
          expect(script, 'a deferred script').to.match(/\sdefer\b/);
        });
      });
    });
  });

  describe('Aurelia', () => {
    const saved = {};

    before(() => {
      cy.kunena_setOption('template', 'aurelia').then((value) => {
        saved.template = value;
      });
      cy.task('deleteRelativePath', 'cache/com_kunena_template');
    });

    after(() => {
      cy.then(() => cy.kunena_setOption('template', saved.template ?? null));
      cy.task('deleteRelativePath', 'cache/com_kunena_template');
    });

    it('shows a ring around a link which has the focus of the keyboard', () => {
      cy.visit(`${plain}&view=category&layout=list`);
      cy.get('#kunena.layout').should('not.have.class', 'agora');
      cy.get('#kunena.layout a[href]:visible').then(($links) => {
        // The link before the one which is checked has the focus; Tab moves it, as a keyboard does.
        $links[0].focus();
      });
      cy.press(Cypress.Keyboard.Keys.TAB);
      cy.focused().should('match', 'a, button, input, select').then(($focused) => {
        const style = $focused[0].ownerDocument.defaultView.getComputedStyle($focused[0]);

        // A link has the outline of the template; a button the focus shadow of Bootstrap.
        if ($focused.is('a:not(.btn)')) {
          expect(style.outlineStyle, 'the ring of the link').to.eq('solid');
          expect(parseFloat(style.outlineWidth), 'its width').to.be.at.least(2);
        } else {
          expect(style.outlineStyle !== 'none' || style.boxShadow !== 'none', 'the ring of the control').to.eq(true);
        }
      });
    });
  });

  describe('as a user', () => {
    beforeEach(() => {
      cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
    });

    it('marks the account fields of the profile for the password managers', () => {
      cy.visit(`${plain}&view=user&layout=edit`);
      cy.get('input#name').should('have.attr', 'autocomplete', 'name');
      cy.get('input#email').should('have.attr', 'autocomplete', 'email');
      cy.get('input#password').should('have.attr', 'autocomplete', 'new-password');
      cy.get('input#password2').should('have.attr', 'autocomplete', 'new-password');
    });

    it('opens the split menus of a topic with a button which the keyboard reaches', () => {
      firstTopic().then((topic) => {
        cy.visit(`${plain}&view=topic&catid=${topic.category_id}&id=${topic.id}`);
      });
      // The split buttons: a label and an arrow (the menus of the login and of the reactions have one button).
      cy.get('.btn-group:has(> .dropdown-toggle-split) > [data-bs-toggle="dropdown"]:not(.dropdown-toggle-split)').should('have.length.greaterThan', 0).each(($label) => {
        expect($label.is('button[type="button"]'), 'the label is a button').to.eq(true);
        expect($label.attr('aria-expanded'), 'it tells whether its menu is open').to.eq('false');
        // The arrow opens the same menu for the mouse: no second stop of the keyboard, no second name.
        const $arrow = $label.siblings('.dropdown-toggle-split');

        expect($arrow.is('button[type="button"][tabindex="-1"][aria-hidden="true"]'), 'the arrow').to.eq(true);
      });
      cy.get('.btn-group:has(> .dropdown-toggle-split) > [data-bs-toggle="dropdown"]:not(.dropdown-toggle-split)').first().as('label');
      cy.get('@label').focus();
      cy.focused().should('have.attr', 'data-bs-toggle', 'dropdown');
      cy.get('@label').click();
      cy.get('@label').should('have.attr', 'aria-expanded', 'true').siblings('.dropdown-menu').should('be.visible');
    });

    it('takes no name with the Enter of an input method', () => {
      cy.visit(`${plain}&view=search`);
      cy.get('#kusersearch').type('ci');
      cy.get('.kunena-autocomplete [role="option"]').should('have.length.greaterThan', 0);
      cy.get('#kusersearch').type('{downArrow}');
      cy.get('#kusersearch').then(($input) => {
        // The Enter which confirms the characters of a Japanese or Chinese input method.
        $input[0].dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', isComposing: true, bubbles: true, cancelable: true }));
      });
      cy.get('#kusersearch').should('have.value', 'ci');
      cy.get('.kunena-autocomplete').should('be.visible');
      cy.get('#kusersearch').then(($input) => {
        $input[0].dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true, cancelable: true }));
      });
      cy.get('#kusersearch').invoke('val').should('match', /^ci-/);
    });
  });
});
