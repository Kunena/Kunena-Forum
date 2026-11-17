// The dates of an announcement (date, publish up, publish down) are date and time fields of the browser (<input type="datetime-local">)
// in place of bootstrap-datepicker, which set a date without its time in these date and time columns. The fields show the stored
// date and time, the empty date (no end) is an empty field, and a new date and time is stored as Y-m-d H:i:s. The row is inserted
// by the spec and removed after each test.
describe('Kunena announcements: the dates are date and time fields', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};

  const stored = () => cy.task('queryDB', `SELECT CAST(created AS CHAR) AS created, CAST(publish_up AS CHAR) AS up, CAST(publish_down AS CHAR) AS down FROM #__kunena_announcement WHERE id = ${ids.announcement}`).then((rows) => rows[0]);

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_announcement (title, sdescription, description, created, published, showdate, publish_up, publish_down, ordering, created_by) VALUES ('Cypress announcement dates ${stamp}', 'Short', 'Long', '2026-01-02 09:15:00', 1, 1, '2026-01-03 10:30:00', '1000-01-01 00:00:00', 0, 797)`,
    ).then((row) => {
      ids.announcement = row.insertId;
    });
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_announcement WHERE id = ${ids.announcement}`);
  });

  it('shows the stored dates and times in the date and time fields', () => {
    cy.visit(`${base}&view=announcement&layout=edit&id=${ids.announcement}`);
    cy.get('input[name="created"]').should('have.attr', 'type', 'datetime-local').and('have.value', '2026-01-02T09:15');
    cy.get('input[name="publish_up"]').should('have.attr', 'type', 'datetime-local').and('have.value', '2026-01-03T10:30');
    // The empty date of the base: no end.
    cy.get('input[name="publish_down"]').should('have.attr', 'type', 'datetime-local').and('have.value', '');
    cy.get('script[src*="datepicker"], link[href*="datepicker"]').should('not.exist');
  });

  it('stores a new date and time of the field as a date and time', () => {
    cy.visit(`${base}&view=announcement&layout=edit&id=${ids.announcement}`);
    cy.get('input[name="publish_down"]').type('2026-12-31T23:45');
    cy.get('input[name="submit"]').click();
    cy.location('href').should('not.contain', 'layout=edit');
    stored().should((row) => {
      expect(row.down, 'the end of the announcement').to.eq('2026-12-31 23:45:00');
      expect(row.up, 'the start, unchanged').to.eq('2026-01-03 10:30:00');
      expect(row.created, 'the date, unchanged').to.eq('2026-01-02 09:15:00');
    });
  });
});
