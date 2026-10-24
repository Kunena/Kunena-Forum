// The clean-up of the moderation log in the backend (task logs.clean with "older than N days"): an entry older than the days is
// removed, a newer one stays. (1000 days: the entries of the test site, which are recent, are not touched.)
describe('Kunena backend logs: clean old entries', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const ids = {};
  const addLog = (daysAgo) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_logs (type, user_id, category_id, topic_id, target_user, ip, time, operation, data) VALUES (1, 797, 0, 0, 0, '', UNIX_TIMESTAMP() - ${daysAgo} * 86400, 'LOG_TOPIC_LOCK', '{}')`,
  );
  const exists = (id) => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_logs WHERE id = ${id}`).then((rows) => Number(rows[0].n) === 1);

  beforeEach(() => {
    addLog(2000).then((log) => {
      ids.old = log.insertId;
    });
    addLog(10).then((log) => {
      ids.recent = log.insertId;
    });
  });

  it('removes the entry older than the days and keeps the newer one', () => {
    cy.task('queryDB', 'SELECT COUNT(*) AS n FROM #__kunena_logs').then((rows) => {
      ids.before = Number(rows[0].n);
    });
    cy.doAdministratorLogin();
    cy.request(`${admin}&view=logs`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({ method: 'POST', url: `${admin}&task=logs.clean`, form: true, body: { clean_days: 1000, [token]: '1' }, followRedirect: false });
    });
    cy.then(() => exists(ids.old)).should('eq', false);
    cy.then(() => exists(ids.recent)).should('eq', true);
    // Only the old entry is gone.
    cy.task('queryDB', 'SELECT COUNT(*) AS n FROM #__kunena_logs').then((rows) => expect(Number(rows[0].n)).to.eq(ids.before - 1));
  });
});
