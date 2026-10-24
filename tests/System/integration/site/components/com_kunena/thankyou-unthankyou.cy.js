// A user thanks a message of another user (task thankyou) and takes the thanks back (task unthankyou): the row of the thanks
// comes and goes, and the counter of thanks of the author goes up and back down to what it was.
describe('Kunena topic: thank you and take it back', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const stamp = Date.now();
  const ids = {};
  const thanks = () => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_thankyou WHERE postid = ${ids.message} AND userid = ${user.id}`).then((rows) => Number(rows[0].n));
  const counter = () => cy.task('queryDB', 'SELECT thankyou AS n FROM #__kunena_users WHERE userid = 797').then((rows) => Number(rows[0].n));
  const send = (task) => cy.request(`${base}&view=topic&catid=3&id=9`).then((page) => {
    const token = /"csrf\.token":"([0-9a-f]{32})"/.exec(page.body)?.[1] || /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ url: `${base}&view=topic&task=${task}&catid=3&id=9&mesid=${ids.message}&${token}=1`, followRedirect: false });
  });

  beforeEach(() => {
    counter().then((n) => {
      ids.counter = n;
    });
    // A reply of ci-admin in the topic 9.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (10, 9, 3, 'ci-admin', 797, '', 'Re: thanks ${stamp}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A message to thank.')`);
    });
    cy.doFrontendLogin(user.name, user.password, false);
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_thankyou WHERE postid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE topic_id = 9 AND data LIKE '%"mesid":${ids.message}%'`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET thankyou = ${ids.counter} WHERE userid = 797`));
    cy.kunena_fixUserCounters();
  });

  it('adds the thanks and takes it back, and the counter of the author follows', () => {
    cy.then(() => send('thankyou'));
    cy.then(() => thanks()).should('eq', 1);
    cy.then(() => counter()).should('eq', ids.counter + 1);
    cy.then(() => send('unthankyou'));
    cy.then(() => thanks()).should('eq', 0);
    cy.then(() => counter()).should('eq', ids.counter);
  });
});
