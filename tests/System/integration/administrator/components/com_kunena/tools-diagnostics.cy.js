// The diagnostics of the backend tools (view tools, layout diagnostics; task tools.diagnostics with delete=<check>): the page
// shows an orphan row as an issue, and the delete of that check removes it. The orphans point to ids which are no rows; the
// test site has no issues (the first test checks it), so the deletes touch the rows of the test only.
describe('Kunena backend tools: diagnostics', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const stamp = Date.now();
  const none = 999999900; // No topic, message or poll has this id or the next ones.
  const ids = {};
  const count = (sql) => cy.task('queryDB', sql).then((rows) => Number(rows[0].n));
  const page = () => cy.request(`${admin}&view=tools&layout=diagnostics`).its('body');
  const remove = (check) => cy.request(`${admin}&view=users`).then((users) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(users.body)[1];

    return cy.request({ url: `${admin}&task=tools.diagnostics&delete=${check}&${token}=1`, followRedirect: false });
  });
  // The text of the result of a check on the page ("TEST PASSED" or the issues).
  const result = (body, check) => {
    const text = body.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ');
    const start = text.indexOf(` ${check} `);

    return text.substring(start, start + 120);
  };

  beforeEach(() => {
    cy.doAdministratorLogin();
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE message = 'Diagnostics ${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE subject = 'Diagnostics ${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE filename = 'diagnostics-${stamp}.txt'`);
    cy.task('queryDB', `DELETE FROM #__kunena_polls_options WHERE text = 'Diagnostics ${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_thankyou WHERE postid = ${none + 2}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${none + 3}`);
  });

  it('finds no issue on the test site', () => {
    page().then((body) => {
      expect(body.match(/TEST PASSED/g) || [], 'the checks which pass').to.have.length.above(25);
      expect(body, 'a failed check').to.not.match(/TEST FAILED/i);
    });
  });

  it('removes an orphan message with its text', () => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${none}, 3, 'ci-user', 798, '', 'Diagnostics ${stamp}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Diagnostics ${stamp}')`));
    page().then((body) => expect(result(body, 'messageOrphaned')).to.match(/^ messageOrphaned TEST FAILED/));
    remove('messageOrphaned');
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_messages WHERE subject = 'Diagnostics ${stamp}'`)).should('eq', 0);
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_messages_text WHERE message = 'Diagnostics ${stamp}'`)).should('eq', 0);
  });

  it('removes the orphan text, file, poll option, thank you and user topic', () => {
    cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${none + 1}, 'Diagnostics ${stamp}')`);
    cy.task('queryDB', `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${none + 1}, 798, 0, '', 10, 'media/kunena/attachments/cypress-${stamp}', 'text/plain', 'diagnostics-${stamp}.txt', 'diagnostics.txt', '', 0, 0)`);
    cy.task('queryDB', `INSERT INTO #__kunena_polls_options (pollid, text, votes) VALUES (${none + 1}, 'Diagnostics ${stamp}', 0)`);
    cy.task('queryDB', `INSERT INTO #__kunena_thankyou (postid, userid, targetuserid, time) VALUES (${none + 2}, 798, 797, NOW())`);
    cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (798, ${none + 3}, 3, 1, 0, 0, 0, 0, '')`);
    page().then((body) => {
      ['messageBodyMissingMessage', 'attachmentOrphaned', 'pollOptionOrphaned', 'thankyouOrphaned', 'userTopicOrphaned'].forEach((check) => expect(result(body, check), check).to.match(new RegExp(`^ ${check} TEST FAILED`)));
    });
    ['messageBodyMissingMessage', 'attachmentOrphaned', 'pollOptionOrphaned', 'thankyouOrphaned', 'userTopicOrphaned'].forEach((check) => remove(check));
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_messages_text WHERE message = 'Diagnostics ${stamp}'`)).should('eq', 0);
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_attachments WHERE filename = 'diagnostics-${stamp}.txt'`)).should('eq', 0);
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_polls_options WHERE text = 'Diagnostics ${stamp}'`)).should('eq', 0);
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_thankyou WHERE postid = ${none + 2}`)).should('eq', 0);
    cy.then(() => count(`SELECT COUNT(*) AS n FROM #__kunena_user_topics WHERE topic_id = ${none + 3}`)).should('eq', 0);
    page().then((body) => expect(body, 'a failed check').to.not.match(/TEST FAILED/i));
  });

  it('fixes the category of a message and of a user topic to that of their topic', () => {
    const fix = (check) => cy.request(`${admin}&view=users`).then((users) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(users.body)[1];

      return cy.request({ url: `${admin}&task=tools.diagnostics&fix=${check}&${token}=1`, followRedirect: false });
    });
    // A topic of category 4 whose message and user topic say category 3.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (4, 'Diagnostics ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 798, 'x', '', 0, UNIX_TIMESTAMP(), 798, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;
      cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-user', 798, '', 'Diagnostics ${stamp}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      ).then((message) => {
        ids.message = message.insertId;
        cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Diagnostics ${stamp}')`);
      });
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (798, ${topic.insertId}, 3, 1, 0, 1, 0, 0, '')`);
    });
    page().then((body) => {
      ['messageWrongCategory', 'userTopicWrongCategory'].forEach((check) => expect(result(body, check), check).to.match(new RegExp(`^ ${check} TEST FAILED`)));
    });
    cy.then(() => fix('messageWrongCategory'));
    cy.then(() => fix('userTopicWrongCategory'));
    cy.then(() => count(`SELECT catid AS n FROM #__kunena_messages WHERE id = ${ids.message}`)).should('eq', 4);
    cy.then(() => count(`SELECT category_id AS n FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`)).should('eq', 4);
    page().then((body) => expect(body, 'a failed check').to.not.match(/TEST FAILED/i));
    cy.then(() => cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`));
    cy.then(() => cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`));
  });
});
