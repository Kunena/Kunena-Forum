// The forms which write: a category made, changed and deleted in the forum (as the administrator), and a draft saved, continued and
// deleted (as a user). The answer is checked in the database, and the afterEach of the support file fails a test for a PHP notice or
// warning on a page.
describe('Kunena writing: categories and drafts', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const categoryName = `Cypress category ${Date.now()}`;
  let draftsBefore;

  const typeInEditor = (text) => {
    cy.window().should('have.property', 'sceditor');
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty')
      .then(($body) => cy.wrap($body).click().type(text, { parseSpecialCharSequences: false }));
  };

  before(() => {
    cy.kunena_setOption('enableDrafts', '1').then((old) => {
      draftsBefore = old;
    });
  });

  after(() => {
    cy.kunena_setOption('enableDrafts', draftsBefore ?? '0');
    // What a failed test can leave behind.
    cy.task('queryDB', `DELETE a FROM #__kunena_aliases AS a INNER JOIN #__kunena_categories AS c ON a.item = c.id AND a.type = 'catid' WHERE c.name = '${categoryName}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE name = '${categoryName}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_drafts WHERE user_id = ${user.id}`);
  });

  describe('a category, as the administrator', () => {
    beforeEach(() => {
      cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
    });

    it('makes a category with the form of the forum', () => {
      cy.visit(`${base}&view=category&layout=create&parentid=1`);

      cy.get('input[name="name"]').type(categoryName);
      cy.get('textarea[name="description"]').type('A category made by the test.');
      cy.get('input[name="submit"]').click();

      cy.task('queryDB', `SELECT id, parentid, published FROM #__kunena_categories WHERE name = '${categoryName}'`).then((rows) => {
        expect(rows, 'the new category').to.have.length(1);
        expect(Number(rows[0].parentid), 'it is in the section').to.eq(1);
      });
    });

    it('changes the settings of the category', () => {
      cy.task('queryDB', `SELECT id FROM #__kunena_categories WHERE name = '${categoryName}'`).then((rows) => {
        cy.visit(`${base}&view=category&layout=manage&catid=${rows[0].id}`);

        cy.get('input[name="name"]').clear().type(`${categoryName} (changed)`);
        cy.get('select[name="params[teaser]"]').select(1, { force: true });
        cy.get('select[name="params[ownTopicsOnly]"]').select(1, { force: true });
        cy.get('input[name="params[inactiveLockDays]"]').clear({ force: true }).type('30', { force: true });
        cy.get('input[name="submit"]').click();

        cy.task('queryDB', `SELECT name, params FROM #__kunena_categories WHERE id = ${rows[0].id}`).then((saved) => {
          expect(saved[0].name).to.eq(`${categoryName} (changed)`);

          const params = JSON.parse(saved[0].params);

          expect(String(params.inactiveLockDays)).to.eq('30');
          expect(String(params.ownTopicsOnly)).to.not.eq('0');
        });

        // The name goes back, so the cleanup finds the category.
        cy.task('queryDB', `UPDATE #__kunena_categories SET name = '${categoryName}' WHERE id = ${rows[0].id}`);
      });
    });

    it('deletes the category with its own button', () => {
      cy.task('queryDB', `SELECT id FROM #__kunena_categories WHERE name = '${categoryName}'`).then((rows) => {
        cy.visit(`${base}&view=category&layout=manage&catid=${rows[0].id}`);

        cy.on('window:confirm', () => true);
        cy.contains('button[type="submit"].btn-outline-danger', /delete/i).click();

        cy.task('queryDB', `SELECT id FROM #__kunena_categories WHERE name = '${categoryName}'`).then((left) => {
          expect(left, 'the category is deleted').to.have.length(0);
        });
      });
    });
  });

  describe('a draft, as a user', () => {
    beforeEach(() => {
      cy.doFrontendLogin(user.name, user.password);
    });

    it('saves a draft of a new topic', () => {
      cy.visit(`${base}&view=topic&layout=create&catid=3`);

      cy.get('#postform input[name="subject"]').type('A draft of the test');
      typeInEditor('The text of the draft.');
      cy.contains('#postform button, #postform input[type="submit"], #postform a', /Save draft/i).click();

      cy.task('queryDB', `SELECT subject, message FROM #__kunena_drafts WHERE user_id = ${user.id}`).then((rows) => {
        expect(rows, 'the draft').to.have.length(1);
        expect(rows[0].subject).to.eq('A draft of the test');
        expect(rows[0].message).to.include('The text of the draft.');
      });
    });

    it('lists the draft and continues it in the editor', () => {
      cy.visit(`${base}&view=topic&layout=drafts`);

      cy.contains('A draft of the test').should('exist');
      cy.contains('a, button', /Continue/i).first().click();

      cy.get('#postform input[name="subject"]').should('have.value', 'A draft of the test');
    });

    it('deletes the draft', () => {
      cy.visit(`${base}&view=topic&layout=drafts`);

      cy.on('window:confirm', () => true);
      cy.contains('a, button', /Delete/i).first().click();

      cy.task('queryDB', `SELECT id FROM #__kunena_drafts WHERE user_id = ${user.id}`).then((rows) => {
        expect(rows, 'no draft is left').to.have.length(0);
      });
    });
  });
});
