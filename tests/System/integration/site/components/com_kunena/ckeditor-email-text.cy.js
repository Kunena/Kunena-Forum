// An e-mail link keeps its display text (issue #9517): the CKEditor writes a link with a text as [email=address]text[/email] and
// one without a different text as [email]address[/email], reads both back (and the quoted [email="address"] of the SCEditor), and
// the topic page shows the text as the link of the address. The SCEditor keeps the text through a switch of the mode. The spec uses
// the template Aurelia, switches its editor in params.ini and puts the file back; the rows are inserted by the spec, nothing is posted.
describe('Kunena editors: the display text of an e-mail link', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const alias = `cypress-email-text-${stamp}`;
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/aurelia/config/params.ini`;
  const address = 'rich@example.com';
  const text = 'send me an email';
  const withText = `[email=${address}]${text}[/email]`;
  const quoted = `[email="${address}"]${text}[/email]`;
  const plain = `[email]${address}[/email]`;
  const ids = {};
  const old = {};

  // The editor of the template Aurelia: the ini file gets the value, the cache of the template is removed.
  const useEditor = (editor) => {
    const line = `editorType="${editor}"`;
    const value = /^editorType=.*$/m.test(old.params) ? old.params.replace(/^editorType=.*$/m, line) : `${old.params.trimEnd()}\n${line}\n`;

    return cy.writeFile(params, value).then(() => cy.task('deleteRelativePath', 'cache/com_kunena_template'));
  };

  // Inserts a message of the topic with its text; the first one is the first message of the topic.
  const insertMessage = (key, parent, message, offset) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, ${ids.category}, '${ids.username}', ${ids.user}, '', 'E-mail text ${stamp}', UNIX_TIMESTAMP() + ${offset}, '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((result) => {
    ids[key] = result.insertId;

    return cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${result.insertId}, '${message}')`);
  });

  const editor = () => cy.window().its('CKEDITOR.instances.message');
  const editorData = () => editor().then((instance) => instance.getData());
  const editorReady = () => cy.window().should((win) => {
    expect(win.Joomla.getOptions('com_kunena.editor_type'), 'the editor of the template').to.eq('ckeditor');
    expect(win.CKEDITOR && win.CKEDITOR.instances.message && win.CKEDITOR.instances.message.status, 'the editor').to.eq('ready');
  }).wait(100);
  const setMode = (mode) => editor().then((instance) => new Cypress.Promise((resolve) => {
    instance.setMode(mode, resolve);
  }));
  const editorBody = () => cy.get('#cke_message iframe.cke_wysiwyg_frame').its('0.contentDocument.body').should('not.be.empty');

  // Opens the link dialog of a new link; CKEditor fires dialogShow after the onShow of the dialog filled in the fields.
  let dialogShown = false;
  const openLinkDialog = () => editor().then((instance) => {
    dialogShown = false;
    instance.on('dialogShow', (event) => {
      event.removeListener();
      dialogShown = true;
    });
    instance.focus();
    instance.execCommand('link');
  });
  const linkDialog = () => cy.window().should((win) => {
    const current = win.CKEDITOR.dialog.getCurrent();

    expect(dialogShown, 'the dialog is shown').to.eq(true);
    expect(current && current.getName(), 'the open dialog').to.eq('link');
  }).then((win) => win.CKEDITOR.dialog.getCurrent());
  // Fills in a new e-mail link in the dialog of the new-topic page and presses OK.
  const insertEmail = (displayText) => {
    openLinkDialog();
    linkDialog().then((dialog) => {
      dialog.setValueOf('info', 'linkType', 'email');
      dialog.setValueOf('info', 'emailAddress', address);
      dialog.setValueOf('info', 'linkDisplayText', displayText);
      dialog.click('ok');
    });
  };

  const scEditor = () => cy.window().then((win) => win.sceditor.instance(win.document.getElementById('message')));

  before(() => {
    cy.kunena_setOption('template', 'aurelia').then((value) => {
      old.template = value;
    });
    cy.readFile(params).then((value) => {
      old.params = value;
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
    cy.task('queryDB', `SELECT id, username FROM #__users WHERE username = '${Cypress.expose('username')}'`).then((users) => {
      ids.user = users[0].id;
      ids.username = users[0].username;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress e-mail text ${stamp}', '${alias}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
      );
    }).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('${alias}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'E-mail text ${stamp}', 0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${ids.user}, 'x', '', 0, UNIX_TIMESTAMP(), ${ids.user}, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return insertMessage('message', 0, withText, 0);
    }).then(() => insertMessage('quoted', ids.message, quoted, 1))
      .then(() => insertMessage('plain', ids.message, plain, 2))
      .then(() => {
        cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.message}, last_post_id = ${ids.plain} WHERE id = ${ids.topic}`);
      });
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid IN (${ids.message}, ${ids.quoted}, ${ids.plain})`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = '${alias}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  describe('CKEditor', () => {
    before(() => {
      cy.then(() => useEditor('ckeditor'));
    });

    it('writes [email=address]text[/email] for a new link with a display text', () => {
      cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
      editorReady();
      insertEmail(text);
      editorData().should('contain', withText);
    });

    it('writes [email]address[/email] for a new link without a different display text', () => {
      cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
      editorReady();
      insertEmail('');
      editorData().should('contain', plain).and('not.contain', '[email=');

      cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
      editorReady();
      insertEmail(address);
      editorData().should('contain', plain).and('not.contain', '[email=');
    });

    it('keeps the text of a stored [email=address]text[/email] through the source mode', () => {
      cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.message}`);
      editorReady();
      editorBody().find(`a[href="mailto:${address}"]`).should('have.length', 1).and('have.text', text);
      editorData().should('contain', withText);
      setMode('source');
      setMode('wysiwyg');
      editorData().should('contain', withText);
    });

    it('reads the quoted [email="address"]text[/email] of the SCEditor', () => {
      cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.quoted}`);
      editorReady();
      editorBody().find('a').should('have.length', 1).and('have.attr', 'href', `mailto:${address}`).and('have.text', text);
      editorData().should('contain', withText);
    });

    it('keeps a stored [email]address[/email] unchanged', () => {
      cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.plain}`);
      editorReady();
      editorBody().find(`a[href="mailto:${address}"]`).should('have.length', 1).and('have.text', address);
      editorData().should('contain', plain).and('not.contain', '[email=');
      setMode('source');
      setMode('wysiwyg');
      editorData().should('contain', plain).and('not.contain', '[email=');
    });
  });

  describe('SCEditor', () => {
    before(() => {
      cy.then(() => useEditor('sceditor'));
    });

    it('keeps the text of a stored [email=address]text[/email] through the source mode', () => {
      cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.message}`);
      cy.window().should((win) => {
        const instance = win.sceditor?.instance(win.document.getElementById('message'));

        expect(Boolean(instance), 'the SCEditor').to.equal(true);
        expect(instance.val(), 'the editor value on load').to.include(address).and.include(text);
      });
      cy.get('#postform .sceditor-button-source').first().click();
      scEditor().then((instance) => expect(instance.sourceMode(), 'the source mode').to.equal(true));
      cy.get('#postform .sceditor-button-source').first().click();
      scEditor().then((instance) => {
        expect(instance.sourceMode(), 'the WYSIWYG mode').to.equal(false);
        expect(instance.val(), 'the editor value').to.match(/\[email="?rich@example\.com"?\]send me an email\[\/email\]/);
      });
    });
  });

  it('shows the text as the link of the address on the topic page', () => {
    cy.visit(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`);
    // The first message and the quoted form show the text, [email]address[/email] shows the address.
    cy.get(`joomla-hidden-mail a[href="mailto:${address}"]`).should(($links) => {
      expect([...$links].map((link) => link.textContent.trim()).sort(), 'the texts of the links').to.deep.eq([address, text, text]);
    });
  });
});
