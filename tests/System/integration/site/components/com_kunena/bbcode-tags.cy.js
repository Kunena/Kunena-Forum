// BBCode tags with an unusual use: the text of the user is shown (it was dropped), and a tag with a wrong value gives no PHP warning
// (the afterEach of the support file fails the test for a notice or a warning on the page).
describe('Kunena BBCode tags', () => {
  const user = { name: 'ci-user', password: 'ci-user-17082005-x' };
  let topic;

  // A message in the first topic which the user can read (the task of the database removes it again after the test).
  const addMessage = (text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${topic.id}, ${topic.id}, ${topic.category_id}, 'ci-admin', 797, '', 'Re: bbcode', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((info) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${info.insertId}, '${text.replaceAll('\\', '\\\\').replaceAll("'", "\\'")}')`));

  const open = () => {
    cy.doFrontendLogin(user.name, user.password);
    cy.visit(`/index.php?option=com_kunena&view=topic&catid=${topic.category_id}&id=${topic.id}`);
  };

  before(() => {
    cy.task(
      'queryDB',
      'SELECT t.id, t.category_id FROM #__kunena_topics AS t INNER JOIN #__kunena_categories AS c ON c.id = t.category_id WHERE t.hold = 0 AND c.published = 1 AND c.pubAccess = 1 ORDER BY t.id ASC LIMIT 1',
    ).then((rows) => {
      topic = rows[0];
    });
  });

  it('shows the text of a terminal without a colour', () => {
    addMessage('[terminal]ls -la /home[/terminal]');
    open();

    cy.contains('pre', 'ls -la /home').should('have.attr', 'style').and('include', 'color:#ffffff');
  });

  it('shows a terminal with the colour with # and without #, and a wrong colour is the start colour', () => {
    addMessage('[terminal colortext="#00ff00"]green[/terminal] [terminal colortext=ff0000]red[/terminal] [terminal colortext=nonsense]plain[/terminal]');
    open();

    cy.contains('pre', 'green').should('have.attr', 'style').and('include', 'color:#00ff00');
    cy.contains('pre', 'red').should('have.attr', 'style').and('include', 'color:#ff0000');
    cy.contains('pre', 'plain').should('have.attr', 'style').and('include', 'color:#ffffff');
  });

  it('shows a mention without a user as text, not as a link with an empty address', () => {
    addMessage('Hello [mention]somebody[/mention] and [mention userid=99999999]nobody[/mention]');
    open();

    cy.contains('.topic-item-messages', '@somebody').should('exist');
    cy.contains('.topic-item-messages', '@nobody').should('exist');
    cy.get('.topic-item-messages a[href=""]').should('not.exist');
  });

  it('does not break on a SoundCloud tag with an address which is no address', () => {
    addMessage('[soundcloud]javascript:alert(1)[/soundcloud] and the rest of the message');
    open();

    cy.contains('.topic-item-messages', 'and the rest of the message').should('exist');
  });
});
