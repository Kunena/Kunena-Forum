// The link form of CKEditor chooses where a link opens (issue #7793): without "New window" it writes [url=<address>], with it
// [url=<address> target=_blank]. The choice is the manual decorator newWindow of the link (editor/ckeditor5/src/kunena-editor.js);
// the BBCode of the editor (bbcode.js) moves the parameter to the target attribute when a message is loaded and back into the address
// when it is saved, so a message comes back unchanged after an edit, a quote and a switch to the source mode and back, and an
// untouched message does not count as changed for the leave warning. The editor of the templates is set to CKEditor for the spec
// (params.ini) and put back afterwards; the rows are inserted by the spec, nothing is posted.
describe('Kunena CKEditor: the link opens in the same window or in a new window', () => {
  const base = '/index.php?option=com_kunena';
  const templates = 'E:/www/apache/htdocs/tests/components/com_kunena/template';
  const stamp = Date.now();
  const alias = `cypress-link-target-${stamp}`;
  const withTarget = '[url=https://example.com target=_blank]x[/url]';
  const withoutTarget = '[url=https://example.org]y[/url]';
  const originals = {};
  const ids = {};

  // Agora is the forum template of the site of the tests; Aurelia too, so the spec does not depend on which one is used.
  const iniFile = (template) => `${templates}/${template}/config/params.ini`;
  const clearCache = () => cy.task('deleteRelativePath', 'cache/com_kunena_template');

  const editorData = () => cy.editor_data();
  // Waits until the message editor is CKEditor and ready, so that leavewarning.js has taken its baseline.
  const editorReady = () => cy.editor_ready();
  const setMode = (mode) => cy.editor_source(mode === 'source');
  // Whether a beforeunload of the page now would show the browser's dialog (as in leave-warning.cy.js).
  const shouldWarn = (expected) => cy.window().should((win) => {
    const event = new win.Event('beforeunload', { cancelable: true });

    win.dispatchEvent(event);
    expect(event.defaultPrevented, expected ? 'the page warns' : 'the page does not warn').to.eq(expected);
  });

  // The choice "New window" of the link form: the manual decorator newWindow of the link command, for the selection.
  const newWindow = () => cy.editor_instance().then(({ editor }) => editor.commands.get('link').manualDecorators.get('linkNewWindow'));
  // Selects the text of the (first) link of the message, as a click on the link does before its form is opened.
  const selectLink = () => cy.editor_instance().then(({ editor }) => {
    const { model } = editor;

    editor.editing.view.focus();
    model.change((writer) => {
      for (const { item } of model.createRangeIn(model.document.getRoot())) {
        if (item.is('$textProxy') && item.hasAttribute('linkHref')) {
          writer.setSelection(model.createRange(model.createPositionAt(item.parent, item.startOffset), model.createPositionAt(item.parent, item.endOffset)));

          return;
        }
      }
    });
  });
  // Saves the selected link as its form does: the address and the choice "New window".
  const saveLink = (href, blank) => cy.editor_execute('link', href, { linkNewWindow: blank });
  // Inserts a new link in the editor of the new-topic page, as the link form does.
  const insertLink = (blank) => {
    cy.editor_instance().invoke('focus');
    cy.editor_execute('link', 'https://example.com', { linkNewWindow: blank }, 'x');
  };

  before(() => {
    ['agora', 'aurelia'].forEach((template) => {
      cy.readFile(iniFile(template)).then((text) => {
        originals[template] = text;

        const line = 'editorType="ckeditor"';
        const pattern = /^editorType=.*$/m;

        cy.writeFile(iniFile(template), pattern.test(text) ? text.replace(pattern, line) : `${text.trimEnd()}\n${line}\n`);
      });
    });
    clearCache();
  });

  after(() => {
    ['agora', 'aurelia'].forEach((template) => {
      if (originals[template] !== undefined) {
        cy.writeFile(iniFile(template), originals[template]);
        cy.readFile(iniFile(template)).should('eq', originals[template]);
      }
    });
    clearCache();
  });

  beforeEach(() => {
    cy.task('queryDB', `SELECT id, username FROM #__users WHERE username = '${Cypress.expose('username')}'`).then((users) => {
      ids.user = users[0].id;
      ids.username = users[0].username;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress link target ${stamp}', '${alias}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
      );
    }).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('${alias}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Link target ${stamp}', 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${ids.user}, 'x', '', 0, UNIX_TIMESTAMP(), ${ids.user}, 'y', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, '${ids.username}', ${ids.user}, '', 'Link target ${stamp}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${withTarget}')`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${message.insertId}, ${ids.topic}, ${ids.category}, '${ids.username}', ${ids.user}, '', 'Re: Link target ${stamp}', UNIX_TIMESTAMP() + 1, '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((reply) => {
      ids.reply = reply.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${reply.insertId}, '${withoutTarget}')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.message}, last_post_id = ${reply.insertId} WHERE id = ${ids.topic}`);
    });
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid IN (${ids.message}, ${ids.reply})`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = '${alias}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('offers the choice in the form of a new link, not chosen', () => {
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    editorReady();
    newWindow().should((decorator) => {
      expect(decorator.label, 'the label of the choice').to.eq('New window');
      expect(Boolean(decorator.value), 'the choice of a new link').to.eq(false);
    });
  });

  it('writes target=_blank for "New window"', () => {
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    editorReady();
    insertLink(true);
    editorData().should('contain', '[url=https://example.com target=_blank]x[/url]');
  });

  it('writes no target without "New window"', () => {
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    editorReady();
    insertLink(false);
    editorData().should('contain', '[url=https://example.com]x[/url]').and('not.contain', 'target=');
  });

  it('writes no target for an e-mail link', () => {
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    editorReady();
    cy.editor_instance().invoke('focus');
    cy.editor_execute('link', 'mailto:rich@example.com', { linkNewWindow: true }, 'mail me');
    editorData().should('contain', '[email=rich@example.com]mail me[/email]').and('not.contain', 'target=');
  });

  it('keeps target=_blank of a stored message through the editor, the source mode and the link form', () => {
    cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.message}`);
    cy.get('#postform').should('have.attr', 'data-kunena-leave-warning');
    editorReady();
    editorData().should('contain', withTarget);
    shouldWarn(false);

    setMode('source');
    editorData().should('contain', withTarget);
    setMode('wysiwyg');
    editorData().should('contain', withTarget);
    shouldWarn(false);

    // The form shows the link as "New window"; saving it without a change keeps the parameter.
    selectLink();
    newWindow().its('value').should('eq', true);
    saveLink('https://example.com', true);
    editorData().should('contain', withTarget);

    // Without "New window" the parameter is removed, which is a change of the text.
    selectLink();
    saveLink('https://example.com', false);
    editorData().should('contain', '[url=https://example.com]x[/url]').and('not.contain', 'target=');
    shouldWarn(true);
  });

  it('keeps target=_blank in a quote', () => {
    cy.visit(`${base}&view=topic&layout=reply&catid=${ids.category}&id=${ids.topic}&mesid=${ids.message}&quote=1`);
    editorReady();
    editorData().should('contain', '[quote=').and('contain', withTarget);
    setMode('source');
    setMode('wysiwyg');
    editorData().should('contain', withTarget);
  });

  it('keeps a link without the parameter without it, unless "New window" is chosen', () => {
    cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.reply}`);
    editorReady();
    editorData().should('contain', withoutTarget).and('not.contain', 'target=');
    shouldWarn(false);

    setMode('source');
    setMode('wysiwyg');
    editorData().should('contain', withoutTarget).and('not.contain', 'target=');

    selectLink();
    newWindow().its('value').should('not.eq', true);
    saveLink('https://example.org', false);
    editorData().should('contain', withoutTarget).and('not.contain', 'target=');

    selectLink();
    saveLink('https://example.org', true);
    editorData().should('contain', '[url=https://example.org target=_blank]y[/url]');
    shouldWarn(true);
  });

  it('opens the link with the parameter in a new window on the topic page, and the other one not', () => {
    cy.visit(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`);
    cy.get('a.bbcode_url[href="https://example.com"]').should('have.attr', 'target', '_blank');
    cy.get('a.bbcode_url[href="https://example.org"]').should(($link) => {
      expect($link.attr('target'), 'the target of the link without the parameter').to.not.eq('_blank');
    });
  });
});
