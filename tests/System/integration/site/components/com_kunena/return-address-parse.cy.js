// A return address which Joomla can not read (///evil.com) is no address of the site: the check of the address (Uri::isInternal)
// threw an exception for it, and the logout ended on an error page of the server instead of going back to the forum.
describe('Kunena logout: a return address which can not be read', () => {
  const base = '/index.php?option=com_kunena';

  it('goes back to the forum, without an error of the server', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(`${base}&view=home`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      cy.request({
        method: 'POST',
        url: `${base}&view=user&task=logout`,
        form: true,
        body: { return: btoa('///evil.com'), [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      }).then((response) => {
        expect(response.status, 'the status').to.be.lessThan(500);
        // The message of the exception of the check of the address.
        expect(String(response.body), 'no error of the address').not.to.contain('Could not parse the requested URI');
        expect(String(response.headers.location || ''), 'the address it goes to').not.to.contain('evil.com');
      });
    });
  });
});
