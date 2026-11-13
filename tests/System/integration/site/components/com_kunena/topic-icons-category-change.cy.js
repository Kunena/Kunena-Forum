// The new topic form loads the topic icons of the category again when the author chooses another category. That list was not
// the one of the page: no titles, other icons, another markup, and the chosen icon was lost. It must be the same list as on
// the loading of the page, whatever the options of the template are.
describe('Kunena new topic: the topic icons after a change of the category', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const aliases = [`cypress-icons-a-${stamp}`, `cypress-icons-b-${stamp}`];
  const ids = [];
  const old = {};

  // The radios, the texts of the labels and the element of each icon, in the order of the list.
  const readList = () => cy.get('#iconset_topicList').then(($list) => ({
    values: $list.find('input[name="topic_emoticon"]').toArray().map((input) => input.value),
    texts: $list.find('label.radio').toArray().map((label) => label.textContent.trim()),
    tags: $list.find('label.radio').toArray().map((label) => (label.firstElementChild ? label.firstElementChild.tagName.toLowerCase() : '')),
  }));

  beforeEach(() => {
    ids.length = 0;
    cy.kunena_setOption('topicIcons', '1').then((value) => {
      // Without the option in the database, the default of Kunena: null removes it again.
      old.topicIcons = value ?? null;
    });

    // The form of a category lists that category and its children (a category alone has no list): the second is a child of the first.
    aliases.forEach((alias, index) => {
      cy.then(() => cy.task(
        'queryDB',
        `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, allowAnonymous, postAnonymous, params, iconset) VALUES (${index ? ids[0] : 1}, 'Cypress icons ${index} ${stamp}', '${alias}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, 0, 0, '{}', 'default')`,
      )).then((result) => {
        ids.push(result.insertId);
        cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('${alias}', 'catid', ${result.insertId}, 0)`);
      });
    });

    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  });

  afterEach(() => {
    aliases.forEach((alias) => cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = '${alias}'`));
    cy.then(() => {
      ids.forEach((id) => {
        cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${id}`);
      });
    });
    cy.then(() => cy.kunena_setOption('topicIcons', old.topicIcons));
  });

  it('shows the same icons, titles and markup, and keeps the chosen icon', () => {
    cy.intercept('POST', /topicicons/).as('topicicons');
    cy.then(() => cy.visit(`${base}&view=topic&layout=create&catid=${ids[0]}`));

    cy.get('#iconset_topicList input[name="topic_emoticon"]').should('have.length.greaterThan', 2);
    readList().then((expected) => {
      expect(expected.values, 'the icons on the loading of the page').to.include('2');

      cy.get('#radio2').check({ force: true });

      // The list of the page: the script replaces it with a new element.
      let oldList;
      cy.get('#iconset_topicList').then(($list) => {
        oldList = $list[0];
      });
      cy.then(() => cy.get('#postcatid').select(String(ids[1])));
      cy.wait('@topicicons');

      cy.get('#iconset_topicList').should(($list) => expect($list[0], 'the list after the change of the category').to.not.equal(oldList));
      cy.get('#iconset_topicList input[name="topic_emoticon"]').should('have.length', expected.values.length);
      readList().then((actual) => {
        expect(actual.values, 'the values of the radios').to.deep.eq(expected.values);
        expect(actual.texts, 'the texts of the labels').to.deep.eq(expected.texts);
        expect(actual.tags, 'the element of each icon').to.deep.eq(expected.tags);
      });
      cy.get('#iconset_topicList .kiconsel').should('not.exist');
      cy.get('#radio2').should('be.checked');
      cy.get('#iconset_topicList input[name="topic_emoticon"]:checked').should('have.length', 1);
    });
  });
});
