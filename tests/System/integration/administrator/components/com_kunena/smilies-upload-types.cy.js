// The upload of the smilies stored any file in the folder of the emoticons, which the site serves: an svg or an html file with a
// script ran in the browser of everyone who opened it, a super user too (only the types of php files were refused, by Joomla).
// A smiley is a picture: only the types of pictures, and a file which is a picture.
describe('Kunena backend: the upload of the smilies takes pictures only', () => {
  const stamp = Date.now();
  const folder = 'media/kunena/emoticons';
  const root = () => Cypress.config('baseUrl').replace(/\/$/, '');
  // A picture of 1 x 1 pixel.
  const png = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';
  const files = {
    svg: { name: `zq-${stamp}.svg`, type: 'image/svg+xml', text: '<svg xmlns="http://www.w3.org/2000/svg"><script>window.__xss=1</script></svg>' },
    html: { name: `zq-${stamp}.html`, type: 'text/html', text: '<script>window.__xss=1</script>' },
    png: { name: `zq-${stamp}.png`, type: 'image/png', base64: png },
  };

  const upload = (file) => {
    cy.visit('/administrator/index.php?option=com_kunena&view=smilies');
    cy.document().then((doc) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(doc.documentElement.innerHTML)[1];

      return cy.window().then((win) => {
        const content = file.base64 ? Uint8Array.from(win.atob(file.base64), (c) => c.charCodeAt(0)) : file.text;
        const body = new win.FormData();

        body.append('Filedata', new win.Blob([content], { type: file.type }), file.name);
        body.append('task', 'smilies.smileyUpload');
        body.append(token, '1');

        return win.fetch(`${root()}/administrator/index.php?option=com_kunena`, { method: 'POST', body, credentials: 'same-origin' });
      });
    });
  };

  const served = (file) => cy.request({ url: `/${folder}/${file.name}`, failOnStatusCode: false }).its('status');

  after(() => {
    Object.values(files).forEach((file) => cy.task('deleteRelativePath', `${folder}/${file.name}`));
  });

  beforeEach(() => {
    cy.doAdministratorLogin();
  });

  it('does not store an svg or an html file', () => {
    upload(files.svg);
    served(files.svg).should('eq', 404);
    upload(files.html);
    served(files.html).should('eq', 404);
  });

  it('stores a picture', () => {
    upload(files.png);
    served(files.png).should('eq', 200);
  });
});
