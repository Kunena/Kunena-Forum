// Every option of the template with a list of values (config.xml of the template Aurelia) is set to each of its values, one at a time,
// and the main pages are opened: none of them may give a server error, a PHP notice or a warning. The options of the template are put
// back after the test.
describe('Kunena template: the options with their values', () => {
  const base = '/index.php?option=com_kunena';
  const template = 'E:/www/apache/htdocs/tests/components/com_kunena/template/aurelia/config';
  let original;
  const options = [];

  const pages = [
    `${base}&view=home`,
    `${base}&view=topic&catid=3&id=9`,
    `${base}&view=topics&layout=default`,
    `${base}&view=topic&layout=create&catid=3`,
  ];

  // The ini file gets the value for one option; the cache of the template is removed.
  const setOption = (name, value) => {
    const line = `${name}="${value}"`;
    const pattern = new RegExp(`^${name}=.*$`, 'm');
    const text = pattern.test(original) ? original.replace(pattern, line) : `${original.trimEnd()}\n${line}\n`;

    return cy.writeFile(`${template}/params.ini`, text).then(() => cy.task('deleteRelativePath', 'cache/com_kunena_template'));
  };

  before(() => {
    cy.readFile(`${template}/params.ini`).then((text) => {
      original = text;
    });
    cy.readFile(`${template}/config.xml`).then((xml) => {
      const doc = new DOMParser().parseFromString(xml, 'text/xml');

      doc.querySelectorAll('field[type="list"]').forEach((field) => {
        const values = [...field.querySelectorAll('option')].map((option) => option.getAttribute('value')).filter((value) => value !== null);

        // Only the options which are kept in the ini file by name.
        if (field.getAttribute('name') && values.length > 1) {
          options.push({ name: field.getAttribute('name'), values });
        }
      });
    });
  });

  after(() => {
    cy.writeFile(`${template}/params.ini`, original);
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  beforeEach(() => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  });

  it('shows the pages with every value of every option', () => {
    expect(options.length, 'the options which were read from config.xml').to.be.greaterThan(10);

    options.forEach(({ name, values }) => {
      values.forEach((value) => {
        setOption(name, value);
        pages.forEach((url) => {
          cy.request({ url, failOnStatusCode: false }).its('status', { log: false }).then((status) => {
            expect(status, `${name}=${value}: the status of ${url.replace(base, '')}`).to.be.lessThan(500);
          });
        });
        // One page is opened in the browser (the whole test takes some minutes: about 90 combinations), to look for notices and warnings in the page.
        cy.visit(pages[1]);
        cy.checkForPhpNoticesOrWarnings();
      });
    });
  });
});
