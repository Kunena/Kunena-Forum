// An anonymous post hides its author: the message has no user. Its files kept the folder of their uploader and the name of the
// upload: without the protection of the attachments (the default) every reader of the post got the address
// media/kunena/attachments/<the id of the author>/<file>, and the list of the files of the message (for the editor) gave the folder.
// The file of an anonymous post gets a name which nobody can guess and goes through the forum, like a private file.
describe('Kunena anonymous posts: the files do not name the author', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const name = `anonfile${stamp}.txt`;
  const content = `anonymous text ${stamp}`;
  const ids = {};
  let protection;

  before(() => {
    cy.kunena_setOption('attachmentProtection', '0').then((value) => {
      protection = value;
    });
    cy.task('queryDB', 'UPDATE #__kunena_categories SET allowAnonymous = 1 WHERE id = 3');
  });

  after(() => {
    cy.task('queryDB', 'UPDATE #__kunena_categories SET allowAnonymous = 0 WHERE id = 3');
    cy.kunena_setOption('attachmentProtection', protection === undefined ? null : protection);
  });

  afterEach(() => {
    // The replies of the test (the user is the starter of topic 9: a reply with another subject would rename the topic).
    cy.task('queryDB', 'SELECT id FROM #__kunena_messages WHERE thread = 9 AND id > 11').then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE userid = ${user.id} AND (mesid = 0 OR mesid > 11)`);
    cy.task('deleteRelativePath', `media/kunena/attachments/${user.id}`);
    // The public topic of the test site gets its counters and its last post back.
    cy.task('queryDB', "UPDATE #__kunena_topics t JOIN #__kunena_messages m ON m.id = 11 SET t.subject = 'Which template do you use?', t.posts = 2, t.attachments = 0, t.last_post_id = 11, t.last_post_userid = 798, t.last_post_time = m.time WHERE t.id = 9");
    cy.task('queryDB', 'DELETE FROM #__kunena_user_topics WHERE user_id = 0 AND topic_id = 9');
    cy.task('queryDB', 'UPDATE #__kunena_user_topics SET posts = 2, last_post_id = 11 WHERE user_id = 798 AND topic_id = 9');
    cy.kunena_fixCategoryCounters();
  });

  // The user uploads the file and sends a reply with it (anonymous or not); the id of the reply is in ids.message.
  const reply = (anonymous) => {
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

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: {
          catid: 3,
          id: 9,
          parentid: 11,
          subject: 'Re: Which template do you use?',
          message: `A reply with a file ${stamp}.`,
          anonymous: anonymous ? 1 : 0,
          authorname: anonymous ? `Somebody${stamp}` : user.name,
          [`attachments[${ids.attachment}]`]: '1',
          [`attachment[${ids.attachment}]`]: '1',
          [ids.token]: '1',
        },
        followRedirect: false,
        failOnStatusCode: false,
      });
    });

    return cy.then(() => cy.task('queryDB', `SELECT mesid FROM #__kunena_attachments WHERE id = ${ids.attachment}`)).then((rows) => {
      ids.message = rows[0].mesid;
    });
  };

  // The file is stored under a name which nobody can guess, and a visitor finds no address with the folder of the author: on the
  // page of the reply, in the list of its files, and at the address with the name of the upload.
  const hidden = () => {
    cy.then(() => cy.task('queryDB', `SELECT m.userid, a.filename, a.filename_real FROM #__kunena_attachments AS a INNER JOIN #__kunena_messages AS m ON m.id = a.mesid WHERE a.id = ${ids.attachment}`)).then((rows) => {
      expect(rows, 'the file is in the reply').to.have.length(1);
      expect(rows[0].userid, 'the reply is anonymous').to.eq(0);
      expect(rows[0].filename, 'the stored name').to.match(/^[0-9a-f]{32}$/);
      expect(rows[0].filename_real, 'the name of the upload').to.eq(name);
    });

    cy.clearAllCookies();
    cy.then(() => cy.request(`${base}&view=topic&catid=3&id=9&mesid=${ids.message}`)).then((page) => {
      expect(page.body, 'the reply').to.contain(`A reply with a file ${stamp}.`);
      expect(page.body, 'the folder of the author').not.to.contain(`attachments/${user.id}/`);
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request(`${base}&view=topic&task=loadattachments&format=json&mes_id=${ids.message}&${token}=1`);
    }).then((response) => {
      const text = typeof response.body === 'string' ? response.body : JSON.stringify(response.body);

      expect(text, 'the file is in the list').to.contain(String(ids.attachment));
      expect(text.replace(/\\\//g, '/'), 'the folder of the author in the list').not.to.contain(`attachments/${user.id}`);
    });
    cy.then(() => cy.request({ url: `/media/kunena/attachments/${user.id}/${name}`, failOnStatusCode: false })).then((response) => {
      expect(String(response.body), 'the file at the address with the name of the upload').not.to.contain(content);
    });
  };

  it('gives the readers of an anonymous reply no address with the folder of the author', () => {
    reply(true);
    hidden();
  });

  it('gives no address with the folder of the author when the reply is made anonymous later', () => {
    reply(false);
    cy.then(() => cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=edit`,
      form: true,
      body: {
        mesid: ids.message,
        subject: 'Re: Which template do you use?',
        message: `A reply with a file ${stamp}.`,
        anonymous: 1,
        authorname: `Somebody${stamp}`,
        [`attachments[${ids.attachment}]`]: '1',
        [`attachment[${ids.attachment}]`]: '1',
        [ids.token]: '1',
      },
      followRedirect: false,
      failOnStatusCode: false,
    }));
    hidden();
  });
});
