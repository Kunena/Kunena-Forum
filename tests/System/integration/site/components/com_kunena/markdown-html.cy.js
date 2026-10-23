// Markdown and safe HTML in messages (options enableMarkdown / enableHtml): they become BBCode, so only the BBCode renderer makes
// HTML. Addresses which are no web, mail or site address never become a link or an image, other HTML stays text, the options
// off leave the text as it is, and a message which is nothing but Markdown quote marks does not break the page.
describe('Kunena Markdown and safe HTML in messages', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const old = {};

  const setOption = (name, value) => cy.kunena_setOption(name, value).then((previous) => {
    if (!(name in old)) {
      old[name] = previous;
    }
  });

  const sql = (text) => text.replaceAll('\\', '\\\\').replaceAll("'", "\\'");

  // A topic of ci-admin with one message, in a category of its own.
  const addTopic = (text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, 'Cypress markup ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, '', '', 0, UNIX_TIMESTAMP(), 797, '', '', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, 'ci-admin', 797, '', 'Cypress markup ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${sql(text)}')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
  )).then(() => {
    ids.topic = topic.insertId;
    cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 1, numPosts = 1, last_topic_id = ${topic.insertId}, last_post_time = UNIX_TIMESTAMP() WHERE id = ${ids.category}`);
  })));
  const topicUrl = () => `${base}&view=topic&catid=${ids.category}&id=${ids.topic}`;

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress markup ${stamp}', 'cypress-markup-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 92, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-markup-${stamp}', 'catid', ${category.insertId}, 0)`);
    });
  });

  afterEach(() => {
    Object.entries(old).forEach(([name, value]) => cy.kunena_setOption(name, value === undefined ? null : value));
    Object.keys(old).forEach((name) => delete old[name]);
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${ids.category}`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-markup-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('renders Markdown and safe HTML, and makes no link or image of an unsafe address', () => {
    setOption('enableMarkdown', '1');
    setOption('enableHtml', '1');
    addTopic([
      '**bold markdown** and [good link](https://example.com/good)',
      '[bad markdown](javascript:alert(1)) <a href="javascript:alert(2)">bad html</a> <a href="&#106;avascript:alert(3)">bad entity</a>',
      '<img src=x onerror=alert(4)> <strong>bold html</strong> ![bad image](javascript:alert(5))',
    ].join('\n'));
    cy.then(() => cy.visit(topicUrl()));
    cy.get('.kmsg').first().within(() => {
      cy.contains('b, strong', 'bold markdown');
      cy.contains('b, strong', 'bold html');
      cy.get('a[href="https://example.com/good"]').should('contain', 'good link');
      cy.get('a, img').each(($el) => {
        expect(`${$el.attr('href') ?? ''} ${$el.attr('src') ?? ''}`.toLowerCase(), 'the address of a link or image').to.not.include('javascript');
      });
      cy.get('[onerror]').should('not.exist');
      cy.get('img').should('not.exist');
      cy.root().should('contain', '<img src=x onerror=alert(4)>');
    });
  });

  it('leaves Markdown and HTML as text when the options are off', () => {
    setOption('enableMarkdown', '0');
    setOption('enableHtml', '0');
    addTopic('**not bold** <b>not bold either</b>');
    cy.then(() => cy.visit(topicUrl()));
    cy.get('.kmsg').first().should('contain', '**not bold**').and('contain', '<b>not bold either</b>');
    cy.get('.kmsg').first().find('b, strong').should('not.exist');
  });

  it('shows a message of thousands of nested quote marks without a server error', () => {
    setOption('enableMarkdown', '1');
    addTopic(`${'> '.repeat(20000)}deep end ${stamp}`);
    cy.then(() => cy.request({ url: topicUrl(), failOnStatusCode: false })).then((response) => {
      expect(response.status, 'the status of the topic page').to.eq(200);
      expect(String(response.body), 'the text of the message').to.include(`deep end ${stamp}`);
    });
    cy.request({ url: `${base}&view=category&catid=${ids.category}`, failOnStatusCode: false }).its('status').should('eq', 200);
  });
});
