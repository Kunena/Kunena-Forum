// The uploads of the forum in Kunena's own code (media/kunena/core/js/uploader.js) in place of jQuery File Upload: a file of the
// file field is sent at once, an image gets its picture in the list, and an image larger than the images of the forum (the options
// "Max image width / height") arrives smaller. The avatar is sent the same way. Too many files are none. The images are drawn by the
// browser; the rows and the files of the uploads are removed after each test, the avatar of the user is put back.
describe('Kunena uploads: the own uploader', () => {
  const create = '/index.php?option=com_kunena&view=topic&layout=create&catid=3';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const folder = `media/kunena/attachments/${user.id}`;
  const old = {};

  // A PNG of the given size, drawn in the page (stripes, so that it is no tiny file).
  const png = (width, height) => cy.window().then((win) => new Cypress.Promise((resolve) => {
    const canvas = win.document.createElement('canvas');
    const context = canvas.getContext('2d');

    canvas.width = width;
    canvas.height = height;

    for (let x = 0; x < width; x += 10) {
      context.fillStyle = x % 20 ? '#3d5afe' : '#ffb300';
      context.fillRect(x, 0, 10, height);
    }

    canvas.toBlob((blob) => blob.arrayBuffer().then((buffer) => resolve(Cypress.Buffer.from(buffer))), 'image/png');
  }));

  // The size of an image at an address of the page, as the browser reads it.
  const imageSize = (href) => cy.window().then((win) => new Cypress.Promise((resolve, reject) => {
    const image = new win.Image();

    image.onload = () => resolve({ width: image.naturalWidth, height: image.naturalHeight });
    image.onerror = () => reject(new Error(`no image at ${href}`));
    image.src = href;
  }));

  before(() => {
    cy.task('queryDB', `SELECT avatar FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      old.avatar = rows[0] ? rows[0].avatar : '';
    });
  });

  after(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET avatar = '${String(old.avatar ?? '').replace(/'/g, "''")}' WHERE userid = ${user.id}`));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE userid = ${user.id} AND mesid = 0`);
    cy.task('deleteRelativePath', folder);
    ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].forEach((extension) => cy.task('deleteRelativePath', `media/kunena/avatars/users/avatar${user.id}.${extension}`));
  });

  it('sends an attachment at once, with its picture, and makes a large image smaller', () => {
    cy.kunena_getOption('imageWidth').then((width) => {
      old.maxWidth = parseInt(width, 10) || 800;
    });
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(create);
    cy.window().should('have.property', 'KunenaUploader');
    cy.intercept({ method: 'POST', url: /upload/ }).as('upload');

    png(2400, 1600).then((contents) => {
      cy.get('#fileupload').selectFile({ contents, fileName: `cypress-uploader-${stamp}.png`, mimeType: 'image/png' }, { force: true });
    });
    // The picture of the image in the list, before the answer of the server.
    cy.get('#files canvas').should('have.length', 1).and('have.attr', 'width', '100');
    cy.wait('@upload').its('response.body.success').should('eq', true);
    cy.contains('#files button', 'Remove File').should('be.visible');
    cy.get('input[name^="attachments["]').should('have.length', 1);

    // The image of the server: not larger than the images of the forum, in the proportion of the original.
    cy.get('#files a[href]').first().invoke('attr', 'href').then((href) => imageSize(href)).then((size) => {
      expect(size.width, 'the width of the image which was sent').to.be.at.most(old.maxWidth);
      expect(Math.abs(size.width / size.height - 1.5), 'the proportion of the image').to.be.below(0.02);
    });
  });

  it('keeps no file when too many are chosen', () => {
    cy.kunena_getOption('attachmentLimit').then((limit) => {
      old.limit = parseInt(limit, 10) || 8;
    });
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(create);
    cy.window().should('have.property', 'KunenaUploader');
    cy.then(() => {
      const files = Array.from({ length: old.limit + 1 }, (_, index) => ({
        contents: Cypress.Buffer.from(`File ${index}.`),
        fileName: `cypress-uploader-${stamp}-${index}.txt`,
        mimeType: 'text/plain',
      }));

      cy.get('#fileupload').selectFile(files, { force: true });
    });
    cy.get('#alert_max_file').should('be.visible');
    cy.get('#files > div').should('have.length', 0);
  });

  it('sends the avatar, which the profile shows', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit('/index.php?option=com_kunena&view=user&layout=edit');
    cy.get('a[href="#editavatar"]').click();
    cy.get('#fileupload').should('have.attr', 'accept', 'image/*').and('not.have.attr', 'multiple');
    cy.intercept({ method: 'POST', url: /upload/ }).as('avatar');

    png(300, 300).then((contents) => {
      cy.get('#fileupload').selectFile({ contents, fileName: `cypress-avatar-${stamp}.png`, mimeType: 'image/png' }, { force: true });
    });
    cy.get('#files canvas').should('have.length', 1);
    cy.wait('@avatar').its('response.body.success').should('eq', true);
    cy.contains('#files button', 'Remove').should('be.visible');
    cy.task('queryDB', `SELECT avatar FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      expect(rows[0].avatar, 'the avatar of the user').to.eq(`users/avatar${user.id}.png`);
    });
  });
});
