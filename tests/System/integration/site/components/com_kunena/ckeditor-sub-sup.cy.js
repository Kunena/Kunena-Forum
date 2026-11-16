// The Subscript and Superscript buttons of CKEditor write [sub] and [sup] (issue #8673): the BBCode of the editor (editor/ckeditor5) maps
// the tags to the elements sub and sup and back, so a message with the tags comes back unchanged after an edit, a quote and a switch to
// the source mode and back, also inside other tags and when the template hides both buttons, and an untouched message does not
// count as changed for the leave warning. The editor of the templates is set to CKEditor for the spec (params.ini) and put back
// afterwards; the rows are inserted by the spec, nothing is posted.
describe('Kunena CKEditor: subscript and superscript are written as [sub] and [sup]', () => {
  const base = '/index.php?option=com_kunena';
  const templates = 'E:/www/apache/htdocs/tests/components/com_kunena/template';
  const stamp = Date.now();
  const alias = `cypress-sub-sup-${stamp}`;
  const formula = 'H[sub]2[/sub]O x[sup]2[/sup]';
  const nested = '[sub][b]a[/b][/sub] [list][*][sup]b[/sup][/list][quote][sub]c[/sub][/quote]';
  const originals = {};
  const ids = {};

  // Agora is the forum template of the site of the tests; Aurelia too, so the spec does not depend on which one is used.
  const iniFile = (template) => `${templates}/${template}/config/params.ini`;
  const clearCache = () => cy.task('deleteRelativePath', 'cache/com_kunena_template');
  // Writes the lines (name="value") into the original params.ini of both templates, replacing the line of the same name.
  const writeParams = (lines) => {
    ['agora', 'aurelia'].forEach((template) => {
      const text = lines.reduce((ini, line) => {
        const pattern = new RegExp(`^${line.split('=')[0]}=.*$`, 'm');

        return pattern.test(ini) ? ini.replace(pattern, line) : `${ini.trimEnd()}\n${line}\n`;
      }, originals[template]);

      cy.writeFile(iniFile(template), text);
    });
    clearCache();
  };

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
  // Types the text into the empty editor of the new-topic page, selects it and runs the command of the button.
  const formatText = (text, command) => {
    cy.editor_instance().then((instance) => {
      instance.focus();
      instance.insertText(text);
    });
    cy.editor_selectAll();
    cy.editor_execute(command);
  };
  // The posted value: the textarea of the form, as the editor writes it on submit.
  const postedValue = () => cy.editor_posted();

  before(() => {
    ['agora', 'aurelia'].forEach((template) => {
      cy.readFile(iniFile(template)).then((text) => {
        originals[template] = text;
      });
    });
    cy.then(() => writeParams(['editorType="ckeditor"']));
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
        `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress sub sup ${stamp}', '${alias}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
      );
    }).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('${alias}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Sub sup ${stamp}', 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${ids.user}, 'x', '', 0, UNIX_TIMESTAMP(), ${ids.user}, 'y', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, '${ids.username}', ${ids.user}, '', 'Sub sup ${stamp}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${formula}')`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${message.insertId}, ${ids.topic}, ${ids.category}, '${ids.username}', ${ids.user}, '', 'Re: Sub sup ${stamp}', UNIX_TIMESTAMP() + 1, '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((reply) => {
      ids.reply = reply.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${reply.insertId}, '${nested}')`);
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

  it('writes [sub] for the Subscript button, in the data, the source mode and the posted form', () => {
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    editorReady();
    cy.editor_button('Subscript').should('have.length', 1);
    formatText('text', 'subscript');
    editorData().should('contain', '[sub]text[/sub]');
    setMode('source');
    editorData().should('contain', '[sub]text[/sub]');
    setMode('wysiwyg');
    postedValue().should('contain', '[sub]text[/sub]');
  });

  it('writes [sup] for the Superscript button, in the data, the source mode and the posted form', () => {
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    editorReady();
    cy.editor_button('Superscript').should('have.length', 1);
    formatText('text', 'superscript');
    editorData().should('contain', '[sup]text[/sup]');
    setMode('source');
    editorData().should('contain', '[sup]text[/sup]');
    setMode('wysiwyg');
    postedValue().should('contain', '[sup]text[/sup]');
  });

  it('shows stored [sub] and [sup] as formatting, with the Subscript command on inside it', () => {
    cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.message}`);
    editorReady();
    cy.editor_editable().should(($editable) => {
      expect($editable.find('sub').text(), 'the text of sub').to.eq('2');
      expect($editable.find('sup').text(), 'the text of sup').to.eq('2');
      expect($editable.text(), 'the text of the editor').to.not.contain('[sub]').and.not.contain('[sup]');
    });
    // The caret after the 2 of H2O: the Subscript command is on there.
    cy.editor_instance().should(({ editor }) => {
      editor.model.change((writer) => writer.setSelection(writer.createPositionAt(editor.model.document.getRoot().getChild(0), 2)));
      expect(editor.commands.get('subscript').value, 'the state of the Subscript command').to.eq(true);
    });
  });

  it('keeps stored [sub] and [sup] through the editor and the source mode, without a leave warning', () => {
    cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.message}`);
    cy.get('#postform').should('have.attr', 'data-kunena-leave-warning');
    editorReady();
    editorData().should('contain', formula);
    shouldWarn(false);

    setMode('source');
    editorData().should('contain', formula);
    setMode('wysiwyg');
    editorData().should('contain', formula);
    postedValue().should('contain', formula);
    shouldWarn(false);
  });

  it('keeps [sub] and [sup] inside bold, a list and a quote', () => {
    cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.reply}`);
    editorReady();
    // CKEditor 5 writes nested tags in its own order ([b] outside [sub], the same formatting); the form sends the stored text
    // unchanged until the text is changed.
    editorData().should('contain', '[b][sub]a[/sub][/b]').and('contain', '[sup]b[/sup]').and('contain', '[sub]c[/sub]');

    setMode('source');
    setMode('wysiwyg');
    editorData().should('contain', '[b][sub]a[/sub][/b]').and('contain', '[sup]b[/sup]').and('contain', '[sub]c[/sub]');
    postedValue().should('contain', '[sub][b]a[/b][/sub]');
    shouldWarn(false);
  });

  it('keeps [sub] and [sup] in a quote', () => {
    cy.visit(`${base}&view=topic&layout=reply&catid=${ids.category}&id=${ids.topic}&mesid=${ids.message}&quote=1`);
    editorReady();
    editorData().should('contain', '[quote=').and('contain', formula);
    setMode('source');
    setMode('wysiwyg');
    editorData().should('contain', '[quote=').and('contain', formula);
  });

  it('keeps stored [sub] and [sup] when the template hides both buttons', () => {
    writeParams(['editorType="ckeditor"', 'editorButtons="Subscript,Superscript"']);
    cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.message}`);
    editorReady();
    cy.window().its('Joomla').invoke('getOptions', 'com_kunena.ckeditor_buttons_configuration').should('eq', 'Subscript,Superscript');
    cy.editor_button('Bold').should('have.length', 1);
    cy.editor_button('Subscript').should('have.length', 0);
    cy.editor_button('Superscript').should('have.length', 0);
    editorData().should('contain', formula);
    setMode('source');
    setMode('wysiwyg');
    editorData().should('contain', formula);
    shouldWarn(false);
  });
});
