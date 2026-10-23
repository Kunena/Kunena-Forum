// A visitor who may write (option "Allow guests to write") gives a name with his post. The name must not be the username or the
// real name of a user: the forum can show the real names, and the post of the visitor would look like a post of that user.
describe('Kunena visitors: the name of a post', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const member = { id: 797, realName: `Cypress Real Name ${stamp}` };
  const old = {};

  const namesOf = () => cy.task('queryDB', `SELECT name FROM #__kunena_messages WHERE subject LIKE 'Re: Cypress guest ${stamp}%' ORDER BY id`).then((rows) => rows.map((row) => row.name));

  const reply = (name, number) => cy.request(`${base}&view=topic&layout=reply&catid=3&id=9`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=post`,
      form: true,
      body: { catid: 3, id: 9, parentid: 10, authorname: name, email: '', subject: `Re: Cypress guest ${stamp} ${number}`, message: `A post of a visitor ${number}.`, [token]: '1' },
      failOnStatusCode: false,
      followRedirect: false,
    });
  });

  before(() => {
    cy.kunena_setOption('pubWrite', '1').then((value) => {
      old.pubWrite = value;
    });
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.floodProtection = value;
    });
    cy.task('queryDB', `SELECT name FROM #__users WHERE id = ${member.id}`).then((rows) => {
      old.name = rows[0].name;
    });
    // The groups which may write in the category: the visitors too (Public and Guest), besides the standard groups.
    cy.task('queryDB', 'SELECT params FROM #__kunena_categories WHERE id = 3').then((rows) => {
      old.params = rows[0].params;
      const params = JSON.parse(rows[0].params || '{}');

      params.access_post = ['1', '2', '6', '8', '9'];
      params.access_reply = ['1', '2', '6', '8', '9'];
      cy.task('queryDB', `UPDATE #__kunena_categories SET params = '${JSON.stringify(params)}' WHERE id = 3`);
    });
  });

  after(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET params = '${old.params}' WHERE id = 3`));
    cy.kunena_setOption('pubWrite', old.pubWrite ?? '0');
    cy.kunena_setOption('floodProtection', old.floodProtection ?? '0');
  });

  beforeEach(() => {
    cy.task('queryDB', `UPDATE #__users SET name = '${member.realName}' WHERE id = ${member.id}`);
  });

  afterEach(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__users SET name = '${old.name}' WHERE id = ${member.id}`));
    cy.task('queryDB', `SELECT id FROM #__kunena_messages WHERE subject LIKE 'Re: Cypress guest ${stamp}%'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${id}`);
      });
    });
    // The topic of the test site gets its counters and its last post back.
    cy.task('queryDB', 'UPDATE #__kunena_topics SET posts = 2, last_post_id = 11, last_post_userid = 798, last_post_guest_name = \'\' WHERE id = 9');
    cy.task('queryDB', 'UPDATE #__kunena_topics t JOIN #__kunena_messages m ON m.id = 11 SET t.last_post_time = m.time WHERE t.id = 9');
  });

  it('keeps a free name, and refuses the username and the real name of a user', () => {
    reply(`Visitor ${stamp}`, 1);
    reply('ci-admin', 2);
    reply(member.realName.toUpperCase(), 3);
    namesOf().then((names) => {
      expect(names, 'the posts of the visitor').to.have.length(3);
      expect(names[0], 'a free name').to.eq(`Visitor ${stamp}`);
      expect(names[1], 'the username of a user').to.not.eq('ci-admin');
      expect(names[2].toLowerCase(), 'the real name of a user').to.not.eq(member.realName.toLowerCase());
    });
  });
});
