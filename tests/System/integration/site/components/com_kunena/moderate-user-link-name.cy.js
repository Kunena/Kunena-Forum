// The page to moderate a message links to the profile of its author; the tooltip of that link had the name escaped twice:
// "Tom & Jerry" read as "Tom &amp; Jerry (798)" (the HTML name was given as the title, which the link escapes again).
describe('Kunena moderate page: the name of the author in the tooltip', () => {
  const stamp = Date.now();
  const user = 798;
  const message = { id: 4, topic: 4, catid: 3 };
  const realName = `Tom & Jerry ${stamp}`;
  const old = {};

  before(() => {
    cy.kunena_setOption('username', '0').then((value) => {
      old.username = value ?? null;
    });
    cy.task('queryDB', `SELECT name FROM #__users WHERE id = ${user}`).then((rows) => {
      old.name = rows[0].name;
    });
    cy.task('queryDB', `UPDATE #__users SET name = '${realName}' WHERE id = ${user}`);
  });

  after(() => {
    cy.kunena_setOption('username', old.username);
    cy.then(() => cy.task('queryDB', `UPDATE #__users SET name = '${old.name}' WHERE id = ${user}`));
  });

  it('has the name once escaped in the tooltip', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
    cy.request(`/index.php?option=com_kunena&view=topic&layout=moderate&catid=${message.catid}&id=${message.topic}&mesid=${message.id}`).then((page) => {
      const body = String(page.body);

      expect(body, 'the tooltip of the link').to.include(`title="Tom &amp; Jerry ${stamp} (${user})"`);
      expect(body, 'a name escaped twice').not.to.include('Tom &amp;amp; Jerry');
    });
  });
});
