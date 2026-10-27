// The head of the page of a topic named its author with his username (the meta tag profile:username, and the author of the
// structured data of schema.org), the name with which he logs in, also when the forum shows the real names and keeps the usernames
// for itself: every visitor, and the search engines, read the usernames there. They have the name which the forum shows now.
describe('Kunena topic page: the name of the author in the head of the page', () => {
  const stamp = Date.now();
  const user = { id: 798, username: 'ci-user' };
  const topic = { id: 4, catid: 3 };
  const realName = `Zq Person ${stamp}`;
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

  it('names the author with the name which the forum shows', () => {
    cy.request(`/index.php?option=com_kunena&view=topic&catid=${topic.catid}&id=${topic.id}`).then((page) => {
      const meta = /<meta property="profile:username" content="([^"]*)"/.exec(page.body);
      const person = /"@type":\s*"Person",\s*"name":\s*"([^"]*)"/.exec(page.body);

      expect(meta && meta[1], 'the meta tag').to.eq(realName);
      expect(person && person[1], 'the author of the structured data').to.eq(realName);
    });
  });
});
