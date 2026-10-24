// The tool "Prune categories" of the backend (task tools.prune), moving to the trash: in the chosen category the topics without a
// new post for more than the days are deleted (hold 2), a recent topic stays, and a sticky topic stays when "keep sticky" is on.
describe('Kunena backend tools: prune', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};

  const addTopic = (name, ordering, daysAgo) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, 'Prune ${name} ${stamp}', 0, 0, 0, 0, ${ordering}, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - ${daysAgo} * 86400, 797, 'x', '', 0, UNIX_TIMESTAMP() - ${daysAgo} * 86400, 797, 'x', '', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, 'ci-admin', 797, '', 'Prune ${name} ${stamp}', UNIX_TIMESTAMP() - ${daysAgo} * 86400, '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
  )).then(() => {
    ids[name] = topic.insertId;
  })));
  const hold = (name) => cy.task('queryDB', `SELECT hold FROM #__kunena_topics WHERE id = ${ids[name]}`).then((rows) => Number(rows[0].hold));

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress prune ${stamp}', 'cypress-prune-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-prune-${stamp}', 'catid', ${category.insertId}, 0)`);
    });
    cy.then(() => addTopic('old', 0, 400));
    cy.then(() => addTopic('recent', 0, 10));
    cy.then(() => addTopic('sticky', 1, 400));
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 3, numPosts = 3 WHERE id = ${ids.category}`));
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${ids.category}`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    // The tag map has no id: the support does not remove its rows (the topic may be gone already).
    cy.task('queryDB', `DELETE FROM #__contentitem_tag_map WHERE type_alias = 'com_kunena.topic' AND content_item_id = ${ids.old}`);
    cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-prune-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.kunena_fixUserCounters();
  });

  it('moves the old topics to the trash and keeps a recent and a sticky topic', () => {
    cy.doAdministratorLogin();
    cy.request(`${admin}&view=tools&layout=prune`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${admin}&task=tools.prune`,
        form: true,
        body: { 'prune_forum[]': ids.category, pruneDays: 365, controlOptions: 'all', keepsticky: 1, trashDelete: 0, [token]: '1' },
        followRedirect: false,
      });
    });
    hold('old').should('eq', 2);
    hold('recent').should('eq', 0);
    hold('sticky').should('eq', 0);
  });

  it('deletes the old topics for ever with their messages, ratings and tags', () => {
    const count = (sql) => cy.task('queryDB', sql).then((rows) => Number(rows[0].n));
    cy.then(() => cy.task('queryDB', `INSERT INTO #__kunena_rate (topic_id, userid, rate, time) VALUES (${ids.old}, 798, 4, NOW())`));
    cy.then(() => cy.task('queryDB', `INSERT INTO #__contentitem_tag_map (type_alias, core_content_id, content_item_id, tag_id, type_id) VALUES ('com_kunena.topic', 0, ${ids.old}, 1, 0)`));
    cy.doAdministratorLogin();
    cy.request(`${admin}&view=tools&layout=prune`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${admin}&task=tools.prune`,
        form: true,
        body: { 'prune_forum[]': ids.category, pruneDays: 365, controlOptions: 'all', keepsticky: 1, trashDelete: 1, [token]: '1' },
        followRedirect: false,
      });
    });
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_topics WHERE id = ${ids.old}`)).should('eq', 0);
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_messages WHERE thread = ${ids.old}`)).should('eq', 0);
    hold('recent').should('eq', 0);
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_rate WHERE topic_id = ${ids.old}`)).should('eq', 0);
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__contentitem_tag_map WHERE type_alias = 'com_kunena.topic' AND content_item_id = ${ids.old}`)).should('eq', 0);
  });

  it('deletes the files of the deleted topics, and keeps a file which is being uploaded', () => {
    const count = (sql) => cy.task('queryDB', sql).then((rows) => Number(rows[0].n));
    const folder = `media/kunena/attachments/cypress-prune-${stamp}`;
    const root = 'E:/www/apache/htdocs/tests';
    // A file of the message of the old topic, and a file of ci-user for a message which is not posted yet (mesid 0).
    ['old', 'pending'].forEach((name) => {
      cy.writeFile(`${root}/${folder}/${name}-${stamp}.txt`, `the file ${name}`);
      cy.then(() => cy.task(
        'queryDB',
        `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${name === 'old' ? `(SELECT first_post_id FROM #__kunena_topics WHERE id = ${ids.old})` : 0}, 798, 0, '', 10, '${folder}', 'text/plain', '${name}-${stamp}.txt', '${name}.txt', '', 0, 0)`,
      ));
    });
    cy.doAdministratorLogin();
    cy.request(`${admin}&view=tools&layout=prune`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${admin}&task=tools.prune`,
        form: true,
        body: { 'prune_forum[]': ids.category, pruneDays: 365, controlOptions: 'all', keepsticky: 1, trashDelete: 1, [token]: '1' },
        followRedirect: false,
      });
    });
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_attachments WHERE filename = 'old-${stamp}.txt'`)).should('eq', 0);
    cy.readFile(`${root}/${folder}/old-${stamp}.txt`).should('not.exist');
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_attachments WHERE filename = 'pending-${stamp}.txt'`)).should('eq', 1);
    cy.readFile(`${root}/${folder}/pending-${stamp}.txt`).should('exist');
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE folder = '${folder}'`);
    cy.task('deleteRelativePath', folder);
  });
});
