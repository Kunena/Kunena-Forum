// An image can be stored with parameters, [img size=200], [img alt=my words], [img float=right] (issue #10101). The BBCode plugin of
// CKEditor does not know such a tag and failed on its [/img], so the editor stayed blank. The plugin kunenaimageparams
// (ckeditor_config.js) keeps the parameters in an attribute of the image while the message is edited and writes them back when it is
// saved, so a message comes back unchanged after an edit, a quote and a switch to the source mode and back, and an untouched message
// does not count as changed for the leave warning. The editor of the templates is set to CKEditor for the spec (params.ini) and put
// back afterwards; the rows are inserted by the spec, nothing is posted.
describe('Kunena CKEditor: an image with parameters opens and keeps them', () => {
  const base = '/index.php?option=com_kunena';
  const templates = 'E:/www/apache/htdocs/tests/components/com_kunena/template';
  const stamp = Date.now();
  const alias = `cypress-image-params-${stamp}`;
  const withSize = '[img size=200]https://example.org/a.png[/img]';
  const combinations = [
    '[img size=200x100]https://example.org/b.png[/img]',
    '[img alt=my words]https://example.org/c.png[/img]',
    '[img float=right]https://example.org/d.png[/img]',
    '[img size=150 alt=photo float=left]https://example.org/e.png[/img]',
    '[img]https://example.org/f.png[/img]',
  ];
  const injection = '[img size=200 alt="><svg onload=window.kunenaXss=1>]https://example.org/a.png[/img]';
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
  // The addresses of the images in the editable area, in their order.
  const imageSources = () => editor().then((instance) => {
    const images = instance.editable().find('img');
    const sources = [];

    for (let i = 0; i < images.count(); i++) {
      sources.push(images.getItem(i).getAttribute('src'));
    }

    return sources;
  });
  // The toolbar is usable: the bold button is not disabled.
  const toolbarEnabled = () => cy.get('.cke_button__bold').should('be.visible').and('not.have.class', 'cke_button_disabled');

  // Inserts a message of the topic; the first one is the first post of the topic.
  const insertMessage = (key, parent, text, second) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, ${ids.category}, '${ids.username}', ${ids.user}, '', 'Image params ${stamp}', UNIX_TIMESTAMP() + ${second}, '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => {
    ids[key] = message.insertId;

    return cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`);
  });

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
        `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress image params ${stamp}', '${alias}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
      );
    }).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('${alias}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Image params ${stamp}', 0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${ids.user}, 'x', '', 0, UNIX_TIMESTAMP(), ${ids.user}, 'y', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return insertMessage('message', 0, withSize, 0);
    }).then(() => insertMessage('combinations', ids.message, combinations.join('\\n'), 1))
      .then(() => insertMessage('injection', ids.message, injection, 2))
      .then(() => {
        cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.message}, last_post_id = ${ids.injection} WHERE id = ${ids.topic}`);
      });
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid IN (${ids.message}, ${ids.combinations}, ${ids.injection})`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = '${alias}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('opens [img size=200] as an image and keeps the parameter through the source mode', () => {
    cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.message}`);
    cy.get('#postform').should('have.attr', 'data-kunena-leave-warning');
    editorReady();
    toolbarEnabled();
    imageSources().should('deep.eq', ['https://example.org/a.png']);
    editorData().should('contain', withSize);
    shouldWarn(false);

    setMode('source');
    editorData().should('contain', withSize);
    setMode('wysiwyg');
    editorData().should('contain', withSize);
    imageSources().should('deep.eq', ['https://example.org/a.png']);
    shouldWarn(false);
  });

  it('opens and keeps every combination of parameters, and a plain image without one', () => {
    cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.combinations}`);
    editorReady();
    toolbarEnabled();
    imageSources().should('deep.eq', ['b', 'c', 'd', 'e', 'f'].map((name) => `https://example.org/${name}.png`));
    combinations.forEach((line) => {
      editorData().should('contain', line);
    });
    shouldWarn(false);

    setMode('source');
    setMode('wysiwyg');
    imageSources().should('have.length', 5);
    combinations.forEach((line) => {
      editorData().should('contain', line);
    });
    shouldWarn(false);
  });

  it('keeps the parameter in a quote', () => {
    cy.visit(`${base}&view=topic&layout=reply&catid=${ids.category}&id=${ids.topic}&mesid=${ids.message}&quote=1`);
    editorReady();
    imageSources().should('deep.eq', ['https://example.org/a.png']);
    editorData().should('contain', '[quote=').and('contain', withSize);
    setMode('source');
    setMode('wysiwyg');
    editorData().should('contain', withSize);
  });

  it('puts a parameter with markup into the editor as an attribute value only', () => {
    cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.injection}`);
    editorReady();
    editor().then((instance) => {
      const editable = instance.editable();
      const image = editable.findOne('img');

      expect(editable.find('svg').count(), 'the svg elements').to.eq(0);
      expect(editable.find('img').count(), 'the images').to.eq(1);
      expect(image.getAttribute('src'), 'the address').to.eq('https://example.org/a.png');
      expect(image.hasAttribute('onload'), 'an onload attribute').to.eq(false);
      expect(image.hasAttribute('onerror'), 'an onerror attribute').to.eq(false);
      expect(instance.window.$.kunenaXss, 'kunenaXss of the editor').to.be.undefined;
    });
    cy.window().then((win) => {
      expect(win.kunenaXss, 'kunenaXss of the page').to.be.undefined;
    });
    editorData().should('contain', injection);
  });

  it('writes a plain image of a new topic as [img]', () => {
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    editorReady();
    editor().then((instance) => new Cypress.Promise((resolve) => {
      instance.setData('[img]https://example.org/b.png[/img]', { callback: resolve });
    }));
    imageSources().should('deep.eq', ['https://example.org/b.png']);
    editorData().should('contain', '[img]https://example.org/b.png[/img]');
  });
});
