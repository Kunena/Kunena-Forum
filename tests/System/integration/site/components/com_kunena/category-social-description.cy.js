// The page of a category gave the social sites (og:description, twitter:description) its description as it is stored, with the
// BBCode tags: a share showed "[b]Welcome[/b] [url=...]". The meta description of the page had the text already.
describe('Kunena category: the description for the social sites', () => {
  const stamp = Date.now();
  const alias = `cypress-social-${stamp}`;
  const ids = {};

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress social ${stamp}', '${alias}', '', '', '[b]Welcome[/b] to [url=https://example.com/]our place[/url] ${stamp}', '', '', 'joomla.level', 1, 1, 1, 8, 1, 90, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('${alias}', 'catid', ${category.insertId}, 0)`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = '${alias}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE alias = '${alias}'`);
  });

  it('gives the text of the description without BBCode', () => {
    cy.then(() => cy.request(`/index.php?option=com_kunena&view=category&catid=${ids.category}`)).then((page) => {
      const body = String(page.body);
      const og = /<meta property="og:description" content="([^"]*)"/.exec(body);
      const twitter = /<meta name="twitter:description" content="([^"]*)"/.exec(body);

      expect(og && og[1], 'og:description').to.eq(`Welcome to our place ${stamp}`);
      expect(twitter && twitter[1], 'twitter:description').to.eq(`Welcome to our place ${stamp}`);
    });
  });
});
