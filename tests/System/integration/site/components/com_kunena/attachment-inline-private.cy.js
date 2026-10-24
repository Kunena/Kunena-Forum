// The buttons of the uploader on the files of a message which is written (AJAX tasks of the topic, format json):
// - setinline marks the files as inline, removeinlineonattachment takes it back (and gives the text of the editor without the
//   BBCode of the file);
// - setprivate does not touch the file of another user;
// only for the own files: the file of another user stays as it is. The files are rows of no message yet (mesid 0), with a file.
describe('Kunena uploader: inline and private files', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const stamp = Date.now();
  const ids = {};
  const folder = `media/kunena/attachments/cypress-${stamp}`;
  // An attachment is saved only when its file is there.
  const attach = (name, userid) => cy.writeFile(`E:/www/apache/htdocs/tests/${folder}/cypress-${name}-${stamp}.txt`, `the file ${name}`).then(() => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (0, ${userid}, 0, '', 10, 'media/kunena/attachments/cypress-${stamp}', 'text/plain', 'cypress-${name}-${stamp}.txt', 'cypress-${name}.txt', '', 0, 0)`,
  )).then((row) => {
    ids[name] = row.insertId;
  });
  const row = (name) => cy.task('queryDB', `SELECT inline, protected FROM #__kunena_attachments WHERE id = ${ids[name]}`).then((rows) => ({ inline: Number(rows[0].inline), protected: Number(rows[0].protected) }));
  const send = (task, params) => cy.request(`${base}&view=topic&layout=create&catid=3`).then((page) => {
    const token = /"csrf\.token":"([0-9a-f]{32})"/.exec(page.body)?.[1] || /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ url: `${base}&view=topic&task=${task}&format=json&${params}&${token}=1`, failOnStatusCode: false });
  });

  beforeEach(() => {
    attach('own', user.id);
    attach('other', 797);
    cy.doFrontendLogin(user.name, user.password, false);
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE folder = '${folder}'`);
    cy.task('deleteRelativePath', folder);
  });

  it('marks the own file inline and takes it back', () => {
    cy.then(() => send('setinline', `files_id=${encodeURIComponent(JSON.stringify([ids.own]))}`)).its('status').should('eq', 200);
    cy.then(() => row('own')).its('inline').should('eq', 1);
    cy.then(() => send('removeinlineonattachment', `file_id=${ids.own}&editor_text=${encodeURIComponent(`Text [attachment=${ids.own}]cypress-own.txt[/attachment] more`)}`)).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.text_prepared, 'the text of the editor').to.eq('Text  more');
    });
    cy.then(() => row('own')).its('inline').should('eq', 0);
  });

  it('does not mark the file of another user inline', () => {
    cy.then(() => send('setinline', `files_id=${encodeURIComponent(JSON.stringify([ids.other]))}`)).its('status').should('eq', 403);
    cy.then(() => row('other')).its('inline').should('eq', 0);
  });

  // (The own file of a message which is written is made private by the save of the message, with its private message:
  // setprivate changes only a file of a private message which the user may read.)
  it('does not make the file of another user private', () => {
    cy.then(() => send('setprivate', `files_id=${encodeURIComponent(JSON.stringify([ids.other]))}`));
    cy.then(() => row('other')).its('protected').should('eq', 0);
  });
});
