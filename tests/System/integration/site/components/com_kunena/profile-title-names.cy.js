// The page of a profile had the name of the member escaped twice in its title and its meta tags: "Tom & Jerry" showed as
// "Tom &amp; Jerry" in the tab of the browser, in og:title, profile:username and the description (the name was taken as HTML,
// then Joomla escaped the title and the meta tags again).
describe('Kunena profile page: the name of the member in the head of the page', () => {
  const stamp = Date.now();
  const user = { id: 798 };
  const realName = `Tom & Jerry ${stamp}`;
  const html = `Tom &amp; Jerry ${stamp}`;
  const old = {};

  before(() => {
    cy.kunena_setOption('username', '0').then((value) => {
      old.username = value ?? null;
    });
    cy.task('queryDB', `SELECT name FROM #__users WHERE id = ${user.id}`).then((rows) => {
      old.name = rows[0].name;
    });
    cy.task('queryDB', `UPDATE #__users SET name = '${realName}' WHERE id = ${user.id}`);
  });

  after(() => {
    cy.kunena_setOption('username', old.username);
    cy.then(() => cy.task('queryDB', `UPDATE #__users SET name = '${old.name}' WHERE id = ${user.id}`));
  });

  it('has the name once escaped in the title and the meta tags', () => {
    cy.request(`/index.php?option=com_kunena&view=user&userid=${user.id}`).then((page) => {
      const body = String(page.body);
      const title = /<title>([^<]*)<\/title>/.exec(body);
      const ogTitle = /<meta property="og:title" content="([^"]*)"/.exec(body);
      const profile = /<meta property="profile:username" content="([^"]*)"/.exec(body);

      expect(title && title[1], 'the title').to.include(html);
      expect(ogTitle && ogTitle[1], 'og:title').to.eq(html);
      expect(profile && profile[1], 'profile:username').to.eq(html);
      expect(body, 'a name escaped twice').not.to.include('Tom &amp;amp; Jerry');
    });
  });

  it('has the name once escaped in the title of the edit page', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request('/index.php?option=com_kunena&view=user&layout=edit').then((page) => {
      const title = /<title>([^<]*)<\/title>/.exec(String(page.body));

      expect(title && title[1], 'the title').to.include(html);
      expect(String(page.body), 'a name escaped twice').not.to.include('Tom &amp;amp; Jerry');
    });
  });
});
