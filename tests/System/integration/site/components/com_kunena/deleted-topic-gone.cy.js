// Deleted topics answer with 410 Gone (option redirectDeletedTopics): the deletion of a topic makes an entry with the status 410 in
// the redirects of Joomla for its address, the address then answers 410 instead of 401/403/404, and a topic which is restored loses
// its entry and is shown again. Without the option nothing is made.
describe('Kunena deleted topics: 410 Gone', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Gone topic ${stamp}`;
  const ids = {};
  const old = {};

  const task = (name) => cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ url: `${base}&view=topic&catid=${ids.category}&id=${ids.topic}&task=${name}&${token}=1`, failOnStatusCode: false, followRedirect: false });
  });
  const entries = () => cy.task('queryDB', `SELECT old_url, header FROM #__redirect_links WHERE comment = 'Kunena: deleted topic' AND old_url LIKE '%${ids.topic}%'`);

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress gone ${stamp}', 'cypress-gone-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-gone-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, 'ci-admin', 797, '', '${subject}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Soon gone.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 1, numPosts = 1 WHERE id = ${ids.category}`);
    });
  });

  afterEach(() => {
    if ('redirectDeletedTopics' in old) {
      cy.kunena_setOption('redirectDeletedTopics', old.redirectDeletedTopics === undefined ? null : old.redirectDeletedTopics);
      delete old.redirectDeletedTopics;
    }
    cy.then(() => cy.task('queryDB', `DELETE FROM #__redirect_links WHERE comment = 'Kunena: deleted topic' AND old_url LIKE '%${ids.topic}%'`));
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-gone-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.kunena_fixUserCounters();
  });

  it('answers 410 for a deleted topic and shows it again when it is restored', () => {
    cy.kunena_setOption('redirectDeletedTopics', '1').then((value) => {
      old.redirectDeletedTopics = value;
    });
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.then(() => task('delete'));
    entries().then((rows) => {
      expect(rows, 'the entry of the redirects').to.have.length(1);
      expect(Number(rows[0].header), 'the status of the entry').to.eq(410);

      // A visitor asks for the address of the deleted topic.
      cy.clearAllCookies();
      cy.request({ url: rows[0].old_url, failOnStatusCode: false }).its('status').should('eq', 410);
    });

    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.then(() => task('undelete'));
    entries().should('have.length', 0);
    cy.clearAllCookies();
    cy.then(() => cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`)).its('body').should('include', subject);
  });

  it('makes no entry when the option is off', () => {
    cy.kunena_setOption('redirectDeletedTopics', '0').then((value) => {
      old.redirectDeletedTopics = value;
    });
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.then(() => task('delete'));
    entries().should('have.length', 0);
  });
});
