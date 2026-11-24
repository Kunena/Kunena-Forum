// The list of the categories which a user follows (view=category&layout=user) is paged, but the page had no links to its other
// pages: the layout showed the pagination only in the tab of the profile (embedded), so the categories after the first page could
// not be reached.
describe('Kunena subscribed categories: the links to the pages', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const categories = [2, 3, 4];
  const old = {};

  beforeEach(() => {
    cy.task('queryDB', `SELECT category_id, subscribed FROM #__kunena_user_categories WHERE user_id = ${user.id} AND category_id IN (${categories.join(',')})`).then((rows) => {
      old.rows = rows;
    });
    cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, subscribed, params) VALUES ${categories.map((id) => `(${user.id}, ${id}, 1, '')`).join(', ')} ON DUPLICATE KEY UPDATE subscribed = 1`);
  });

  afterEach(() => {
    cy.then(() => {
      const kept = (old.rows || []).map((row) => Number(row.category_id));

      cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${user.id} AND category_id IN (${categories.join(',')})${kept.length ? ` AND category_id NOT IN (${kept.join(',')})` : ''}`);
      (old.rows || []).forEach((row) => cy.task('queryDB', `UPDATE #__kunena_user_categories SET subscribed = ${Number(row.subscribed)} WHERE user_id = ${user.id} AND category_id = ${Number(row.category_id)}`));
    });
  });

  it('links the first page to the second one', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=category&layout=user&limit=2`).then((page) => {
      const body = String(page.body).replaceAll('&amp;', '&');

      expect(body, 'a link to the second page').to.match(/href="[^"]*[?&](limit)?start=2\b[^"]*"/);
    });
  });
});
