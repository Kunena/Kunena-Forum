// The address which the forum keeps with a post (and shows to the moderators) is an IP address or nothing. Behind a load balancer
// (the option behind_loadbalancer of Joomla) the address comes from the header X-Forwarded-For, which the visitor writes himself:
// a text with markup was kept as the address of the post, and a page of the moderators showed it as it was.
describe('Kunena posts: the address of the author', () => {
  const base = '/index.php?option=com_kunena';
  const config = 'E:/www/apache/htdocs/tests/configuration.php';
  const stamp = Date.now();
  const subject = `Re: Cypress address ${stamp}`;
  let original;

  beforeEach(() => {
    cy.readFile(config).then((text) => {
      original = text;
      cy.writeFile(config, text.replace('public $behind_loadbalancer = false;', 'public $behind_loadbalancer = true;'));
    });
    // The flood protection goes by the address: the posts of the last minutes of the tests are moved out of its time.
    cy.task('queryDB', "UPDATE #__kunena_messages SET time = time - 3600 WHERE time > UNIX_TIMESTAMP() - 600");
  });

  afterEach(() => {
    if (original) {
      cy.writeFile(config, original);
    }

    cy.task('queryDB', `SELECT id, thread FROM #__kunena_messages WHERE subject = '${subject}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${id}`);
      });
    });
    cy.kunena_fixUserCounters();
    cy.kunena_fixCategoryCounters();
    cy.task('queryDB', 'UPDATE #__kunena_topics AS t SET posts = (SELECT COUNT(*) FROM #__kunena_messages AS m WHERE m.thread = t.id AND m.hold = 0), last_post_id = (SELECT MAX(m.id) FROM #__kunena_messages AS m WHERE m.thread = t.id AND m.hold = 0) WHERE t.id = 9');
  });

  it('keeps no markup of the header X-Forwarded-For as the address of the post', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(`${base}&view=topic&layout=reply&catid=3&id=9&mesid=11`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        headers: { 'X-Forwarded-For': '"><img/src=x/onerror=alert(1)>' },
        body: { catid: 3, id: 9, parentid: 11, subject, message: `A reply ${stamp}.`, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });

    cy.task('queryDB', `SELECT ip FROM #__kunena_messages WHERE subject = '${subject}'`).then((rows) => {
      expect(rows, 'the reply').to.have.length(1);
      expect(rows[0].ip, 'the address of the reply').not.to.match(/[<>"]/);
    });
  });
});
