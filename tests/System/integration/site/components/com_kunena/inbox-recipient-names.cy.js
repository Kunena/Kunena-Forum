// The form of a new private message to a user (the link "send a message" of a profile: inboxto=<id>) was filled in with the username
// of that user, the name with which he logs in, also when the forum shows the real names and keeps the usernames for itself: every
// user could read the username of any user. The form shows the name which the forum shows now, and sends the number of the user.
describe('Kunena inbox: the recipient of a new message', () => {
  const ajax = '/index.php?option=com_ajax&plugin=inbox&group=kunena&format=json';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const target = { id: 797, username: 'ci-admin' };
  const realName = `Zq Admin ${stamp}`;
  const old = {};
  let pluginEnabled = false;

  before(() => {
    cy.task('queryDB', "SELECT enabled FROM #__extensions WHERE type = 'plugin' AND folder = 'kunena' AND element = 'inbox'").then((rows) => {
      pluginEnabled = rows.length > 0 && String(rows[0].enabled) === '1';
    });
    cy.kunena_setOption('username', '0').then((value) => {
      old.username = value ?? null;
    });
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.floodProtection = value ?? null;
    });
    cy.task('queryDB', `SELECT name FROM #__users WHERE id = ${target.id}`).then((rows) => {
      old.name = rows[0].name;
    });
    cy.task('queryDB', `UPDATE #__users SET name = '${realName}' WHERE id = ${target.id}`);
  });

  after(() => {
    cy.kunena_setOption('username', old.username);
    cy.kunena_setOption('floodProtection', old.floodProtection);
    cy.then(() => cy.task('queryDB', `UPDATE #__users SET name = '${old.name}' WHERE id = ${target.id}`));
    cy.task('queryDB', `DELETE FROM #__kunena_inbox WHERE subject IN ('Recipient ${stamp}', 'Form ${stamp}')`);
  });

  beforeEach(function skipWithoutPlugin() {
    if (!pluginEnabled) {
      this.skip();
    }
  });

  it('fills in the shown name, not the username, and sends the message to that user', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`/index.php?option=com_kunena&view=inbox&inboxto=${target.id}`).then((page) => {
      expect(page.body, 'the username in the form').not.to.contain(`data-to="${target.username}"`);
      expect(page.body, 'the shown name in the form').to.contain(`data-to="${realName}"`);

      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      // The form sends the number of the user (and the shown name in the field).
      return cy.request({
        method: 'POST',
        url: `${ajax}&task=send`,
        form: true,
        body: { to: realName, toid: target.id, subject: `Recipient ${stamp}`, body: 'A private message.', [token]: '1' },
        failOnStatusCode: false,
      });
    });
    cy.task('queryDB', `SELECT user_id FROM #__kunena_inbox WHERE subject = 'Recipient ${stamp}' AND sender_id = ${user.id}`).then((rows) => {
      expect(rows.map((row) => Number(row.user_id)), 'the recipient').to.deep.eq([target.id]);
    });
  });

  it('sends the message of the form of the page to that user', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`/index.php?option=com_kunena&view=inbox&inboxto=${target.id}`);
    cy.get('#kinbox-to').should('have.value', realName);
    cy.get('#kinbox-subject').type(`Form ${stamp}`);
    cy.get('#kinbox-body').type('A private message of the form.');
    cy.get('#kinbox [data-action="send"]').click();
    // The form closes when the message is sent.
    cy.get('#kinbox [data-role="compose"]').should('have.class', 'd-none');
    cy.task('queryDB', `SELECT user_id FROM #__kunena_inbox WHERE subject = 'Form ${stamp}' AND sender_id = ${user.id}`).then((rows) => {
      expect(rows.map((row) => Number(row.user_id)), 'the recipient').to.deep.eq([target.id]);
    });
  });
});
