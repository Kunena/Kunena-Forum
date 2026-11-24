// The layout "Profile" of Agora shows a post of the lists of posts (the recent posts, the posts of a profile) as a card with the
// start of its text. That start was taken from every message: with "hide the replies from the guests" (teaser), a guest read the
// beginning of the replies which the forum hides from him everywhere else. The first message of a topic stays readable.
describe('Kunena Agora layout Profile: the replies hidden from the guests', () => {
  const stamp = Date.now();
  const first = `Cypress teaser opening ${stamp}`;
  const secret = `Cypress teaser hidden reply ${stamp}`;
  const params = () => `${Cypress.expose('cmsPath')}/components/com_kunena/template/agora/config/params.ini`;
  const old = {};
  const ids = {};

  const addMessage = (parent, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, 3, 'ci-user', 798, '', 'Cypress teaser ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`).then(() => message.insertId));

  before(() => {
    cy.kunena_setOption('template', 'agora').then((value) => {
      old.template = value ?? null;
    });
    cy.kunena_setOption('teaser', '1').then((value) => {
      old.teaser = value ?? null;
    });
    cy.readFile(params()).then((text) => {
      old.params = text;

      return cy.writeFile(params(), /^layoutSet=.*$/m.test(text) ? text.replace(/^layoutSet=.*$/m, 'layoutSet="profile"') : `${text.trimEnd()}\nlayoutSet="profile"\n`);
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  after(() => {
    cy.then(() => cy.writeFile(params(), old.params));
    cy.kunena_setOption('teaser', old.teaser);
    cy.kunena_setOption('template', old.template);
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress teaser ${stamp}', 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 798, '${first}', '', 0, UNIX_TIMESTAMP(), 798, '${secret}', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;
    });
    cy.then(() => addMessage(0, first)).then((id) => {
      ids.first = id;
    });
    cy.then(() => addMessage(ids.first, secret)).then((id) => {
      ids.reply = id;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.first}, last_post_id = ${id} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.subject = 'Cypress teaser ${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE subject = 'Cypress teaser ${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE subject = 'Cypress teaser ${stamp}'`);
  });

  it('shows a guest the first message but not the start of the reply', () => {
    cy.request('/index.php?option=com_kunena&view=topics&layout=posts&mode=recent&userid=798&sel=720').then((page) => {
      const body = String(page.body);

      expect(body, 'the layout Profile').to.include('agora-post-card');
      expect(body, 'the first message').to.include(first);
      expect(body, 'the hidden reply').not.to.include(secret);
    });
  });
});
