// Every task of the controllers of the forum gets requests with parameters which no form sends: text where a number is expected, a
// number which is no row, and a list (id[]=x) where a value is expected. The forum may refuse them, but it must not answer with a
// server error. The views get the same requests. It is done as a guest, a user and the administrator; the ids are no rows, so no data
// is changed.
describe('Kunena requests with parameters which no form sends', () => {
  const base = '/index.php?option=com_kunena';
  const tasks = {
    announcement: 'none edit publish unpublish banner delete save',
    category: 'save delete jump markread subscribe unsubscribe',
    misc: 'template',
    search: 'results',
    topic: 'loadattachments setinline setprivate removeinlineonattachment removeattachments categorytemplate similar reportspam loadrate setrate upload post edit thankyou unthankyou subscribe unsubscribe favorite unfavorite sticky unsticky solved lock unlock delete undelete permdelete approve move report vote resetvotes topicicons savedraft loaddraft deletedraft unsubscribemail',
    topics: 'permdel delete restore approve status move unfavorite unsubscribe',
    user: 'search change karmaup karmadown save ban status statusText upload removeAvatar loadAvatar delfile getusersmentions notifications getusersmentionssearch ignore unignore follow unfollow',
  };
  const sets = {
    empty: {},
    text: { catid: 'abc', id: 'abc', mesid: 'abc', userid: 'abc', topic_id: 'abc' },
    'no row': { catid: 99999999, id: 99999999, mesid: 99999999, userid: 99999999, topic_id: 99999999 },
    list: { catid: ['x'], id: ['x'], mesid: ['x'], userid: ['x'], topic_id: ['x'], q: ['x'], search: ['x'], subject: ['x'], type: ['x'], sig: ['x'], draftid: ['x'], parentid: ['x'] },
  };

  const views = {
    category: 'default list manage create user subscriptions',
    topic: 'default create reply edit moderate drafts',
    topics: 'default user posts unread sticky locked category mylatest noreplies latest',
    user: 'default list edit ban mention',
    search: 'default',
    statistics: 'default',
    home: 'default',
    credits: 'default',
    inbox: 'default',
    misc: 'default',
    announcement: 'default list create edit',
  };
  const viewSets = [
    { id: 'abc', catid: 'abc', userid: 'abc', mesid: 'abc' },
    { id: 99999999, catid: 99999999, userid: 99999999, mesid: 99999999 },
    { id: ['x'], catid: ['x'], userid: ['x'], mesid: ['x'], limitstart: ['x'], limit: ['x'], layout: ['x'], mode: ['x'] },
    { limitstart: -5, limit: -5, start: 'abc', sel: 'zzz', mode: 'zzz', search: '%', q: '"<>' },
    { limit: 1000000, limitstart: 1000000 },
  ];

  const runViews = (who) => {
    const failed = [];

    Object.entries(views).forEach(([view, layouts]) => [...layouts.split(' '), 'unknown'].forEach((layout) => viewSets.forEach((params, index) => {
      cy.request({ url: `${base}&view=${view}&layout=${layout}`, qs: params, failOnStatusCode: false, followRedirect: false }).then((answer) => {
        if (answer.status >= 500) {
          failed.push(`${view}/${layout} with the parameters ${index + 1}: ${answer.status}`);
        }
      });
    })));
    cy.then(() => expect(failed, `${who}: the views with a server error`).to.deep.eq([]));
  };

  const run = (who) => {
    const failed = [];

    cy.request(`${base}&view=topic&catid=3&id=9`).then((response) => {
      const token = (/name="([0-9a-f]{32})" value="1"/.exec(response.body) || [])[1] || 'x';

      Object.entries(tasks).forEach(([view, list]) => list.split(' ').forEach((task) => Object.entries(sets).forEach(([name, params]) => {
        ['GET', 'POST'].forEach((method) => {
          const url = `${base}&view=${view}&task=${task}`;
          const options = method === 'GET' ? { url: `${url}&${token}=1`, qs: params } : { method: 'POST', url, form: true, body: { ...params, [token]: '1' } };

          cy.request({ ...options, failOnStatusCode: false, followRedirect: false }).then((answer) => {
            if (answer.status >= 500) {
              failed.push(`${method} ${view}.${task} with ${name} parameters: ${answer.status}`);
            }
          });
        });
      })));
    });
    cy.then(() => expect(failed, `${who}: the requests with a server error`).to.deep.eq([]));
  };

  it('as a guest', () => {
    run('guest');
    runViews('guest');
  });

  it('as a user', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x');
    run('user');
    runViews('user');
  });

  it('as the administrator', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
    run('administrator');
    runViews('administrator');
  });
});
