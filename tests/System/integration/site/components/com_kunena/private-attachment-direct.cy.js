// A file of the private part of a message is for the users of that part and the moderators. The editor uploads the file and asks
// the forum to make it private (setprivate), before the message (and so its private part) exists: the forum refused that, and the
// file stayed a normal file. Every visitor got it through the forum (view=attachment), and without the protection of the
// attachments (the default) at its direct address too: media/kunena/attachments/<user>/<the name of the upload>.
describe('Kunena private attachments: the file of the private part of a new reply', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const name = `secret${stamp}.txt`;
  const content = `secret text ${stamp}`;
  const ids = {};
  let protection;

  before(() => {
    cy.kunena_setOption('attachmentProtection', '0').then((value) => {
      protection = value;
    });
  });

  after(() => {
    cy.kunena_setOption('attachmentProtection', protection === undefined ? null : protection);
  });

  afterEach(() => {
    // The replies of the test (the user is the starter of topic 9: a reply with another subject would rename the topic).
    cy.task('queryDB', 'SELECT id FROM #__kunena_messages WHERE thread = 9 AND id > 11').then((rows) => {
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
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE userid = ${user.id} AND (mesid = 0 OR mesid > 11)`);
    cy.task('deleteRelativePath', `media/kunena/attachments/${user.id}`);
    // The public topic of the test site gets its counters and its last post back.
    cy.task('queryDB', "UPDATE #__kunena_topics t JOIN #__kunena_messages m ON m.id = 11 SET t.subject = 'Which template do you use?', t.posts = 2, t.last_post_id = 11, t.last_post_userid = 798, t.last_post_time = m.time WHERE t.id = 9");
    cy.task('queryDB', 'UPDATE #__kunena_users u SET posts = (SELECT COUNT(*) FROM #__kunena_messages m WHERE m.userid = u.userid AND m.hold = 0) WHERE u.userid = 798');
    cy.task('queryDB', 'UPDATE #__kunena_user_topics SET posts = 2, last_post_id = 11 WHERE user_id = 798 AND topic_id = 9');
    cy.kunena_fixCategoryCounters();
  });

  it('gives the file to nobody else, through the forum or at a direct address', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=topic&layout=reply&catid=3&id=9`).then((page) => {
      ids.token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=upload&format=json&${ids.token}=1`,
        qs: { catid: 3, filename: name, size: content.length, mime: 'text/plain', hash: 'abc', chunkStart: 0, chunkEnd: content.length },
        headers: { 'Content-Type': 'application/octet-stream' },
        body: content,
      });
    }).then((response) => {
      ids.attachment = response.body.data.id;

      // The editor makes the file private, then sends the reply with the file in its private part.
      cy.request({ url: `${base}&view=topic&task=setprivate&format=json&${ids.token}=1&files_id=${encodeURIComponent(JSON.stringify([ids.attachment]))}`, failOnStatusCode: false });
      cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: {
          catid: 3,
          id: 9,
          parentid: 11,
          subject: 'Re: Which template do you use?',
          message: 'A reply with a private file.',
          message_private: 'A private part.',
          'attachment_private[0]': String(ids.attachment),
          [ids.token]: '1',
        },
        followRedirect: false,
        failOnStatusCode: false,
      });
    });

    // The stored file has a name which nobody can guess (like the files of the protection of the attachments), which the
    // pages never show: a private file is always linked through the forum.
    cy.then(() => cy.task('queryDB', `SELECT filename, filename_real FROM #__kunena_attachments WHERE id = ${ids.attachment}`)).then((rows) => {
      expect(rows[0].filename, 'the stored name').to.match(/^[0-9a-f]{32}$/);
      expect(rows[0].filename_real, 'the name of the upload').to.eq(name);
    });

    // A visitor: through the forum, and at the address with the name of the upload.
    cy.clearAllCookies();
    [`${base}&view=attachment&id=${ids.attachment}&format=raw`, `/media/kunena/attachments/${user.id}/${name}`].forEach((url) => {
      cy.then(() => cy.request({ url, failOnStatusCode: false })).then((response) => {
        expect(String(response.body), `the file at ${url}`).not.to.contain(content);
      });
    });

    // The author still opens it through the forum.
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => cy.request(`${base}&view=attachment&id=${ids.attachment}&format=raw`)).its('body').should('contain', content);
  });
});
