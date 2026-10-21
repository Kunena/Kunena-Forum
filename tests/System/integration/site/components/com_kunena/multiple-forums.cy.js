// Two forums on one site: the menu item Forum Home can show only some categories. A category of another forum is not found (a 404,
// not an error page), and the index only lists the categories of the forum.
describe('Kunena multiple forums', () => {
  const base = '/index.php?option=com_kunena';
  let inside;
  let outside;
  let menuId;

  // Kunena and Joomla keep what they read of the menu in a cache: it is cleared after a change.
  const clearCaches = () => {
    cy.task('deleteRelativePath', 'cache/_system');
    cy.task('deleteRelativePath', 'administrator/cache/_system');
  };

  before(() => {
    cy.task('queryDB', 'SELECT id FROM #__kunena_categories WHERE parentid > 0 AND published = 1 ORDER BY id ASC LIMIT 2').then((categories) => {
      expect(categories.length, 'two categories which are in a section').to.be.at.least(2);
      inside = categories[0].id;
      outside = categories[1].id;
    });
    cy.task('queryDB', "SELECT id FROM #__menu WHERE client_id = 0 AND link LIKE '%option=com_kunena&view=home%' ORDER BY id ASC LIMIT 1").then((items) => {
      menuId = items[0].id;
      cy.task('queryDB', `UPDATE #__menu SET params = JSON_SET(params, '$.catids', JSON_ARRAY('${inside}')) WHERE id = ${menuId}`);
      clearCaches();
    });
  });

  after(() => {
    cy.task('queryDB', `UPDATE #__menu SET params = JSON_REMOVE(params, '$.catids') WHERE id = ${menuId}`);
    clearCaches();
  });

  it('shows the category of the forum', () => {
    cy.request({ url: `${base}&view=category&catid=${inside}`, failOnStatusCode: false }).its('status').should('eq', 200);
  });

  it('does not find a category of another forum (404, no error page)', () => {
    cy.request({ url: `${base}&view=category&catid=${outside}`, failOnStatusCode: false }).its('status').should('eq', 404);
  });

  it('does not find a topic of another forum', () => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${outside} LIMIT 1`).then((topics) => {
      if (!topics.length) {
        cy.log('no topic in the other category');

        return;
      }

      cy.request({ url: `${base}&view=topic&catid=${outside}&id=${topics[0].id}`, failOnStatusCode: false }).its('status').should('eq', 404);
    });
  });

  it('shows no raw language key on the page of a category which is not found', () => {
    cy.request({ url: `${base}&view=category&catid=${outside}`, failOnStatusCode: false }).its('body').then((body) => {
      const text = body.replace(/<script[\s\S]*?<\/script>/gi, '');

      expect(text).not.to.match(/\bJERROR_[A-Z_]+/);
    });
  });
});
