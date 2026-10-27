// A post which its author makes anonymous by an edit hides who wrote it. The edit sent the e-mails of the new mentions before the
// post was made anonymous: the mentioned user got "<the name of the author> mentioned you", with the name which the post hides.
describe('Kunena anonymous posts: the mention e-mail of an edit which makes the post anonymous', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const author = { name: Cypress.expose('username'), password: Cypress.expose('password'), id: 797 };
  const mentioned = 798;
  const site = 'E:/www/apache/htdocs/tests';
  const ids = {};
  let mentionEmail;
  let configuration;

  const joomla = (args) => cy.task('joomlaCli', args);

  before(() => {
    // The mails of the site go to the mail server of the tests (nothing leaves the computer). The configuration of Joomla is
    // written back as it was after the test (the console writes the values in quotes and makes the file read-only).
    cy.readFile(`${site}/configuration.php`).then((text) => {
      configuration = text;
    });
    cy.task('startMailServer');
    joomla(['config:set', 'mailer=smtp', 'smtphost=localhost', 'smtpport=1025', 'smtpauth=0', 'smtpsecure=none']);
    cy.readFile(`${site}/configuration.php`).should('contain', "public $mailer = 'smtp';").and('match', /public \$smtpport = '?1025'?;/);
    cy.kunena_setOption('mentionEmail', '1').then((value) => {
      mentionEmail = value;
    });
    cy.task('queryDB', 'UPDATE #__kunena_categories SET allowAnonymous = 1 WHERE id = 3');
  });

  after(() => {
    cy.task('queryDB', 'UPDATE #__kunena_categories SET allowAnonymous = 0 WHERE id = 3');
    cy.kunena_setOption('mentionEmail', mentionEmail === undefined ? null : mentionEmail);
    cy.then(() => {
      if (configuration) {
        cy.task('writeRelativeFile', { path: 'configuration.php', content: configuration, mode: 0o666 });
      }
    });
  });

  beforeEach(() => {
    // A reply of the author in the public topic of the test site.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (11, 9, 3, '${author.name}', ${author.id}, '', 'Re: Which template do you use?', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A reply ${stamp}.')`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('queryDB', 'DELETE FROM #__kunena_user_topics WHERE user_id = 0 AND topic_id = 9');
    cy.task('queryDB', "UPDATE #__kunena_topics t JOIN #__kunena_messages m ON m.id = 11 SET t.posts = 2, t.last_post_id = 11, t.last_post_userid = 798, t.last_post_time = m.time WHERE t.id = 9");
    cy.task('queryDB', `UPDATE #__kunena_users u SET posts = (SELECT COUNT(*) FROM #__kunena_messages m WHERE m.userid = u.userid AND m.hold = 0) WHERE u.userid = ${author.id}`);
    cy.kunena_fixCategoryCounters();
  });

  it('does not name the author in the e-mail to the mentioned user', () => {
    cy.doFrontendLogin(author.name, author.password, false);
    cy.then(() => cy.request(`${base}&view=topic&layout=edit&catid=3&id=9&mesid=${ids.message}`)).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      cy.task('clearEmails');

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=edit`,
        form: true,
        body: {
          mesid: ids.message,
          subject: 'Re: Which template do you use?',
          message: `A reply ${stamp}, [mention userid=${mentioned}]ci-user[/mention]`,
          anonymous: 1,
          authorname: `Somebody${stamp}`,
          [token]: '1',
        },
        followRedirect: false,
        failOnStatusCode: false,
      });
    });

    cy.then(() => cy.task('queryDB', `SELECT userid FROM #__kunena_messages WHERE id = ${ids.message}`)).then((rows) => {
      expect(rows[0].userid, 'the reply is anonymous').to.eq(0);
    });
    cy.task('getMails').then((mails) => {
      expect(mails, 'the mention e-mail').to.have.length(1);
      const mail = mails[0];
      const text = `${mail.headers.subject} ${mail.body} ${mail.html || ''}`;

      expect(text, 'the name of the anonymous post').to.contain(`Somebody${stamp}`);
      expect(text, 'the name of the author').not.to.contain(author.name);
    });
  });
});
