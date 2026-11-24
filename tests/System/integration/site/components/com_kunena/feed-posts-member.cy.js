// The feed of the posts (feed type "posts" of the configuration, the link of the page footer: mode=posts) listed only the own
// posts of the reader when he was logged in: the list of the posts takes the logged-in user when the address has no userid, as
// the page "my posts" does. A guest got the posts of everybody. The feed is the posts of everybody for every reader now.
describe('Kunena feeds: the feed of the posts for a member', () => {
  const stamp = Date.now();
  const text = `Cypress feed post ${stamp}`;
  const ids = {};
  const old = {};

  before(() => {
    cy.kunena_setOption('enableRss', '1').then((value) => {
      old.enableRss = value ?? null;
    });
    cy.kunena_setOption('rssType', 'post').then((value) => {
      old.rssType = value ?? null;
    });
  });

  after(() => {
    cy.kunena_setOption('enableRss', old.enableRss);
    cy.kunena_setOption('rssType', old.rssType);
  });

  beforeEach(() => {
    // A reply of ci-admin in topic 9 (General Discussion).
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (10, 9, 3, 'ci-admin', 797, '', '${text}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'The text of ${text}')`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
  });

  const feed = '/index.php?option=com_kunena&view=topics&layout=feed&format=feed&type=rss&mode=posts&sel=1%20year';

  it('lists the post of another member for a guest and for ci-user', () => {
    cy.request(feed).its('body').should('include', text);
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(feed).its('body').should('include', text);
  });
});
