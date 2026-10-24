// The trash of the backend (tasks trashs.restore and trashs.purge, type topics): a deleted topic is restored (published again),
// and a deleted topic which is purged is removed for ever with its message.
describe('Kunena backend trash: restore and purge topics', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};

  const addTopic = (name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, 'Trash ${name} ${stamp}', 0, 0, 0, 2, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, 'ci-admin', 797, '', 'Trash ${name} ${stamp}', UNIX_TIMESTAMP(), '', 0, 0, 2, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
  )).then(() => {
    ids[name] = topic.insertId;
    ids[`${name}Message`] = message.insertId;
  })));
  const send = (task, cid, type = 'topics') => cy.request(`${admin}&view=trashs`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ method: 'POST', url: `${admin}&task=trashs.${task}`, form: true, body: { 'cid[]': cid, type, [token]: '1' }, followRedirect: false });
  });
  const count = (sql) => cy.task('queryDB', sql).then((rows) => Number(rows[0].n));

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress trash ${stamp}', 'cypress-trash-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-trash-${stamp}', 'catid', ${category.insertId}, 0)`);
    });
    cy.then(() => addTopic('restore'));
    cy.then(() => addTopic('purge'));
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
    cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-trash-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.kunena_fixUserCounters();
  });

  it('restores a deleted topic and purges another one for ever', () => {
    cy.doAdministratorLogin();
    cy.then(() => send('restore', ids.restore));
    cy.then(() => count(`SELECT hold AS n FROM #__kunena_topics WHERE id = ${ids.restore}`)).then((n) => expect(n, 'the hold of the restored topic').to.eq(0));
    cy.then(() => send('purge', ids.purge));
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_topics WHERE id = ${ids.purge}`)).then((n) => expect(n, 'the purged topic').to.eq(0));
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_messages WHERE id = ${ids.purgeMessage}`)).then((n) => expect(n, 'the message of the purged topic').to.eq(0));
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_messages_text WHERE mesid = ${ids.purgeMessage}`)).then((n) => expect(n, 'the text of the purged message').to.eq(0));
  });

  it('purges a deleted reply for ever, with its text', () => {
    // A published topic with a deleted reply.
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_topics SET hold = 0 WHERE id = ${ids.restore}`));
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_messages SET hold = 0 WHERE id = ${ids.restoreMessage}`));
    cy.then(() => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${ids.restoreMessage}, ${ids.restore}, ${ids.category}, 'ci-user', 798, '', 'Re: trash ${stamp}', UNIX_TIMESTAMP(), '', 0, 0, 2, 0, 0, 0, 0, 0, '')`,
    )).then((reply) => {
      ids.reply = reply.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${reply.insertId}, 'A deleted reply.')`);
    });
    cy.doAdministratorLogin();
    cy.then(() => send('purge', ids.reply, 'messages'));
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_messages WHERE id = ${ids.reply}`)).then((n) => expect(n, 'the purged reply').to.eq(0));
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_messages_text WHERE mesid = ${ids.reply}`)).then((n) => expect(n, 'the text of the purged reply').to.eq(0));
  });

  it('keeps the file count of the topic when it purges a deleted reply without files', () => {
    // A published topic with a file in its first message, and a deleted reply without file.
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_topics SET hold = 0, attachments = 1 WHERE id = ${ids.restore}`));
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_messages SET hold = 0 WHERE id = ${ids.restoreMessage}`));
    cy.then(() => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${ids.restoreMessage}, 797, 0, '', 10, 'media/kunena/attachments/cypress-${stamp}', 'text/plain', 'cypress-${stamp}.txt', 'cypress.txt', '', 0, 0)`,
    ));
    cy.then(() => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${ids.restoreMessage}, ${ids.restore}, ${ids.category}, 'ci-user', 798, '', 'Re: trash ${stamp}', UNIX_TIMESTAMP(), '', 0, 0, 2, 0, 0, 0, 0, 0, '')`,
    )).then((reply) => {
      ids.reply = reply.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${reply.insertId}, 'A deleted reply.')`);
    });
    cy.doAdministratorLogin();
    cy.then(() => send('purge', ids.reply, 'messages'));
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_messages WHERE id = ${ids.reply}`)).then((n) => expect(n, 'the purged reply').to.eq(0));
    cy.then(() => count(`SELECT attachments AS n FROM #__kunena_topics WHERE id = ${ids.restore}`)).then((n) => expect(n, 'the files of the topic').to.eq(1));
    cy.then(() => cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE mesid = ${ids.restoreMessage}`));
  });

  it('restores a deleted topic when its first message is restored', () => {
    cy.doAdministratorLogin();
    cy.then(() => send('restore', ids.restoreMessage, 'messages'));
    cy.then(() => count(`SELECT hold AS n FROM #__kunena_messages WHERE id = ${ids.restoreMessage}`)).then((n) => expect(n, 'the hold of the restored message').to.eq(0));
    cy.then(() => count(`SELECT hold AS n FROM #__kunena_topics WHERE id = ${ids.restore}`)).then((n) => expect(n, 'the hold of its topic').to.eq(0));
  });

  it('lists the deleted messages when the request has a list limit only', () => {
    // A list[] without ordering left the ordering and the direction empty: "ORDER BY  " broke the query, the list was empty.
    cy.doAdministratorLogin();
    cy.request(`${admin}&view=trashs&list[limit]=0`).its('body').should('contain', `Trash restore ${stamp}`);
  });
});
