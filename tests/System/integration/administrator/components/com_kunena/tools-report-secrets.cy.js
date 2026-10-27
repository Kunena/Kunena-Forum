// The system report (backend tools, layout report) is made to be posted on the forum of the support: it lists the configuration
// of Kunena, without the keys and the passwords of the services. The eBay Cert ID (the secret of the client of eBay) was not left
// out, it was in the report with its value.
describe('Kunena backend tools: no secret in the system report', () => {
  const admin = '/administrator/index.php?option=com_kunena';
  const secret = `zqxsecret${Date.now()}`;
  const old = {};

  before(() => {
    cy.kunena_setOption('ebayCertId', secret).then((value) => {
      old.ebayCertId = value ?? '';
    });
  });

  after(() => {
    cy.kunena_setOption('ebayCertId', old.ebayCertId);
  });

  it('leaves out the eBay Cert ID', () => {
    cy.doAdministratorLogin();
    cy.request(`${admin}&view=tools&layout=report`).its('body').then((body) => {
      expect(body, 'the configuration in the report').to.contain('Kunena config settings');
      expect(body, 'the secret').not.to.contain(secret);
    });
  });
});
