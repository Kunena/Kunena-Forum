// The check of the alias in the form of a new category in the backend (category-edit.js): the alias of another category is taken,
// and the form says so before it is saved (the server refuses it). It only did so for a category which already had aliases: a new
// category got a red border and buttons which saved, and the error came after the save.
describe('Kunena backend categories: the alias check of a new category', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};

  // Types an alias and waits for the answer of the server.
  const typeAlias = (alias) => {
    cy.intercept('GET', /task=category\.chkaliases/).as('check');
    cy.get('#jform_aliases').clear().type(alias);
    cy.wait('@check').its('response.statusCode').should('eq', 200);
  };
  const saveButtons = () => cy.get('#toolbar .button-apply, #toolbar .button-save, #toolbar .button-save-new');

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'cypress-alias-taken ${stamp}', 'cypress-alias-taken-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-alias-taken-${stamp}', 'catid', ${category.insertId}, 0)`);
    });
    cy.doAdministratorLogin();
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-alias-taken-${stamp}'`);
    cy.then(() => {
      if (ids.category) {
        cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
      }
    });
  });

  it('says that the alias is taken and saves nothing until it is free', () => {
    // The form of a new category; it is not saved.
    cy.visit(`${admin}&view=category&layout=create`);

    typeAlias(`cypress-alias-taken-${stamp}`);
    cy.get('#jform_aliases').should('have.class', 'invalid-border');
    cy.get('#aliasinfo').should('not.be.empty');
    saveButtons().should('have.length.at.least', 2).each(($button) => {
      expect($button.prop('disabled'), $button.attr('class')).to.eq(true);
    });

    typeAlias(`cypress-alias-free-${stamp}`);
    cy.get('#jform_aliases').should('not.have.class', 'invalid-border');
    cy.get('#aliasinfo').should('be.empty');
    saveButtons().each(($button) => {
      expect($button.prop('disabled'), $button.attr('class')).to.eq(false);
    });
  });
});
