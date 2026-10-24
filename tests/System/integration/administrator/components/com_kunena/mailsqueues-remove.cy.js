// The queue of the notification emails in the backend (view mailsqueues, task mailsqueues.remove): the list shows a queued
// email, and removing the chosen rows deletes those and keeps the others. (Not the purge: it empties the whole queue of the
// test site.)
describe('Kunena backend mail queue: remove queued emails', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const queue = (name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_notifications_mailsqueue (subject, messageId, url, emailListJson, categoryName, once, sentusers, send) VALUES ('Cypress queue ${name} ${stamp}', 0, 'index.php', '[]', 'Cypress', '0', '[]', 0)`,
  ).then((row) => {
    ids[name] = row.insertId;
  });
  const exists = (name) => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_notifications_mailsqueue WHERE id = ${ids[name]}`).then((rows) => Number(rows[0].n) === 1);

  beforeEach(() => {
    queue('removed');
    queue('kept');
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_notifications_mailsqueue WHERE subject LIKE 'Cypress queue % ${stamp}'`);
  });

  it('lists the queued email and removes the chosen one only', () => {
    cy.doAdministratorLogin();
    cy.request(`${admin}&view=mailsqueues&list[limit]=0`).then((page) => {
      expect(page.body).to.contain(`Cypress queue removed ${stamp}`);
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({ method: 'POST', url: `${admin}&task=mailsqueues.remove`, form: true, body: { 'cid[]': ids.removed, [token]: '1' }, followRedirect: false });
    });
    cy.then(() => exists('removed')).should('eq', false);
    cy.then(() => exists('kept')).should('eq', true);
  });
});
