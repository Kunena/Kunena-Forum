// The icon of a topic (topic_emoticon) is one of the icons which the form offers: the published icons of the users of the set of
// icons of the category. The server took any number: a member gave his topic the icon of a system state (500-505: moved, deleted,
// unapproved, locked), and a number larger than the column was a database error.
describe('Kunena post: the icon of a topic is one of the form', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const old = {};

  const post = (icon, n) => cy.request(`${base}&view=topic&layout=create&catid=3`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=post`,
      form: true,
      body: { catid: 3, subject: `Icon ${n} ${stamp}`, message: `A topic with an icon ${stamp}.`, topic_emoticon: icon, [token]: '1' },
      followRedirect: false,
      failOnStatusCode: false,
    });
  });

  const icon = (n) => cy.task('queryDB', `SELECT icon_id FROM #__kunena_topics WHERE subject = 'Icon ${n} ${stamp}'`);

  beforeEach(() => {
    cy.kunena_setOption('topicIcons', '1').then((value) => {
      old.topicIcons = value ?? null;
    });
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.floodProtection = value ?? null;
    });
  });

  afterEach(() => {
    cy.kunena_setOption('topicIcons', old.topicIcons);
    cy.then(() => cy.kunena_setOption('floodProtection', old.floodProtection));
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE 'Icon % ${stamp}'`).then((rows) => {
      rows.forEach((row) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${row.id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${row.id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${row.id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${row.id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${row.id}`);
      });
    });
    cy.task('queryDB', `UPDATE #__kunena_users u SET posts = (SELECT COUNT(*) FROM #__kunena_messages m WHERE m.userid = u.userid AND m.hold = 0) WHERE u.userid = ${user.id}`);
    cy.kunena_fixCategoryCounters();
  });

  it('keeps an icon of the form', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    post(2, 'question');
    icon('question').its('0.icon_id').should('eq', 2);
  });

  it('does not give the icon of a system state', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    post(501, 'deleted');
    icon('deleted').then((rows) => {
      expect(rows.length, 'the topic is saved').to.eq(1);
      expect(rows[0].icon_id, 'the icon').to.eq(0);
    });
  });

  it('saves the topic with a number larger than the column', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    post('99999999999', 'large');
    icon('large').then((rows) => {
      expect(rows.length, 'the topic is saved').to.eq(1);
      expect(rows[0].icon_id, 'the icon').to.eq(0);
    });
  });
});
