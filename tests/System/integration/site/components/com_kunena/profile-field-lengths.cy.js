// The fields of the profile have a length in the database (location, website name and address: 50 characters; the signature:
// the option "Max. Signature Length"). The form does not stop a longer text (the address of a website is often longer than 50
// characters): the database refused it and the profile was not saved at all. A text which is too long is shortened now, and the
// signature keeps to its maximum.
describe('Kunena profile: texts longer than their field', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const longUrl = 'https://www.example.com/a/rather/long/path/to/the/page/of/the/user.html';
  const longPlace = 'A town with a very long name in a country far away from here';
  const old = {};

  const save = (fields) => cy.request(`${base}&view=user&layout=edit`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({
      method: 'POST',
      url: `${base}&view=user&task=save`,
      form: true,
      body: { userid: user.id, ...fields, [token]: '1' },
      failOnStatusCode: false,
      followRedirect: false,
    }).then((response) => expect(response.status, 'the status of the save').to.be.lessThan(500));
  });
  const profile = () => cy.task('queryDB', `SELECT location, websiteurl, websitename, signature, personalText FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => rows[0]);

  before(() => {
    cy.kunena_setOption('maxSig', '30').then((value) => {
      old.maxSig = value;
    });
    cy.kunena_setOption('signature', '1').then((value) => {
      old.signature = value;
    });
    profile().then((row) => {
      old.row = row;
    });
  });

  after(() => {
    cy.kunena_setOption('maxSig', old.maxSig ?? '300');
    cy.kunena_setOption('signature', old.signature ?? '1');
    cy.then(() => {
      const q = (value) => (value === null || value === undefined ? 'NULL' : `'${String(value).replace(/'/g, "\\'")}'`);

      cy.task('queryDB', `UPDATE #__kunena_users SET location = ${q(old.row.location)}, websiteurl = ${q(old.row.websiteurl)}, websitename = ${q(old.row.websitename)}, signature = ${q(old.row.signature)}, personalText = ${q(old.row.personalText)} WHERE userid = ${user.id}`);
    });
  });

  it('saves a long address of a website and a long place, shortened to their fields', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    save({ websiteurl: longUrl, location: longPlace, personalText: 'Saved with the long fields' });
    profile().then((row) => {
      expect(row.personalText, 'the profile is saved').to.eq('Saved with the long fields');
      expect(row.location, 'the place').to.eq(longPlace.slice(0, 50));
      expect(row.websiteurl.length, 'the length of the address').to.be.at.most(50);
    });
  });

  it('keeps the signature to its maximum length', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    save({ signature: 'x'.repeat(200), personalText: 'Saved with a long signature' });
    profile().then((row) => {
      expect(row.personalText, 'the profile is saved').to.eq('Saved with a long signature');
      expect(row.signature.length, 'the length of the signature').to.be.at.most(30);
    });
  });
});
