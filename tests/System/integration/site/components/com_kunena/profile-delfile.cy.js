// The list of the files of a user in the profile deletes the chosen file of the user (task user.delfile): that one goes, the
// other file of the same message stays, and the topic counts one file less. (The other one has the id 1 on purpose: the list
// of the attachments to remove from the message was [0, 1], the keys of an array, not the id of the chosen file.)
// The files are not on disk (a folder of no files), the attachment rows only.
describe('Kunena profile: delete a file of the user', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const stamp = Date.now();
  const ids = {};
  const attach = (name, id) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_attachments (${id ? 'id, ' : ''}mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${id ? `${id}, ` : ''}${ids.message}, ${user.id}, 0, '', 10, 'media/kunena/attachments/cypress-${stamp}', 'text/plain', 'cypress-${name}-${stamp}.txt', 'cypress-${name}.txt', '', 0, 0)`,
  ).then((row) => {
    ids[name] = id || row.insertId;
  });
  const exists = (name) => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_attachments WHERE id = ${ids[name]}`).then((rows) => Number(rows[0].n) === 1);

  beforeEach(() => {
    cy.task('queryDB', 'SELECT COUNT(*) AS n FROM #__kunena_attachments WHERE id = 1').then((rows) => expect(Number(rows[0].n), 'no attachment 1 on the test site').to.eq(0));
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress delfile ${stamp}', 'cypress-delfile-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-delfile-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Delfile ${stamp}', 0, 0, 0, 0, 0, 1, 0, 2, 0, 0, 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, 'ci-user', ${user.id}, '', 'Delfile ${stamp}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Two files.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      attach('kept', 1);
      attach('chosen');
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-delfile-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.kunena_fixUserCounters();
  });

  it('deletes the chosen file only and counts it off the topic', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=user&layout=attachments&userid=${user.id}`).then((page) => {
      expect(page.body, 'the checkbox of the chosen file').to.contain(`value="${ids.chosen}"`);
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({ method: 'POST', url: `${base}&view=user`, form: true, body: { task: 'delfile', 'cid[]': ids.chosen, [token]: '1' }, followRedirect: false });
    });
    cy.then(() => exists('chosen')).should('eq', false);
    cy.then(() => exists('kept')).should('eq', true);
    cy.task('queryDB', `SELECT attachments FROM #__kunena_topics WHERE id = ${ids.topic}`).then((rows) => expect(Number(rows[0].attachments), 'the files of the topic').to.eq(1));
  });
});
