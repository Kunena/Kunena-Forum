// The tasks of the backend which work on rows get requests with the ids of rows which do not exist, with text and with lists where
// an id is expected (the administrator, with a valid token): the backend may refuse them but must not answer with a server error.
// Only ids which are no rows are used, so no data of the site is changed; tools which act on the whole forum are not sent.
describe('Kunena backend: requests with ids which are no rows', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const tasks = [
    'attachments.delete',
    'badges.save', 'badges.cancel',
    'categories.lock', 'categories.unlock', 'categories.review', 'categories.unreview', 'categories.allowanonymous', 'categories.denyanonymous',
    'categories.allowpolls', 'categories.denypolls', 'categories.publish', 'categories.unpublish', 'categories.delete', 'categories.archive',
    'categories.trash', 'categories.orderup', 'categories.orderdown', 'categories.batchcategories', 'categories.saveorderajax',
    'category.save', 'category.apply', 'category.cancel', 'category.checkin', 'category.publish', 'category.unpublish',
    'ranks.edit', 'ranks.remove', 'rank.save',
    'smilies.edit', 'smilies.remove', 'smiley.save',
    'socials.save', 'socials.cancel',
    'trashs.restore',
    'user.save', 'user.applychanges', 'user.moveMessages',
    'users.edit', 'users.logout', 'users.moderate', 'users.unmoderate', 'users.block', 'users.unblock', 'users.batchmoderators', 'users.ban', 'users.unban',
    'users.removecatsubscriptions', 'users.removetopicsubscriptions', 'users.subscribeuserstocategories',
  ];
  const sets = {
    text: { 'cid[]': 'abc', id: 'abc', catid: 'abc', userid: 'abc', 'userid[]': 'abc', 'ids[]': 'abc', name: 'x', rank_title: 'x', code: 'x' },
    'no row': { 'cid[]': 99999999, id: 99999999, catid: 99999999, userid: 99999999, 'userid[]': 99999999, 'ids[]': 99999999, name: 'x', rank_title: 'x', code: 'x' },
    // A list inside a list is not used: PHP makes the number 1 of it, and the delete tasks would remove the row with the id 1.
    list: { cid: ['x'], id: ['x'], catid: ['x'], userid: ['x'], name: ['x'], 'jform[name]': ['x'], 'jform[id]': ['x'], limit: ['x'], limitstart: ['x'] },
  };

  beforeEach(() => {
    cy.doAdministratorLogin();
  });

  it('answers without a server error', () => {
    const failed = [];

    cy.request({ url: `${admin}&view=categories`, failOnStatusCode: false }).then((page) => {
      const token = (/name="([0-9a-f]{32})" value="1"/.exec(page.body) || [])[1] || 'x';

      tasks.forEach((task) => Object.entries(sets).forEach(([name, params]) => {
        ['GET', 'POST'].forEach((method) => {
          const options = method === 'GET'
            ? { url: `${admin}&task=${task}&${token}=1`, qs: params }
            : { method: 'POST', url: `${admin}&task=${task}`, form: true, body: { ...params, [token]: '1' } };

          cy.request({ ...options, failOnStatusCode: false, followRedirect: false, log: false }).then((answer) => {
            if (answer.status >= 500) {
              failed.push(`${method} ${task} with ${name}: ${answer.status}`);
            }
          });
        });
      }));
    });
    cy.then(() => expect(failed, 'the requests with a server error').to.deep.eq([]));
    // A number which is no user must not get a row of the forum (the tasks of the users saved one before).
    cy.task('queryDB', 'SELECT userid FROM #__kunena_users WHERE userid = 99999999').then((rows) => expect(rows, 'a row of the forum for a user who does not exist').to.have.length(0));
  });

  it('answers the views with ids which are no rows without a server error', () => {
    const failed = [];
    const views = ['categories', 'category', 'users', 'user', 'ranks', 'rank', 'smilies', 'smiley', 'templates', 'template', 'logs', 'plugins', 'attachments', 'trashs', 'stats', 'cpanel'];

    views.forEach((view) => Object.entries(sets).forEach(([name, params]) => {
      // The form of one row is opened with the id of a row which is not there (the lists have no such layout).
      const layout = ['category', 'user', 'rank', 'smiley', 'template'].includes(view) ? { layout: 'edit' } : {};

      cy.request({ url: `${admin}&view=${view}`, qs: { ...params, ...layout }, failOnStatusCode: false, followRedirect: false, log: false }).then((answer) => {
        if (answer.status >= 500) {
          failed.push(`${view} with ${name}: ${answer.status}`);
        }
      });
    }));
    cy.then(() => expect(failed, 'the views with a server error').to.deep.eq([]));
  });
});
