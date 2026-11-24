// The feeds name the author of each item in its title ("Subject - By: Tom & Jerry", the option rssAuthorInTitle, on by default)
// and as its author: the name was HTML, the feed escapes it again, and a reader showed "Tom &amp; Jerry".
describe('Kunena feeds: the name of the author', () => {
  const stamp = Date.now();
  const user = 798;
  const realName = `Tom & Jerry ${stamp}`;
  const old = {};

  before(() => {
    cy.kunena_setOption('username', '0').then((value) => {
      old.username = value ?? null;
    });
    cy.kunena_setOption('rssAuthorInTitle', '1').then((value) => {
      old.rssAuthorInTitle = value ?? null;
    });
    cy.task('queryDB', `SELECT name FROM #__users WHERE id = ${user}`).then((rows) => {
      old.name = rows[0].name;
    });
    cy.task('queryDB', `UPDATE #__users SET name = '${realName}' WHERE id = ${user}`);
  });

  after(() => {
    cy.kunena_setOption('username', old.username);
    cy.kunena_setOption('rssAuthorInTitle', old.rssAuthorInTitle);
    cy.then(() => cy.task('queryDB', `UPDATE #__users SET name = '${old.name}' WHERE id = ${user}`));
  });

  it('has the name of the author once escaped', () => {
    cy.request('/index.php?option=com_kunena&view=topics&layout=feed&format=feed&type=rss&mode=topics&sel=1%20year').then((response) => {
      const body = String(response.body);

      expect(body, 'the name in a title').to.include(`Tom &amp; Jerry ${stamp}`);
      expect(body, 'a name escaped twice').not.to.include(`Tom &amp;amp; Jerry ${stamp}`);
    });
  });
});
