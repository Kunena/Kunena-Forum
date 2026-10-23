// The private part of a message takes the files which the user uploaded for it (attachment_private). The numbers come from the
// form: a number of a file of somebody else must not be taken. Before, such a file became a private file of the user's message:
// it was gone for the readers of its own message, and the user himself became one of the users who may read it.
describe('Kunena attachments: the files of the private part are the own files', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const folder = `media/kunena/attachments/cypress-foreign-${stamp}`;
  const root = 'E:/www/apache/htdocs/tests';
  const ids = {};
  let protection;

  const file = (id) => cy.request({ url: `${base}&view=attachment&id=${id}&format=raw`, failOnStatusCode: false }).then((response) => String(response.body));

  before(() => {
    cy.kunena_setOption('attachmentProtection', '1').then((value) => {
      protection = value;
    });
  });

  after(() => {
    cy.kunena_setOption('attachmentProtection', protection ?? '0');
  });

  beforeEach(() => {
    // A category which the user may not read, with a message of the administrator and its (protected) file.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress foreign ${stamp}', 'cypress-foreign-${stamp}', '', '', '', '', '', 'joomla.level', 3, 3, 1, 8, 1, 90, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-foreign-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, 'Cypress foreign topic ${stamp}', 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, ${ids.category}, 'ci-admin', ${admin}, '', 'Cypress foreign topic ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A hidden message.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      cy.writeFile(`${root}/${folder}/secret${stamp}.txt`, `the secret content ${stamp}`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${message.insertId}, ${admin}, 1, 'secret', 30, '${folder}', 'text/plain', 'secret${stamp}.txt', 'secret${stamp}.txt', '', 0, 0)`,
      );
    }).then((attachment) => {
      ids.attachment = attachment.insertId;
    });
  });

  afterEach(() => {
    // The replies of the test (the user is the starter of topic 9: a reply with another subject would rename the topic).
    cy.task('queryDB', `SELECT id FROM #__kunena_messages WHERE thread = 9 AND id > 11`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `SELECT private_id FROM #__kunena_private_post_map WHERE message_id = ${id}`).then((maps) => maps.forEach(({ private_id: privateId }) => {
          cy.task('queryDB', `DELETE FROM #__kunena_private_attachment_map WHERE private_id = ${privateId}`);
          cy.task('queryDB', `DELETE FROM #__kunena_private_user_map WHERE private_id = ${privateId}`);
          cy.task('queryDB', `DELETE FROM #__kunena_private_post_map WHERE private_id = ${privateId}`);
          cy.task('queryDB', `DELETE FROM #__kunena_private WHERE id = ${privateId}`);
        }));
        cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_private_attachment_map WHERE attachment_id = ${ids.attachment}`);
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE folder = '${folder}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id IN (${ids.topic}, 9) AND user_id = ${user.id} AND posts = 0`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-foreign-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.task('deleteRelativePath', folder);
    // The public topic of the test site gets its counters and its last post back.
    cy.task('queryDB', "UPDATE #__kunena_topics t JOIN #__kunena_messages m ON m.id = 11 SET t.subject = 'Which template do you use?', t.posts = 2, t.last_post_id = 11, t.last_post_userid = 798, t.last_post_time = m.time WHERE t.id = 9");
    cy.task('queryDB', 'UPDATE #__kunena_users u SET posts = (SELECT COUNT(*) FROM #__kunena_messages m WHERE m.userid = u.userid AND m.hold = 0) WHERE u.userid = 798');
    cy.task('queryDB', 'UPDATE #__kunena_user_topics SET posts = 2, last_post_id = 11 WHERE user_id = 798 AND topic_id = 9');
  });

  it('does not take the file of another user into the private part of a reply', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    file(ids.attachment).then((body) => expect(body, 'the file before').to.not.include(`the secret content ${stamp}`));

    // A reply in a public topic, with a private part which names the file of the hidden message.
    cy.request(`${base}&view=topic&layout=reply&catid=3&id=9`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: {
          catid: 3,
          id: 9,
          parentid: 10,
          subject: 'Re: Which template do you use?',
          message: 'A reply with a private part.',
          message_private: 'A private part.',
          'attachment_private[0]': String(ids.attachment),
          [token]: '1',
        },
        followRedirect: false,
        failOnStatusCode: false,
      });
    });

    cy.task('queryDB', `SELECT mesid, protected FROM #__kunena_attachments WHERE id = ${ids.attachment}`).then((rows) => {
      expect(Number(rows[0].protected), 'the protection of the file of the hidden message').to.eq(1);
    });
    cy.task('queryDB', `SELECT private_id FROM #__kunena_private_attachment_map WHERE attachment_id = ${ids.attachment}`).then((rows) => {
      expect(rows, 'a private part with the file of somebody else').to.have.length(0);
    });
    file(ids.attachment).then((body) => expect(body, 'the file afterwards').to.not.include(`the secret content ${stamp}`));
  });

  it('does not take the upload of another user (a file in no message yet) into a reply', () => {
    // The administrator has uploaded a file for a message which he has not sent yet.
    cy.writeFile(`${root}/${folder}/pending${stamp}.txt`, `the pending content ${stamp}`);
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (0, ${admin}, 0, 'pending', 30, '${folder}', 'text/plain', 'pending${stamp}.txt', 'pending${stamp}.txt', '', 0, 0)`,
    ).then((attachment) => {
      ids.pending = attachment.insertId;
    });

    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=topic&layout=reply&catid=3&id=9`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: {
          catid: 3,
          id: 9,
          parentid: 10,
          subject: 'Re: Which template do you use?',
          message: 'A reply which names the upload of somebody else.',
          [`attachments[${ids.pending}]`]: '1',
          [`attachment[${ids.pending}]`]: '1',
          [token]: '1',
        },
        followRedirect: false,
        failOnStatusCode: false,
      });
    });

    cy.then(() => cy.task('queryDB', `SELECT mesid FROM #__kunena_attachments WHERE id = ${ids.pending}`)).then((rows) => {
      expect(Number(rows[0].mesid), 'the message of the upload of the administrator').to.eq(0);
    });
  });
});
