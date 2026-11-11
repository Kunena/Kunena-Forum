// The tab "Modified files" of the edit screen of a template compares the files of the template with its checksums.json, written
// by the build. A dummy template with two files: one listed with its right SHA-256 (not shown), one with a wrong SHA-256
// (modified), and a third listed file which does not exist (missing). Without checksums.json the tab says there is no list.
describe('Kunena templates: the modified files of a template', () => {
  const root = 'E:/www/apache/htdocs/tests';
  const dummy = `cypresschecksums${Date.now() % 100000}`;
  const folder = `${root}/components/com_kunena/template/${dummy}`;
  const edit = `/administrator/index.php?option=com_kunena&view=template&layout=edit&name=${dummy}`;
  const right = 'right.txt';
  const wrong = 'wrong.txt';
  // hash('sha256', 'the right content') in PHP.
  const rightHash = 'c5e0af4684fba26f807394409a0bc54b47515ba1994bfe745cdc8ef43bee3cf8';

  before(() => {
    // A template of its own: a copy of the description of Aurelia with its own name.
    cy.readFile(`${root}/components/com_kunena/template/aurelia/config/template.xml`).then((xml) => {
      cy.writeFile(`${folder}/config/template.xml`, xml.replace(/<name>[^<]*<\/name>/, `<name>${dummy}</name>`));
    });
    cy.writeFile(`${folder}/${right}`, 'the right content');
    cy.writeFile(`${folder}/${wrong}`, 'the content on the site');
  });

  after(() => {
    cy.task('deleteRelativePath', `components/com_kunena/template/${dummy}`);
  });

  beforeEach(() => {
    cy.doAdministratorLogin();
  });

  it('lists the modified and the missing files, not the unchanged one', () => {
    cy.writeFile(`${folder}/checksums.json`, {
      [right]: rightHash,
      [wrong]: '0'.repeat(64),
      'absent.txt': '1'.repeat(64),
    });

    cy.visit(edit);
    cy.get('#tab_modifiedfiles-tab').click();

    cy.get('#tab_modifiedfiles').should('be.visible').within(() => {
      cy.contains('tr', wrong).should('contain', 'Modified');
      cy.contains('tr', 'absent.txt').should('contain', 'Missing');
      cy.contains(right).should('not.exist');
    });
  });

  it('says that the template has no list without checksums.json', () => {
    cy.task('deleteRelativePath', `components/com_kunena/template/${dummy}/checksums.json`);

    cy.visit(edit);
    cy.get('#tab_modifiedfiles-tab').click();

    cy.get('#tab_modifiedfiles').should('be.visible')
      .and('contain', 'No checksum list is available for this template')
      .find('table').should('not.exist');
  });
});
