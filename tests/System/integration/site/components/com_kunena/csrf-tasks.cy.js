// A task which changes data must not run without the token of the form (CSRF): the administrator, with all rights, sends the tasks
// of the forum without a token (and with a token which is not his) for a category, a topic, a message and a user which the test made,
// and none of them may be changed.
describe('Kunena security: tasks without a valid token', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};

  const tables = ['topics', 'messages', 'messages_text', 'categories', 'users_banned', 'announcement', 'user_topics', 'user_categories', 'thankyou', 'user_ignore', 'user_follow', 'polls', 'polls_options', 'drafts', 'aliases'];
  const snapshot = () => cy.task('queryDB', `CHECKSUM TABLE ${tables.map((table) => `#__kunena_${table}`).join(', ')}`).then((rows) => rows.map((row) => row.Checksum).join('-'));

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress csrf ${stamp}', 'cypress-csrf-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 94, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-csrf-${stamp}', 'catid', ${ids.category}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, 'Cypress csrf ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 798, 'x', 'ci-user', 0, UNIX_TIMESTAMP(), 798, 'x', 'ci-user', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, ${ids.category}, 'ci-user', 798, '', 'Cypress csrf ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;

      return cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${ids.message}, 'A message for the test.')`);
    }).then(() => cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.message}, last_post_id = ${ids.message} WHERE id = ${ids.topic}`))
      .then(() => cy.task('queryDB', `INSERT INTO #__kunena_announcement (title, sdescription, description, created, published, showdate, publish_up, publish_down, ordering, created_by) VALUES ('Cypress csrf ${stamp}', 's', 'd', NOW(), 1, 1, '1000-01-01 00:00:00', '1000-01-01 00:00:00', 0, 797)`))
      .then((announcement) => {
        ids.announcement = announcement.insertId;
      });
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid IN (SELECT id FROM (SELECT id FROM #__kunena_messages WHERE thread = ${ids.topic}) AS m)`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-csrf-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_announcement WHERE title = 'Cypress csrf ${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_users_banned WHERE userid = 798`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_ignore WHERE user_id = 797`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_follow WHERE user_id = 797`);
  });

  const requests = () => {
    const topic = `view=topic&catid=${ids.category}&id=${ids.topic}`;
    const message = `${topic}&mesid=${ids.message}`;
    const fields = { catid: ids.category, id: ids.topic, mesid: ids.message, userid: 798, topic_id: ids.topic };
    const list = [];
    const get = (query) => list.push({ method: 'GET', url: `${base}&${query}` });
    const post = (query, body) => list.push({ method: 'POST', url: `${base}&${query}`, body });

    ['sticky', 'unsticky', 'lock', 'unlock', 'delete', 'undelete', 'permdelete', 'approve', 'solved', 'subscribe', 'favorite', 'resetvotes', 'thankyou'].forEach((task) => {
      get(`${topic}&task=${task}`);
      get(`${message}&task=${task}`);
      post(`view=topic&task=${task}`, fields);
    });
    post('view=topic&task=move', { ...fields, targetcategory: 3, targetid: 0, mode: 'topic' });
    post('view=topic&task=edit', { ...fields, subject: 'Changed without a token', message: 'Changed without a token' });
    post('view=topic&task=post', { catid: ids.category, subject: 'Created without a token', message: 'Created without a token' });
    post('view=topic&task=report', { ...fields, reason: 'x', text: 'x' });
    post('view=topic&task=savedraft', { catid: ids.category, subject: 'A draft without a token', message: 'x' });

    ['delete', 'markread', 'subscribe', 'unsubscribe'].forEach((task) => {
      get(`view=category&catid=${ids.category}&task=${task}`);
      post('view=category&task=' + task, fields);
    });
    post('view=category&task=save', { ...fields, name: 'Changed without a token' });

    ['delete', 'permdel', 'restore', 'approve', 'unfavorite', 'unsubscribe', 'move'].forEach((task) => {
      get(`view=topics&task=${task}&topics[]=${ids.topic}&catid=${ids.category}`);
      post(`view=topics&task=${task}`, { 'topics[]': ids.topic, catid: ids.category, targetcategory: 3 });
    });

    ['ban', 'karmaup', 'karmadown', 'ignore', 'follow', 'save', 'change'].forEach((task) => {
      get(`view=user&task=${task}&userid=798`);
      post(`view=user&task=${task}`, { userid: 798, ban: 1, banexpires: '', reason_public: 'x', name: 'Changed without a token' });
    });

    ['delete', 'publish', 'unpublish', 'save'].forEach((task) => {
      get(`view=announcement&task=${task}&id=${ids.announcement}`);
      post(`view=announcement&task=${task}`, { id: ids.announcement, title: 'Changed without a token', description: 'x', sdescription: 'x' });
    });

    return list;
  };

  const run = (tokenOf) => {
    snapshot().then((start) => {
      cy.then(() => requests()).then((list) => {
        list.forEach(({ method, url, body }) => {
          const withToken = tokenOf ? { [tokenOf]: '1' } : {};

          cy.request(method === 'GET'
            ? { url: `${url}${tokenOf ? `&${tokenOf}=1` : ''}`, failOnStatusCode: false, followRedirect: false }
            : { method, url, form: true, body: { ...body, ...withToken }, failOnStatusCode: false, followRedirect: false });
        });
      });
      snapshot().then((end) => expect(end, 'the data of the forum after the tasks').to.eq(start));
    });
  };

  it('does nothing without a token', () => {
    run(null);
  });

  it('changes the topic with a valid token (so the test can see a change)', () => {
    snapshot().then((start) => {
      cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`).then((response) => {
        const token = /name="([0-9a-f]{32})" value="1"/.exec(response.body)[1];

        cy.request({ url: `${base}&view=topic&catid=${ids.category}&id=${ids.topic}&task=sticky&${token}=1`, followRedirect: false, failOnStatusCode: false });
      });
      snapshot().then((end) => expect(end, 'the data after a task with a valid token').to.not.eq(start));
    });
  });

  it('does nothing with a token which is not valid', () => {
    run('0123456789abcdef0123456789abcdef');
  });
});
