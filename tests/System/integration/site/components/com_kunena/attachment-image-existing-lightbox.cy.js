// The option "Lightbox: use the existing one" (2: the lightbox of the site template, fancybox) was lost for a picture put in the
// text with [attachment=ID]: the layout tested the option for 1 (the lightbox of Kunena) and gave the other values a plain link,
// while the thumbnails and the links of the attachments have the attributes of the existing lightbox.
describe('Kunena attachments: the existing lightbox for a picture in the text', () => {
  const stamp = Date.now();
  const name = `pic${stamp}.png`;
  const folder = `media/kunena/attachments/cypress-lightbox-${stamp}`;
  const root = 'E:/www/apache/htdocs/tests';
  const png = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';
  const old = {};
  const ids = {};

  before(() => {
    cy.kunena_setOption('lightbox', '2').then((value) => {
      old.lightbox = value ?? null;
    });
  });

  after(() => {
    cy.kunena_setOption('lightbox', old.lightbox);
  });

  beforeEach(() => {
    cy.writeFile(`${root}/${folder}/${name}`, png, 'base64');
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (10, 9, 3, 'ci-admin', 797, '', 'Re: lightbox ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      ids.message = message.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${message.insertId}, 797, 0, '', 70, '${folder}', 'image/png', '${name}', '${name}', '', 1, 0)`,
      );
    }).then((attachment) => {
      ids.attachment = attachment.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${ids.message}, 'Lightbox ${stamp}: [attachment=${attachment.insertId}]${name}[/attachment]')`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE folder = '${folder}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('deleteRelativePath', folder);
  });

  it('gives the link of the picture the attributes of the existing lightbox', () => {
    cy.then(() => cy.request(`/index.php?option=com_kunena&view=topic&catid=3&id=9&mesid=${ids.message}`)).its('body').then((body) => {
      const text = String(body);
      const message = text.slice(text.indexOf(`Lightbox ${stamp}:`));
      const link = /<a [^>]*class="kattach-imagelink"[^>]*>/.exec(message);

      expect(link, 'the link of the picture').not.to.equal(null);
      expect(link[0], 'the link of the picture').to.include('data-fancybox=');
    });
  });
});
