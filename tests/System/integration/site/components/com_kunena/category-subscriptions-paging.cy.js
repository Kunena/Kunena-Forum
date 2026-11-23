// The list of the categories which a user follows (view=category&layout=user) is paged: the pagination took the start and the limit
// of the request, the list did not, so every page showed the first categories again.
describe('Kunena subscribed categories: the pages of the list', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const categories = [2, 3, 4];
  const old = {};

  // The aliases of the categories which a page links to.
  const shown = (start, aliases) => cy.request(`${base}&view=category&layout=user&limit=2&limitstart=${start}`).then((page) => aliases.filter((alias) => page.body.includes(`/${alias}"`) || page.body.includes(`/${alias}/`)));

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

  it('shows the next categories on the next page', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.task('queryDB', `SELECT alias FROM #__kunena_categories WHERE id IN (${categories.join(',')})`).then((rows) => {
      const aliases = rows.map((row) => row.alias);

      shown(0, aliases).then((first) => {
        expect(first, 'the first page').to.have.length(2);

        shown(2, aliases).then((second) => {
          expect(second, 'the second page').to.have.length(1);
          expect(first, 'no category on both pages').not.to.include(second[0]);
        });
      });
    });
  });
});
