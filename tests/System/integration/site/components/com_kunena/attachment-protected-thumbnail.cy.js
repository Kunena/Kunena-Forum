// With the protection of the attachments on, a picture in the attachment list of a message is shown from its inline address: the
// download address is sent as a forced download, which the browser does not show as a picture (issue #10013). A file which is
// not a picture keeps its link to the download.
describe('Kunena attachments: the pictures of a message with the protection on', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const folder = `media/kunena/attachments/cypress-${stamp}`;
  const root = 'E:/www/apache/htdocs/tests';
  const png = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
  const random = Array.from({ length: 32 }, () => Math.floor(Math.random() * 16).toString(16)).join('');
  const pictures = { protected: `protected${stamp}.png`, unprotected: `unprotected${stamp}.png` };
  const ids = {};
  const old = {};

  const addAttachment = (message, protection, filetype, filename, real) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${message}, 797, ${protection}, '${filename}', 70, '${folder}', '${filetype}', '${filename}', '${real}', '', 0, 0)`,
  );

  before(() => {
    cy.kunena_setOption('attachmentProtection', '1').then((value) => {
      old.protection = value;
    });
    cy.kunena_setOption('lightbox', '1').then((value) => {
      old.lightbox = value;
    });
  });

  after(() => {
    cy.kunena_setOption('attachmentProtection', old.protection ?? '0');
    cy.kunena_setOption('lightbox', old.lightbox ?? null);
  });

  // The queryDB task removes what is inserted (with an id) after each test: the data is made again for every test.
  beforeEach(() => {
    cy.writeFile(`${root}/${folder}/${random}`, png, 'base64');
    cy.writeFile(`${root}/${folder}/${pictures.unprotected}`, png, 'base64');
    cy.writeFile(`${root}/${folder}/notes${stamp}`, `the notes ${stamp}`);

    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress thumbnail ${stamp}', 0, 0, 0, 0, 0, 1, 0, 3, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', 'Cypress thumbnail ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      addAttachment(message.insertId, 1, 'image/png', random, pictures.protected);
      addAttachment(message.insertId, 0, 'image/png', pictures.unprotected, pictures.unprotected);
      addAttachment(message.insertId, 1, 'text/plain', `notes${stamp}`, `notes${stamp}.txt`);
    });

    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.then(() => cy.visit(`${base}&view=topic&catid=3&id=${ids.topic}`));
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = 'Cypress thumbnail ${stamp}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE folder = '${folder}'`);
    cy.task('deleteRelativePath', folder);
  });

  Object.entries(pictures).forEach(([kind, name]) => {
    it(`shows the ${kind} picture from its inline address`, () => {
      cy.get(`.kattach img[alt="${name}"]`).scrollIntoView().should(($img) => {
        expect($img.attr('src'), 'the address of the picture').to.not.include('download=1');
        expect($img[0].complete, 'the picture is loaded').to.be.true;
        expect($img[0].naturalWidth, 'the width of the loaded picture').to.be.greaterThan(0);
      }).then(($img) => cy.request($img[0].src)).then((response) => {
        expect(response.status).to.eq(200);
        expect(response.headers['content-type']).to.match(/^image\/png/);
        expect(response.headers['content-disposition']).to.match(/^inline/);
      });
    });

    it(`opens the ${kind} picture as an image in the lightbox`, () => {
      cy.get(`.kattach img[alt="${name}"]`).then(($img) => {
        const $link = $img.closest('a');

        expect($link.attr('data-fancybox')).to.eq('gallery');
        expect($link.attr('data-type')).to.eq('image');
        expect($link.attr('href'), 'the link of the picture').to.eq($img.attr('src'));
      });
    });
  });

  it('keeps the link of a file which is not a picture to the download', () => {
    cy.get('.kattach .thumbnail:not(:has(img)) > a[data-bs-toggle="tooltip"]').should('have.length', 1).then(($link) => {
      expect($link.attr('href'), 'the link of the file').to.include('download=1');

      return cy.request($link[0].href);
    }).then((response) => {
      expect(response.headers['content-disposition']).to.match(/^attachment/);
    });
  });
});
