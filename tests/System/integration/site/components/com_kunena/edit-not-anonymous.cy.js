// In a category with anonymous posts ("allow anonymous", "anonymous by default"), the edit form ticked "post as anonymous" for any post
// (the default of a new post), and the edit made the post anonymous on that flag alone: a member who corrected a typo lost his name
// on his post unless he unticked the box, and a moderator who edited the post of a member made it anonymous.
describe('Kunena edit: an edit does not make a post anonymous by itself', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};

  const addMessage = (parent, hold, userid, name, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, ${ids.category}, '${name}', ${userid}, '', 'Cypress anonymous-edit ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, ${hold}, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`).then(() => message.insertId));

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, review, allowAnonymous, postAnonymous, params, iconset) VALUES (1, 'Cypress anonymous-edit ${stamp}', 'cypress-anonymous-edit-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, 0, 1, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-anonymous-edit-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Cypress anonymous-edit ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return addMessage(0, 0, 797, 'ci-admin', 'The question.');
    }).then((first) => {
      ids.first = first;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${first}, last_post_id = ${first} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 1, numPosts = 1 WHERE id = ${ids.category}`);

      // A reply of ci-user.
      return addMessage(first, 0, 798, 'ci-user', `The reply ${stamp}.`);
    }).then((reply) => {
      ids.reply = reply;
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_inbox WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-anonymous-edit-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.kunena_fixUserCounters();
  });

  const edit = (anonymous) => cy.then(() => cy.request(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.reply}`)).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];
    const body = { mesid: ids.reply, subject: `Cypress anonymous-edit ${stamp}`, message: `The reply ${stamp}, edited.`, [token]: '1' };

    if (anonymous) {
      body.anonymous = 1;
    }

    return cy.request({ method: 'POST', url: `${base}&view=topic&task=edit`, form: true, body, followRedirect: false, failOnStatusCode: false });
  });

  const author = () => cy.task('queryDB', `SELECT m.userid, x.message FROM #__kunena_messages m JOIN #__kunena_messages_text x ON x.mesid = m.id WHERE m.id = ${ids.reply}`).then((rows) => rows[0]);

  it('does not tick "anonymous" in the edit form of a post of a member', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.then(() => cy.request(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.reply}`)).then((page) => {
      const box = /<input[^>]*name="anonymous"[^>]*>/.exec(page.body);

      expect(box, 'the box of the anonymous post').to.not.eq(null);
      expect(box[0], 'the box of the anonymous post').to.not.contain('checked');
    });
  });

  it('keeps the name of the member on his post when a moderator edits it with the box ticked', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    edit(true);
    author().then((row) => {
      expect(row.message, 'the edit is saved').to.eq(`The reply ${stamp}, edited.`);
      expect(Number(row.userid), 'the author').to.eq(798);
    });
  });

  it('makes the post anonymous when its author asks it', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    edit(true);
    author().then((row) => {
      expect(row.message, 'the edit is saved').to.eq(`The reply ${stamp}, edited.`);
      expect(Number(row.userid), 'the author').to.eq(0);
    });
  });
});
