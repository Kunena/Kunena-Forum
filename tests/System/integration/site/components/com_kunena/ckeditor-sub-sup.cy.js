// The Subscript and Superscript buttons of CKEditor write [sub] and [sup] (issue #8673): the bbcode plugin of the bundle maps the
// tags to the elements sub and sup and back, so a message with the tags comes back unchanged after an edit, a quote and a switch to
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

  const editor = () => cy.window().its('CKEDITOR.instances.message');
  const editorData = () => editor().then((instance) => instance.getData());
  // Waits until the message editor is CKEditor and ready, so that leavewarning.js has taken its baseline.
  const editorReady = () => cy.window().should((win) => {
    expect(win.Joomla.getOptions('com_kunena.editor_type'), 'the editor of the template').to.eq('ckeditor');
    expect(win.CKEDITOR && win.CKEDITOR.instances.message && win.CKEDITOR.instances.message.status, 'the editor').to.eq('ready');
  }).wait(100);
  const setMode = (mode) => editor().then((instance) => new Cypress.Promise((resolve) => {
    instance.setMode(mode, resolve);
  }));
  // Whether a beforeunload of the page now would show the browser's dialog (as in leave-warning.cy.js).
  const shouldWarn = (expected) => cy.window().should((win) => {
    const event = new win.Event('beforeunload', { cancelable: true });

    win.dispatchEvent(event);
    expect(event.defaultPrevented, expected ? 'the page warns' : 'the page does not warn').to.eq(expected);
  });
  // Types the text into the empty editor of the new-topic page, selects it and runs the command of the button.
  const formatText = (text, command) => editor().then((instance) => {
    const range = instance.createRange();

    instance.focus();
    instance.insertText(text);
    range.selectNodeContents(instance.editable());
    instance.getSelection().selectRanges([range]);
    instance.execCommand(command);
  });
  // The posted value: the textarea of the form after CKEditor wrote its data into it, as it does on submit.
  const postedValue = () => editor().then((instance) => {
    instance.updateElement();

    return instance.element.getValue();
  });

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
    cy.get('.cke_button__subscript').should('exist');
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
    cy.get('.cke_button__superscript').should('exist');
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
    editor().should((instance) => {
      const editable = instance.editable();

      expect(editable.findOne('sub') && editable.findOne('sub').getText(), 'the text of sub').to.eq('2');
      expect(editable.findOne('sup') && editable.findOne('sup').getText(), 'the text of sup').to.eq('2');
      expect(editable.getText(), 'the text of the editor').to.not.contain('[sub]').and.not.contain('[sup]');
    });
    editor().then((instance) => {
      const range = instance.createRange();

      instance.focus();
      range.setStart(instance.editable().findOne('sub').getFirst(), 1);
      range.collapse(true);
      instance.getSelection().selectRanges([range]);
    });
    cy.window().should((win) => {
      expect(win.CKEDITOR.instances.message.getCommand('subscript').state, 'the state of the Subscript command').to.eq(win.CKEDITOR.TRISTATE_ON);
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
    editorData().should('contain', '[sub][b]a[/b][/sub]').and('contain', '[sup]b[/sup]').and('contain', '[sub]c[/sub]');

    setMode('source');
    setMode('wysiwyg');
    editorData().should('contain', '[sub][b]a[/b][/sub]').and('contain', '[sup]b[/sup]').and('contain', '[sub]c[/sub]');
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
    cy.get('.cke_button__bold').should('exist');
    cy.get('.cke_button__subscript').should('not.exist');
    cy.get('.cke_button__superscript').should('not.exist');
    editorData().should('contain', formula);
    setMode('source');
    setMode('wysiwyg');
    editorData().should('contain', formula);
    shouldWarn(false);
  });
});
