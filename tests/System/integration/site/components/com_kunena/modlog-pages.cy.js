// The moderation log (view=topic&layout=modlog) read the first entry of a page from "start", but its page links carry "limitstart"
// (as every list of the forum): without SEF addresses (where Joomla does not turn limitstart into ?start=) every page showed the
// first 25 entries.
describe('Kunena moderation log: the pages', () => {
  const stamp = Date.now();
  const op = (n) => `CYPRESS_PAGE_${stamp}_${n}`;
  // An operation without a translation is shown as a text made of its name.
  const shown = (n) => `Cypress page ${stamp} ${n}<`;

  beforeEach(() => {
    // 30 entries, the newest last: the first page has 30 to 6, the second 5 to 1 (and the older entries of the site).
    for (let n = 1; n <= 30; n++) {
      cy.task('queryDB', `INSERT INTO #__kunena_logs (type, user_id, category_id, topic_id, target_user, ip, time, operation, data) VALUES (1, 797, 3, 0, 0, '', UNIX_TIMESTAMP(), '${op(n)}', '{}')`);
    }

    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE operation LIKE 'CYPRESS_PAGE_${stamp}_%'`);
  });

  it('shows the older entries on the second page', () => {
    cy.request('/index.php?option=com_kunena&view=topic&layout=modlog&limitstart=25').then((page) => {
      // The rows of the log (the filter of the operations above lists every operation).
      const text = String(page.body);
      const body = text.slice(text.indexOf('<tbody'), text.lastIndexOf('</tbody>'));

      expect(body, 'an entry of the second page').to.include(shown(5));
      expect(body, 'the newest entry, of the first page').not.to.include(shown(30));
    });
  });
});
