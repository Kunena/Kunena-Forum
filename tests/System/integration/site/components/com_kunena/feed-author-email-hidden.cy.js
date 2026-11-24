// With the option "Author format: e-mail" (or both) the feeds gave the e-mail address of the Joomla account of every author, to
// every reader (guests and bots too), also of a member who hides his address: the choice of the member (hideEmail) and the option
// "Show e-mail addresses" were not asked, as everywhere else (canDisplayEmail()).
describe('Kunena feeds: the e-mail address of an author who hides it', () => {
  const user = 798;
  const old = {};

  before(() => {
    cy.kunena_setOption('rssAuthorFormat', 'email').then((value) => {
      old.rssAuthorFormat = value ?? null;
    });
    cy.task('queryDB', `SELECT email FROM #__users WHERE id = ${user}`).then((rows) => {
      old.email = rows[0].email;
    });
    cy.task('queryDB', `SELECT hideEmail FROM #__kunena_users WHERE userid = ${user}`).then((rows) => {
      old.hideEmail = rows[0].hideEmail;
    });
    cy.task('queryDB', `UPDATE #__kunena_users SET hideEmail = 1 WHERE userid = ${user}`);
  });

  after(() => {
    cy.kunena_setOption('rssAuthorFormat', old.rssAuthorFormat);
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET hideEmail = ${Number(old.hideEmail)} WHERE userid = ${user}`));
  });

  it('does not give the hidden address to a guest', () => {
    cy.request('/index.php?option=com_kunena&view=topics&layout=feed&format=feed&type=rss&mode=topics&sel=1%20year').then((response) => {
      const body = String(response.body);

      expect(body, 'the feed').to.include('<rss');
      expect(body, 'a post of the member').to.include('Which template do you use?');
      expect(body, 'the hidden e-mail address').not.to.include(old.email);
    });
  });
});
