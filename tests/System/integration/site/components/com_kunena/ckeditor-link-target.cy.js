// The link dialog of CKEditor chooses where a link opens (issue #7793): "Same window" writes [url=<address>], "New window" writes
// [url=<address> target=_blank]. The plugin kunenalinktarget (ckeditor_config.js) moves the parameter to the target attribute when a
// message is loaded and back into the address when it is saved, so a message comes back unchanged after an edit, a quote and a switch
// to the source mode and back, and an untouched message does not count as changed for the leave warning. The editor of the templates
// is set to CKEditor for the spec (params.ini) and put back afterwards; the rows are inserted by the spec, nothing is posted.
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

  // Opens the link dialog, on the link of the message when there is one. CKEditor runs the dialog's onShow (which fills in the
  // fields) 100 ms after the dialog became the current one, and fires dialogShow after it: the spec waits for that event.
  let dialogShown = false;
  const openLinkDialog = () => editor().then((instance) => {
    const link = instance.editable().findOne('a');

    dialogShown = false;
    instance.on('dialogShow', (event) => {
      event.removeListener();
      dialogShown = true;
    });
    instance.focus();

    if (link) {
      instance.getSelection().selectElement(link);
    }

    instance.execCommand('link');
  });
  const linkDialog = () => cy.window().should((win) => {
    const current = win.CKEDITOR.dialog.getCurrent();

    expect(dialogShown, 'the dialog is shown').to.eq(true);
    expect(current && current.getName(), 'the open dialog').to.eq('link');
  }).then((win) => win.CKEDITOR.dialog.getCurrent());
  const targetSelect = () => linkDialog().then((dialog) => cy.get(`#${dialog.getContentElement('info', 'kunenaLinkTarget').getInputElement().getId()}`));
  // Fills in a new link in the dialog of the new-topic page and presses OK.
  const insertLink = (target) => {
    openLinkDialog();
    linkDialog().then((dialog) => {
      dialog.setValueOf('info', 'url', 'https://example.com');
      dialog.setValueOf('info', 'linkDisplayText', 'x');
      dialog.setValueOf('info', 'kunenaLinkTarget', target);
      dialog.click('ok');
    });
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

  it('offers the choice in the dialog of a new link, "Same window" first', () => {
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    editorReady();
    openLinkDialog();
    linkDialog().then((dialog) => {
      expect(dialog.getValueOf('info', 'kunenaLinkTarget'), 'the choice of a new link').to.eq('');
    });
    targetSelect().should('be.visible').find('option').should(($options) => {
      expect([...$options].map((option) => [option.textContent.trim(), option.value])).to.deep.eq([['Same window', ''], ['New window', '_blank']]);
    });
    linkDialog().then((dialog) => cy.get(`#${dialog.getContentElement('info', 'kunenaLinkTarget').getElement().getId()}`)).should('contain', 'Open the link in');
  });

  it('writes target=_blank for "New window"', () => {
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    editorReady();
    insertLink('_blank');
    editorData().should('contain', '[url=https://example.com target=_blank]x[/url]');
  });

  it('writes no target for "Same window"', () => {
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    editorReady();
    insertLink('');
    editorData().should('contain', '[url=https://example.com]x[/url]').and('not.contain', 'target=');
  });

  it('hides the choice for an e-mail link', () => {
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    editorReady();
    openLinkDialog();
    targetSelect().should('be.visible');
    linkDialog().then((dialog) => {
      dialog.setValueOf('info', 'linkType', 'email');
    });
    targetSelect().should('not.be.visible');
    linkDialog().then((dialog) => {
      dialog.hide();
    });
  });

  it('keeps target=_blank of a stored message through the editor, the source mode and the dialog', () => {
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

    // The dialog shows the link as "New window", with the address alone; OK without a change keeps the parameter.
    openLinkDialog();
    linkDialog().then((dialog) => {
      expect(dialog.getValueOf('info', 'kunenaLinkTarget'), 'the choice').to.eq('_blank');
      expect(dialog.getValueOf('info', 'protocol'), 'the protocol').to.eq('https://');
      expect(dialog.getValueOf('info', 'url'), 'the URL field').to.eq('example.com');
      dialog.click('ok');
    });
    editorData().should('contain', withTarget);

    // "Same window" removes the parameter, which is a change of the text.
    openLinkDialog();
    linkDialog().then((dialog) => {
      dialog.setValueOf('info', 'kunenaLinkTarget', '');
      dialog.click('ok');
    });
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

    openLinkDialog();
    linkDialog().then((dialog) => {
      expect(dialog.getValueOf('info', 'kunenaLinkTarget'), 'the choice').to.eq('');
      dialog.click('ok');
    });
    editorData().should('contain', withoutTarget).and('not.contain', 'target=');

    openLinkDialog();
    linkDialog().then((dialog) => {
      dialog.setValueOf('info', 'kunenaLinkTarget', '_blank');
      dialog.click('ok');
    });
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
