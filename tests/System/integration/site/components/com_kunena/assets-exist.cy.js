// Every script, stylesheet and picture of the same site which a page of the forum asks for must exist (a moved or removed file is a
// 404 in the browser: a script which does not run, a picture which is missing).
describe('Kunena assets: the files of the pages exist', () => {
  const base = '/index.php?option=com_kunena';
  const pages = [
    `${base}&view=home`,
    `${base}&view=category&layout=list`,
    `${base}&view=category&catid=3`,
    `${base}&view=topic&catid=3&id=9`,
    `${base}&view=topics`,
    `${base}&view=user&layout=default&userid=798`,
    `${base}&view=user&layout=list`,
    `${base}&view=search`,
    `${base}&view=statistics`,
    `${base}&view=topic&layout=create&catid=3`,
    `${base}&view=topic&layout=reply&catid=3&id=9`,
    `${base}&view=user&layout=edit`,
    `${base}&view=category&layout=manage&catid=3`,
  ];

  const check = (who) => {
    const missing = new Set();
    const seen = new Set();

    pages.forEach((url) => {
      cy.request({ url, failOnStatusCode: false }).then((response) => {
        const doc = new DOMParser().parseFromString(String(response.body), 'text/html');
        const urls = [
          ...[...doc.querySelectorAll('script[src]')].map((element) => element.getAttribute('src')),
          ...[...doc.querySelectorAll('link[rel="stylesheet"][href], link[rel="icon"][href], link[rel="shortcut icon"][href]')].map((element) => element.getAttribute('href')),
          ...[...doc.querySelectorAll('#kunena img[src]')].map((element) => element.getAttribute('src')),
        ];

        urls.filter((asset) => asset && !/^(https?:)?\/\//.test(asset) && !asset.startsWith('data:') && !asset.startsWith('#')).forEach((asset) => {
          // The address is relative to the page (it starts with the folder of the site).
          const clean = new URL(asset.split('#')[0], new URL(url, Cypress.config('baseUrl'))).href;

          if (!seen.has(clean)) {
            seen.add(clean);
            cy.request({ url: clean, failOnStatusCode: false, log: false }).then((answer) => {
              if (answer.status >= 400) {
                missing.add(`${clean} (${answer.status}) in ${url.replace(base, '')}`);
              }
            });
          }
        });
      });
    });
    cy.then(() => expect([...missing], `${who}: the files which are asked for and not there`).to.deep.eq([]));
  };

  it('are all there for a visitor', () => {
    check('a visitor');
  });

  it('are all there for the administrator', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    check('the administrator');
  });
});
