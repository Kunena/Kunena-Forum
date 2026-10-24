// Two clean-ups of the backend tools on the messages:
// - tools.purgeReStatements removes a reply prefix ("AW:" here, the messages of the test site start with "Re:") from the
//   start of the subjects, and leaves a subject that has it elsewhere;
// - tools.cleanupIP removes the IP of the messages older than N days, and keeps that of the newer ones.
describe('Kunena backend tools: clean up subjects and IP addresses', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = { messages: {} };
  const message = (key, subject, daysAgo) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, ${ids.category}, 'ci-user', 798, '', '${subject}', UNIX_TIMESTAMP() - ${daysAgo} * 86400, '192.0.2.${daysAgo}', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((result) => {
    ids.messages[key] = result.insertId;
    cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${result.insertId}, 'Cleanup ${key}.')`);
  });
  const row = (key) => cy.task('queryDB', `SELECT subject, ip FROM #__kunena_messages WHERE id = ${ids.messages[key]}`).then((rows) => rows[0]);
  const send = (task, body) => cy.request(`${admin}&view=users`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ method: 'POST', url: `${admin}&task=tools.${task}`, form: true, body: { ...body, [token]: '1' }, followRedirect: false });
  });

  beforeEach(() => {
    ids.messages = {};
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress cleanup ${stamp}', 'cypress-cleanup-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-cleanup-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Cleanup ${stamp}', 0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - 2000 * 86400, 798, 'x', '', 0, UNIX_TIMESTAMP(), 798, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;
      message('old', `AW: Cleanup ${stamp}`, 2000);
      message('recent', `AW: AW: Cleanup ${stamp}`, 10);
      message('inside', `About AW: Cleanup ${stamp}`, 10);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid IN (SELECT id FROM #__kunena_messages WHERE thread = ${ids.topic})`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-cleanup-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('removes the prefix from the start of the subjects only', () => {
    cy.task('queryDB', 'SELECT GROUP_CONCAT(subject ORDER BY id) AS s FROM #__kunena_messages WHERE id <= 11').then((rows) => {
      ids.siteSubjects = rows[0].s;
    });
    cy.doAdministratorLogin();
    cy.then(() => send('purgeReStatements', { reString: 'AW:' }));
    cy.then(() => row('old')).its('subject').should('eq', `Cleanup ${stamp}`);
    // One prefix: the second "AW:" stays.
    cy.then(() => row('recent')).its('subject').should('eq', `AW: Cleanup ${stamp}`);
    cy.then(() => row('inside')).its('subject').should('eq', `About AW: Cleanup ${stamp}`);
    // The "Re:" subjects of the test site are not touched.
    cy.task('queryDB', 'SELECT GROUP_CONCAT(subject ORDER BY id) AS s FROM #__kunena_messages WHERE id <= 11').then((rows) => expect(rows[0].s).to.eq(ids.siteSubjects));
  });

  it('removes the IP of the messages older than the days only', () => {
    cy.task('queryDB', 'SELECT COUNT(*) AS n FROM #__kunena_messages WHERE ip IS NULL').then((rows) => {
      ids.withoutIp = Number(rows[0].n);
    });
    cy.doAdministratorLogin();
    cy.then(() => send('cleanupIP', { cleanup_ip_days: 1000 }));
    cy.then(() => row('old')).its('ip').should('eq', null);
    cy.then(() => row('recent')).its('ip').should('eq', '192.0.2.10');
    // Only the old message lost its IP.
    cy.task('queryDB', 'SELECT COUNT(*) AS n FROM #__kunena_messages WHERE ip IS NULL').then((rows) => expect(Number(rows[0].n)).to.eq(ids.withoutIp + 1));
  });
});
