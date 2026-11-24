// The layout "Chat" of Agora shows a topic with the latest topics of its category in a side pane, with the number of the unread
// messages of each one (agora-chat-unread). The pane never knew which topics are unread: the number was never there. It is now.
describe('Kunena Agora layout Chat: the unread topics of the side pane', () => {
  const stamp = Date.now();
  const params = () => `${Cypress.expose('cmsPath')}/components/com_kunena/template/agora/config/params.ini`;
  const ids = {};
  const old = {};

  before(() => {
    cy.kunena_setOption('template', 'agora').then((value) => {
      old.template = value ?? null;
    });
    cy.readFile(params()).then((text) => {
      old.params = text;

      return cy.writeFile(params(), /^layoutSet=.*$/m.test(text) ? text.replace(/^layoutSet=.*$/m, 'layoutSet="chat"') : `${text.trimEnd()}\nlayoutSet="chat"\n`);
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  after(() => {
    cy.then(() => cy.writeFile(params(), old.params));
    cy.kunena_setOption('template', old.template);
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('shows the unread messages of a new topic of another member', () => {
    // A new topic of ci-admin in General Discussion.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress unread pane ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', 'Cypress unread pane ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Unread')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
    });
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request('/index.php?option=com_kunena&view=topic&catid=3&id=9').its('body').then((body) => {
      const text = String(body);
      const at = text.indexOf(`Cypress unread pane ${stamp}`);
      const end = text.indexOf('</a>', at);

      expect(at, 'the topic in the pane').to.be.greaterThan(-1);
      expect(text.slice(at, end), 'the number of its unread messages').to.contain('agora-chat-unread');
    });
  });
});
