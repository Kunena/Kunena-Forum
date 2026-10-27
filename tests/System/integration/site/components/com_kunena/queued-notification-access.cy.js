// The e-mails of the subscriptions are queued when a message is posted, and the scheduled task sends them later. The task sent the
// queue as it was: a message which a moderator deleted in the meantime (spam), or moved to a category which the subscriber may not
// read, still went to him with its text. The task sends a message now only to the users who may read it when the e-mail is sent.
describe('Kunena notifications: the queue sends only what the user may read', () => {
  const stamp = Date.now();
  const site = 'E:/www/apache/htdocs/tests';
  const recipient = { id: 798, email: 'ci-user@example.com' };
  const ids = {};
  let configuration;

  const joomla = (args) => cy.task('joomlaCli', args);

  // A message of the administrator in the topic and its category, with the state (hold) given, and its e-mail in the queue.
  const addMessage = (name, thread, catid, hold) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${thread}, ${catid}, 'ci-admin', 797, '', 'Queued ${name} ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, ${hold}, 0, 0, 0, 0, 0, '')`,
  ).then((message) => {
    ids[name] = message.insertId;
    cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'The text of ${name} ${stamp}')`);
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_notifications_mailsqueue (subject, messageId, url, emailListJson, categoryName, once, sentusers, send) VALUES ('Queued ${name} ${stamp}', ${message.insertId}, 'http://localhost/tests/index.php', '[{"id":${recipient.id},"email":"${recipient.email}","subscription":1,"moderator":0}]', 'x', '0', '[]', 0)`,
    );
  });

  before(() => {
    // The mails of the site go to the mail server of the tests (nothing leaves the computer). The configuration of Joomla is
    // written back as it was after the test (the console writes the values in quotes and makes the file read-only).
    cy.readFile(`${site}/configuration.php`).then((text) => {
      configuration = text;
    });
    cy.task('startMailServer');
    joomla(['config:set', 'mailer=smtp', 'smtphost=localhost', 'smtpport=1025', 'smtpauth=0', 'smtpsecure=none']);
    cy.readFile(`${site}/configuration.php`).should('contain', "public $mailer = 'smtp';").and('match', /public \$smtpport = '?1025'?;/);
  });

  after(() => {
    cy.then(() => {
      if (configuration) {
        cy.task('writeRelativeFile', { path: 'configuration.php', content: configuration, mode: 0o666 });
      }
    });
  });

  beforeEach(() => {
    // The e-mails which other tests left in the queue wait aside (send 2): the task sends only the e-mails of this test.
    cy.task('queryDB', 'UPDATE #__kunena_notifications_mailsqueue SET send = 2 WHERE send = 0');

    // A category for the managers only (access level Special).
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress queue closed ${stamp}', 'cypress-queue-closed-${stamp}', '', '', '', '', '', 'joomla.level', 3, 1, 1, 8, 1, 98, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
    });

    addMessage('open', 9, 3, 0);
    addMessage('deleted', 9, 3, 2);
    // The topic which was moved to the closed category.
    cy.then(() => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, 'Queued closed ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    )).then((topic) => {
      ids.topic = topic.insertId;
      addMessage('closed', topic.insertId, ids.category, 0);
    });
  });

  afterEach(() => {
    ['open', 'deleted', 'closed'].filter((name) => ids[name]).forEach((name) => {
      cy.task('queryDB', `DELETE FROM #__kunena_notifications_mailsqueue WHERE messageId = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids[name]}`);
      cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids[name]}`);
    });
    cy.then(() => {
      if (ids.topic) {
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
      }

      if (ids.category) {
        cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
      }
    });
    cy.task('queryDB', 'UPDATE #__kunena_notifications_mailsqueue SET send = 0 WHERE send = 2');
  });

  it('sends the message which the user may read, not a deleted one or one of a closed category', () => {
    cy.task('clearEmails');
    cy.task('joomlaCli', ['scheduler:run', '--id=6'], { timeout: 120000 });

    cy.task('getMails').then((mails) => {
      const subjects = mails.map((mail) => mail.headers.subject).join(' | ');
      const text = mails.map((mail) => `${mail.body} ${mail.html || ''}`).join(' ');

      expect(subjects, 'the readable message').to.contain(`Queued open ${stamp}`);
      expect(subjects, 'the deleted message').not.to.contain(`Queued deleted ${stamp}`);
      expect(subjects, 'the message of the closed category').not.to.contain(`Queued closed ${stamp}`);
      expect(text, 'the text of the deleted message').not.to.contain(`The text of deleted ${stamp}`);
      expect(text, 'the text of the message of the closed category').not.to.contain(`The text of closed ${stamp}`);
    });

    // The queue is done: the e-mails which are not sent are not sent later either.
    cy.then(() => cy.task('queryDB', `SELECT COUNT(*) AS waiting FROM #__kunena_notifications_mailsqueue WHERE send = 0 AND messageId IN (${ids.open}, ${ids.deleted}, ${ids.closed})`)).its('0.waiting').should('eq', 0);
  });
});
