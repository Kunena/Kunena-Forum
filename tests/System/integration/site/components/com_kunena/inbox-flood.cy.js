// The flood protection of the forum (the time between two posts of a user) is for the private messages too: a user could send
// as many messages as he wanted, one after the other, to all the users of the site.
describe('Kunena inbox: the flood protection', () => {
  const base = '/index.php?option=com_ajax&plugin=inbox&group=kunena&format=json';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  let oldFlood;

  const pageToken = () => cy.request('/index.php?option=com_kunena&view=home').then((response) => /name="([0-9a-f]{32})" value="1"/.exec(response.body)[1]);
  const send = (n) => pageToken().then((token) => cy.request({
    method: 'POST',
    url: `${base}&task=send`,
    form: true,
    body: { to: 'ci-admin', subject: `Flood ${n} ${stamp}`, body: 'A private message.', [token]: '1' },
    failOnStatusCode: false,
  }));
  const sent = () => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_inbox WHERE sender_id = ${user.id} AND subject LIKE 'Flood % ${stamp}'`).then((rows) => Number(rows[0].n));

  beforeEach(() => {
    cy.kunena_setOption('floodProtection', '30').then((old) => {
      oldFlood = old ?? null;
    });
  });

  afterEach(() => {
    cy.kunena_setOption('floodProtection', oldFlood);
    cy.task('queryDB', `DELETE FROM #__kunena_inbox WHERE subject LIKE 'Flood % ${stamp}'`);
  });

  it('refuses a second private message within the time of the flood protection', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    send(1);
    send(2);
    sent().should('eq', 1);
  });
});
