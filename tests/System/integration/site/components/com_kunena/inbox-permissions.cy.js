// The inbox of a user is for him alone: another user must not see the messages in the list or in a conversation, and must not mark
// them as read or delete them, even when he knows the numbers of the messages. The requests go to the inbox plugin (com_ajax) like
// the page of the inbox sends them.
describe('Kunena permissions: the inbox', () => {
  const base = '/index.php?option=com_ajax&plugin=inbox&group=kunena&format=json';
  const stamp = Date.now();
  const secret = `SECRETINBOX${stamp}`;
  const owner = 797;
  const ids = {};

  const ajax = (task, params = {}, method = 'GET', token = null) => cy.request({
    method,
    url: `${base}&task=${task}`,
    qs: method === 'GET' ? { ...params, ...(token ? { [token]: '1' } : {}) } : undefined,
    form: method === 'POST',
    body: method === 'POST' ? { ...params, ...(token ? { [token]: '1' } : {}) } : undefined,
    failOnStatusCode: false,
  });

  const pageToken = () => cy.request('/index.php?option=com_kunena&view=home').then((response) => /name="([0-9a-f]{32})" value="1"/.exec(response.body)[1]);

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_inbox (user_id, sender_id, type, conversation_id, subject, body, topic_id, message_id, created_time, read_time, deleted_recipient, deleted_sender) VALUES (${owner}, 796, 'message', 4242, '${secret} subject', '${secret} body', 0, 0, UNIX_TIMESTAMP(), 0, 0, 0)`,
    ).then((row) => {
      ids.row = row.insertId;

      // A file of the message (the files are in a folder which the web server does not give out).
      cy.writeFile(`E:/www/apache/htdocs/tests/media/kunena/inbox/cypress-${stamp}.txt`, secret);

      return cy.task('queryDB', `INSERT INTO #__kunena_inbox_attachments (inbox_id, user_id, filename, \`stored\`, mime, size, created_time) VALUES (${row.insertId}, 796, 'secret.txt', 'cypress-${stamp}.txt', 'text/plain', ${secret.length}, UNIX_TIMESTAMP())`);
    }).then((attachment) => {
      ids.attachment = attachment.insertId;
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_inbox_attachments WHERE \`stored\` = 'cypress-${stamp}.txt'`);
    cy.task('deleteRelativePath', `media/kunena/inbox/cypress-${stamp}.txt`);
  });

  it('shows the messages to the user they are for', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    ajax('list').its('body').then((body) => expect(JSON.stringify(body), 'the list of the owner').to.include(secret));
    ajax('download', { id: ids.attachment }).its('body').then((body) => expect(String(body), 'the file for the owner').to.include(secret));
  });

  it('shows nothing of them to another user', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);

    ajax('list').its('body').then((body) => expect(JSON.stringify(body), 'the list').to.not.include(secret));
    [ids.row, 4242].forEach((id) => {
      ajax('view', { id }).its('body').then((body) => expect(JSON.stringify(body), `the conversation ${id}`).to.not.include(secret));
    });
    ajax('download', { id: ids.attachment }).then((response) => {
      // com_ajax answers an error with JSON.
      expect(response.headers['content-type'] || '', 'the type of the answer').to.not.include('octet-stream');
      expect(String(response.body), 'the file').to.not.include(secret);
    });
  });

  it('does not let another user mark them as read or delete them', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);

    pageToken().then((token) => {
      ajax('read', { 'ids[]': ids.row }, 'POST', token);
      ajax('delete', { 'ids[]': ids.row }, 'POST', token);
    });

    cy.task('queryDB', `SELECT read_time, deleted_recipient, deleted_sender FROM #__kunena_inbox WHERE id = ${ids.row}`).then((rows) => {
      expect(rows, 'the message is still there').to.have.length(1);
      expect(Number(rows[0].read_time), 'not read').to.eq(0);
      expect(Number(rows[0].deleted_recipient), 'not deleted by the recipient').to.eq(0);
      expect(Number(rows[0].deleted_sender), 'not deleted by the sender').to.eq(0);
    });
  });
});
