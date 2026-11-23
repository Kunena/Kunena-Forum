// A member who chooses the status "Invisible" (3) is not shown as online, like a member who switched "show online" off (the
// login widget treats both alike, and the status of the profile is hidden). The list of who is online hid only the second one, so a
// guest saw the invisible member by name.
describe('Kunena who is online: an invisible member', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const saved = {};
  const old = {};

  // The block of the list of who is online on the index.
  const whoIsOnline = (body) => {
    const start = body.indexOf('id="kwho"');

    expect(start, 'the list of who is online').to.be.greaterThan(-1);

    return body.slice(start, body.indexOf('kwho-filter', start) > -1 ? body.indexOf('kwho-filter', start) : start + 20000);
  };

  before(() => {
    cy.kunena_setOption('showWhoisonline', '1').then((value) => {
      old.showWhoisonline = value ?? null;
    });
    cy.task('queryDB', `SELECT status, showOnline FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      saved.status = rows[0].status;
      saved.showOnline = rows[0].showOnline;
    });
    cy.task('queryDB', `SELECT name, username FROM #__users WHERE id = ${user.id}`).then((rows) => {
      saved.names = [rows[0].name, rows[0].username];
    });
  });

  after(() => {
    cy.kunena_setOption('showWhoisonline', old.showWhoisonline);
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET status = ${saved.status}, showOnline = ${saved.showOnline} WHERE userid = ${user.id}`));
  });

  it('does not show the invisible member to a guest', () => {
    cy.task('queryDB', `UPDATE #__kunena_users SET status = 3, showOnline = 1 WHERE userid = ${user.id}`);

    // The member is on the forum: he has a session of now.
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=category&layout=list`);
    cy.clearCookies();

    cy.request(`${base}&view=category&layout=list`).its('body').then((body) => {
      const block = whoIsOnline(body);

      saved.names.forEach((name) => expect(block, `the name ${name}`).not.to.contain(`>${name}<`));
    });
  });
});
