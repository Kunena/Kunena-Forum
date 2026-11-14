// A [code] block stays verbatim in the CKEditor (issue #7873): the code plugin (media/kunena/core/js/plugins/code/plugin.js) shows
// it as a <pre> with the text as written, without smileys, BBCode or HTML made of it, and writes the same BBCode back. The text
// outside the block converts as before. The spec uses the template Aurelia with the CKEditor and puts the file of its options back.
describe('Kunena CKEditor: the text of a [code] block stays as written', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const alias = `cypress-ckeditor-code-${stamp}`;
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/aurelia/config/params.ini`;
  const code = '[code=php]\n\tif (foo:DoSomething()) { return \':D\'; }\n    echo "[b]bold[/b]"; // <img src=x onerror="window.kunenaXss=1"> & more\n[/code]';
  const content = '\n\tif (foo:DoSomething()) { return \':D\'; }\n    echo "[b]bold[/b]"; // <img src=x onerror="window.kunenaXss=1"> & more\n';
  const text = `Before the code :D\n${code}\nAfter the code.`;
  const ids = {};
  const old = {};

  const sql = (value) => value.replace(/\\/g, '\\\\').replace(/'/g, "''");

  // The editor of the template Aurelia: the ini file gets the value, the cache of the template is removed.
  const useEditor = (editor) => {
    const line = `editorType="${editor}"`;
    const value = /^editorType=.*$/m.test(old.params) ? old.params.replace(/^editorType=.*$/m, line) : `${old.params.trimEnd()}\n${line}\n`;

    return cy.writeFile(params, value).then(() => cy.task('deleteRelativePath', 'cache/com_kunena_template'));
  };

  const editor = () => cy.window().its('CKEDITOR.instances.message');
  const ckeditorReady = () => cy.window().should((win) => {
    expect(win.CKEDITOR && win.CKEDITOR.instances.message && win.CKEDITOR.instances.message.status, 'the editor').to.eq('ready');
  });
  const ckeditorMode = (mode) => cy.window().then((win) => new Cypress.Promise((resolve) => {
    win.CKEDITOR.instances.message.setMode(mode, resolve);
  }));
  const ckeditorSetData = (data) => cy.window().then((win) => new Cypress.Promise((resolve) => {
    win.CKEDITOR.instances.message.setData(data, { callback: resolve });
  }));
  const editorBody = () => cy.get('#cke_message iframe.cke_wysiwyg_frame').its('0.contentDocument.body').should('not.be.empty');

  const openEdit = () => {
    cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.message}`);
    ckeditorReady();
  };

  before(() => {
    cy.kunena_setOption('template', 'aurelia').then((value) => {
      old.template = value;
    });
    cy.readFile(params).then((value) => {
      old.params = value;
    });
    cy.then(() => useEditor('ckeditor'));
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
    cy.task('queryDB', `SELECT id, username FROM #__users WHERE username = '${Cypress.expose('username')}'`).then((users) => {
      ids.user = users[0].id;
      ids.username = users[0].username;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress CKEditor code ${stamp}', '${alias}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
      );
    }).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('${alias}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'CKEditor code ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${ids.user}, 'x', '', 0, UNIX_TIMESTAMP(), ${ids.user}, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, '${ids.username}', ${ids.user}, '', 'CKEditor code ${stamp}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${sql(text)}')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
    });
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = '${alias}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('shows the code block as plain text, with a smiley outside of it only', () => {
    openEdit();
    editorBody().find('pre').should('have.length', 1).then(($pre) => {
      expect($pre[0].textContent, 'the text of the block').to.eq(content);
      expect($pre.find('*'), 'no element in the block').to.have.length(0);
    });
    editorBody().find('img').should('have.length', 1).and(($img) => {
      expect($img.closest('pre'), 'the smiley is outside of the block').to.have.length(0);
    });
    cy.window().then((win) => {
      expect(win.kunenaXss, 'no script ran in the page').to.eq(undefined);
    });
    cy.get('#cke_message iframe.cke_wysiwyg_frame').its('0.contentWindow').then((frame) => {
      expect(frame.kunenaXss, 'no script ran in the editor').to.eq(undefined);
    });
    editor().invoke('getData').should('contain', code).and('contain', 'Before the code :D');
  });

  it('saves the code block unchanged', () => {
    openEdit();
    editor().invoke('getData').should('contain', code);
    cy.get('#form_submit_button').should('not.be.disabled').click();
    cy.location('href').should('not.contain', 'layout=edit');
    cy.task('queryDB', `SELECT message FROM #__kunena_messages_text WHERE mesid = ${ids.message}`).then((rows) => {
      // The browser sends the line breaks of a form as CR LF.
      expect(rows[0].message.replace(/\r\n/g, '\n'), 'the text in the database').to.contain(code);
    });
  });

  it('keeps [code], [code=php] and [code type=php] through the source mode', () => {
    openEdit();
    ['[code]\n  one <two> & "three"\n[/code]', '[code=php]\n\t$a = 1; // :P\n[/code]', '[code type=php]\n  [i]x[/i] :)\n[/code]'].forEach((block) => {
      const data = `A line\n${block}\nThe end`;

      ckeditorSetData(data);
      editor().invoke('getData').should('eq', data);
      ckeditorMode('source');
      ckeditorMode('wysiwyg');
      editor().invoke('getData').should('eq', data);
    });
  });

  it('converts a text without a code block as before', () => {
    openEdit();
    ckeditorSetData('[b]x[/b] :D\nline two\nline three');
    editorBody().find('strong').should('have.text', 'x');
    editorBody().find('img').should('have.length', 1);
    editor().invoke('getData').should('eq', '[b]x[/b] :D\nline two\nline three');
  });
});
