// A file (not a picture) put in the text of a message with [attachment=ID] showed its name escaped twice: "t&j.txt" read as
// "t&amp;j.txt" (the short name of the attachment is HTML already, the layout escaped it again).
describe('Kunena BBCode: the name of an inline file', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const name = `t&j${String(stamp).slice(-6)}.txt`;
  const html = name.replace('&', '&amp;');
  const folder = `media/kunena/attachments/cypress-inline-${stamp}`;
  const root = 'E:/www/apache/htdocs/tests';
  const ids = {};

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (10, 9, 3, 'ci-admin', 797, '', 'Re: inline file ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      ids.message = message.insertId;
      cy.writeFile(`${root}/${folder}/${name}`, 'an inline file');

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${message.insertId}, 797, 0, '', 14, '${folder}', 'text/plain', '${name}', '${name}', '', 1, 0)`,
      );
    }).then((attachment) => {
      ids.attachment = attachment.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${ids.message}, 'Inline ${stamp}: [attachment=${attachment.insertId}]${name}[/attachment]')`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE folder = '${folder}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('deleteRelativePath', folder);
  });

  it('shows the name of the file once escaped', () => {
    cy.then(() => cy.request(`${base}&view=topic&catid=3&id=9&mesid=${ids.message}`)).its('body').then((body) => {
      const text = String(body);
      const message = text.slice(text.indexOf(`Inline ${stamp}:`));

      expect(text, 'the message').to.include(`Inline ${stamp}:`);
      expect(message, 'the name of the file').to.include(html);
      expect(text, 'a name escaped twice').not.to.include(name.replace('&', '&amp;amp;'));
    });
  });
});
