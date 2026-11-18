// The button "Hide text" of SCEditor wrote [hidetext] in its WYSIWYG mode, a tag which the forum did not know: the text was shown to
// everyone, also to the guests. The button writes [hide] now, and a message with [hidetext] (written before) is [hide] for the parser,
// the lists, the feeds and the search.
describe('Kunena BBCode: [hidetext] is [hide]', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress hidetext ${stamp}`;
  const secret = `HIDETEXTPROBE${stamp}`;
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/agora/config/params.ini`;
  const old = {};
  let topicId;

  const pages = () => [
    `${base}&view=topic&catid=3&id=${topicId}`,
    `${base}&view=category&catid=3`,
    `${base}&view=category&catid=3&format=feed&layout=feed&type=rss`,
    `${base}&view=topics&mode=replies&sel=-1`,
    `${base}&view=topics&format=feed&layout=feed&type=rss`,
    `${base}&view=topic&format=json&catid=3&id=${topicId}`,
  ];

  before(() => {
    cy.kunena_setOption('template', 'agora').then((value) => {
      old.template = value;
    });
    cy.readFile(params).then((text) => {
      old.params = text;
    });
  });

  after(() => {
    cy.then(() => {
      if (old.params !== undefined) {
        cy.writeFile(params, old.params);
      }
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
    cy.then(() => cy.kunena_setOption('template', old.template ?? null));
  });

  beforeEach(() => {
    // A topic whose first message has a [hidetext] block, as the button of SCEditor wrote it.
    const message = `Before [hidetext]${secret}[/hidetext] and after.`;

    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, '${message}', '', 0, UNIX_TIMESTAMP(), 797, '${message}', '', 0, '')`,
    ).then((topic) => {
      topicId = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topicId}, 3, 'ci-admin', 797, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((result) => {
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${result.insertId}, '${message}')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${result.insertId}, last_post_id = ${result.insertId} WHERE id = ${topicId}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${topicId}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${topicId}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${topicId}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${topicId}`);
  });

  it('hides the text of [hidetext] from a guest on every page', () => {
    cy.clearAllCookies();
    cy.request(`${base}&view=topic&catid=3&id=${topicId}`).its('body').should('include', 'and after').and('not.include', '[hidetext]');

    const leaks = [];

    pages().forEach((url) => {
      cy.request({ url, failOnStatusCode: false }).then((response) => {
        const body = typeof response.body === 'string' ? response.body : JSON.stringify(response.body);

        if (body.includes(secret)) {
          leaks.push(url.replace(base, ''));
        }
      });
    });
    cy.then(() => expect(leaks, 'the pages which show the hidden text to a guest').to.deep.eq([]));

    // The search shows the words which were searched: the topic is not one of its results.
    cy.request(`${base}&view=search&task=results&query=${secret}`).its('body').should('not.include', subject);
  });

  it('shows the text of [hidetext] to a logged-in user', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    cy.request(`${base}&view=topic&catid=3&id=${topicId}`).its('body').should('include', secret).and('not.include', '[hidetext]');
  });

  it('writes [hide] with the button "Hide text" of SCEditor', () => {
    // SCEditor, with the button among the buttons of the toolbar (the basic toolbar has none).
    const set = (text, name, value) => {
      const line = `${name}="${value}"`;
      const pattern = new RegExp(`^${name}=.*$`, 'm');

      return pattern.test(text) ? text.replace(pattern, line) : `${text.trimEnd()}\n${line}\n`;
    };

    cy.then(() => cy.writeFile(params, set(set(old.params, 'editorType', 'sceditor'), 'editorButtons', 'bold,hidetext,source')));
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty');
    cy.get('#postform .sceditor-button-hidetext').first().click();
    cy.window().should((win) => {
      const value = win.sceditor.instance(win.document.getElementById('message')).val();

      expect(value, 'the tag of the button').to.include('[hide]').and.to.include('[/hide]');
      expect(value, 'no tag which the forum does not know').to.not.include('hidetext');
    });
  });
});
