// The file of an anonymous message keeps its uploader: the list of the files of that user (view=user&layout=attachments) must not
// show it to the others - with the link to its topic it would tell who wrote the anonymous message. The user himself and the
// moderators still see it.
describe('Kunena anonymous: the files of anonymous messages in the list of the user', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const stamp = Date.now();
  const folder = `media/kunena/attachments/cypress-anon-${stamp}`;
  const root = 'E:/www/apache/htdocs/tests';
  const ids = {};

  // A reply in topic 9 of the test site, by ci-user or anonymous (no user, the name "Anonymous"), with a file of ci-user.
  const addMessage = (userid, name, file) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (10, 9, 3, '${name}', ${userid}, '', 'Re: files ${stamp}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => {
    cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A message with a file.')`);
    cy.writeFile(`${root}/${folder}/${file}`, `the file ${file}`);
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${message.insertId}, ${user.id}, 0, '${file}', 20, '${folder}', 'text/plain', '${file}', '${file}', '', 0, 0)`,
    );

    return cy.wrap(message.insertId);
  });

  const list = () => cy.request({ url: `${base}&view=user&layout=attachments&userid=${user.id}`, failOnStatusCode: false }).then((response) => {
    expect(response.status, 'the status').to.eq(200);

    return String(response.body);
  });

  beforeEach(() => {
    addMessage(user.id, 'ci-user', `ownfile${stamp}.txt`).then((id) => {
      ids.own = id;
    });
    addMessage(0, 'Anonymous', `anonfile${stamp}.txt`).then((id) => {
      ids.anonymous = id;
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE folder = '${folder}'`);
    cy.task('deleteRelativePath', folder);
  });

  it('does not show the others the file of an anonymous message', () => {
    list().then((body) => {
      expect(body, 'the file of the message with his name').to.include(`ownfile${stamp}`);
      expect(body, 'the file of the anonymous message').to.not.include(`anonfile${stamp}`);
    });
  });

  it('shows the user himself and a moderator the file of the anonymous message', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    list().should('include', `anonfile${stamp}`);
    cy.clearAllCookies();
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    list().should('include', `anonfile${stamp}`);
  });
});
