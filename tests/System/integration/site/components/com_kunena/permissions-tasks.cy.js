// A user without rights sends the tasks of the moderator and the administrator to the forum (with a valid token, so only the rights
// of the user can stop it). The data of the forum must be the same afterwards, and the forum must not answer with a server error.
describe('Kunena permissions: tasks which a user may not do', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const tables = ['topics', 'messages', 'messages_text', 'categories', 'users_banned', 'ranks', 'polls', 'polls_options', 'attachments', 'announcement', 'smileys'];
  let topic;
  let adminKarma;
  let token;

  const checksum = () => cy.task('queryDB', `CHECKSUM TABLE ${tables.map((table) => `#__kunena_${table}`).join(', ')}`).then((rows) => rows.map((row) => `${row.Table}=${row.Checksum}`).join(','));

  before(() => {
    // A message of the administrator in a topic of a public category.
    cy.task(
      'queryDB',
      'SELECT t.id, t.category_id, t.first_post_id FROM #__kunena_topics AS t INNER JOIN #__kunena_categories AS c ON c.id = t.category_id INNER JOIN #__kunena_messages AS m ON m.id = t.first_post_id WHERE t.hold = 0 AND c.published = 1 AND c.pubAccess = 1 AND m.userid = 797 ORDER BY t.id ASC LIMIT 1',
    ).then((rows) => {
      topic = rows[0];
    });
    // A user may give karma: the karma of the administrator is put back after the tests (it went up by one in every run).
    cy.task('queryDB', 'SELECT karma FROM #__kunena_users WHERE userid = 797').then((rows) => {
      adminKarma = Number(rows[0].karma);
    });
  });

  after(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_karma WHERE userid = ${user.id} AND target_userid = 797`);
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET karma = ${adminKarma} WHERE userid = 797`));
  });

  beforeEach(() => {
    cy.doFrontendLogin(user.name, user.password);
    cy.request(`${base}&view=topic&catid=3&id=${topic.id}`).then((response) => {
      token = /name="([0-9a-f]{32})" value="1"/.exec(response.body)[1];
    });
  });

  const get = (query) => cy.request({ url: `${base}&${query}&${token}=1`, failOnStatusCode: false });
  const post = (query, body) => cy.request({ method: 'POST', url: `${base}&${query}`, form: true, body: { ...body, [token]: '1' }, failOnStatusCode: false });

  const tasks = () => {
    const t = topic;
    const topicQuery = `view=topic&catid=${t.category_id}&id=${t.id}`;
    const messageQuery = `${topicQuery}&mesid=${t.first_post_id}`;

    return {
      'sticky the topic': () => get(`${topicQuery}&task=sticky`),
      'lock the topic': () => get(`${topicQuery}&task=lock`),
      'delete the topic': () => get(`${topicQuery}&task=delete`),
      'delete the topic for ever': () => get(`${topicQuery}&task=permdelete`),
      'approve the topic': () => get(`${topicQuery}&task=approve`),
      'reset the votes of the poll': () => get(`${topicQuery}&task=resetvotes`),
      'delete the message': () => get(`${messageQuery}&task=delete`),
      'delete the message for ever': () => get(`${messageQuery}&task=permdelete`),
      'approve the message': () => get(`${messageQuery}&task=approve`),
      'move the topic': () => post(`view=topic&task=move`, { catid: t.category_id, id: t.id, targetcategory: 4, targetid: 0, mode: 'topic' }),
      'edit the message': () => post('view=topic&task=edit', { catid: t.category_id, id: t.id, mesid: t.first_post_id, subject: 'Changed by a user', message: 'Changed by a user' }),
      'ban the author of the message': () => post('view=user&task=ban', { userid: 797, ban: 1, banexpires: '', reason_public: 'x' }),
      'change the profile of the administrator': () => post('view=user&task=save', { userid: 797, name: 'Changed', personalText: 'changed' }),
      'delete a category': () => post('view=category&task=delete', { catid: t.category_id }),
      'save a category': () => post('view=category&task=save', { catid: t.category_id, name: 'Changed by a user' }),
      'change the karma of the administrator': () => get('view=user&task=karmaup&userid=797'),
    };
  };

  it('does not do any of the tasks of the moderator or the administrator', () => {
    checksum().then((start) => {
      Object.entries(tasks()).forEach(([name, run]) => {
        run().then((response) => {
          expect(response.status, `the answer to "${name}" is no server error`).to.be.lessThan(500);
        });
        checksum().then((now) => expect(now, `the data after "${name}"`).to.eq(start));
      });
    });
  });
});
