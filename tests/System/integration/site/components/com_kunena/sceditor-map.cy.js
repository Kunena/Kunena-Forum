// The map button of the SCEditor (issue #8532): a [map type=.. zoom=..]place[/map] is shown in the WYSIWYG mode as a placeholder with
// the place name (no image, no frame) and gives the same BBCode back after a switch to the source mode and back, after a save and in
// the edit form; a tag without attributes and a place name with HTML characters stay as they are. Cypress fails a test on an uncaught
// exception of the page, so a switch of the mode which throws fails too. The forum template and the editor of the site are put back.
describe('Kunena SCEditor: the [map] BBCode', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress SCEditor map ${stamp}`;
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/aurelia/config/params.ini`;
  const inserted = '[map type=roadmap zoom=10]Amsterdam[/map]';
  let ids = {};
  let original;
  let oldTemplate;
  let templateSaved = false;

  const editor = () => cy.window().then((win) => win.sceditor.instance(win.document.getElementById('message')));
  const value = () => editor().then((instance) => instance.val());
  const body = () => cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body');

  // Waits for the editor of the form (and its value, when one is given).
  const ready = (text) => {
    cy.window().should((win) => {
      const instance = win.sceditor?.instance(win.document.getElementById('message'));

      expect(Boolean(instance), 'the SCEditor').to.equal(true);

      if (text !== undefined) {
        expect(instance.val(), 'the editor value on load').to.include(text);
      }
    });
  };

  // To the source mode and back to the WYSIWYG mode with the button of the toolbar.
  const switchModes = () => {
    cy.get('#postform .sceditor-button-source').first().click();
    editor().then((instance) => expect(instance.sourceMode(), 'the source mode').to.equal(true));
    cy.get('#postform .sceditor-button-source').first().click();
    editor().then((instance) => expect(instance.sourceMode(), 'the WYSIWYG mode').to.equal(false));
  };

  before(() => {
    // The form of the template Aurelia with the SCEditor: the site may run another template or editor.
    cy.kunena_getOption('template').then((option) => {
      oldTemplate = option;
      templateSaved = true;
    });
    cy.kunena_setOption('template', 'aurelia');
    cy.readFile(params).then((content) => {
      original = content;

      const line = 'editorType="sceditor"';

      cy.writeFile(params, /^editorType=.*$/m.test(content) ? content.replace(/^editorType=.*$/m, line) : `${content.trimEnd()}\n${line}\n`);
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  after(() => {
    cy.then(() => {
      if (original !== undefined) {
        cy.writeFile(params, original);
      }
    });
    cy.then(() => {
      if (templateSaved) {
        cy.kunena_setOption('template', oldTemplate === undefined ? null : oldTemplate);
      }
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  beforeEach(() => {
    ids = {};
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  });

  // The topics of the test (the one written with the form and the ones inserted), with their messages.
  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.kunena_fixUserCounters();
    cy.kunena_fixCategoryCounters();
  });

  // A topic with its message by the administrator, with the given text.
  const setUp = (text) => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`);
    });
  };

  const openEditForm = (text) => {
    cy.then(() => cy.visit(`${base}&view=topic&layout=edit&catid=3&id=${ids.topic}&mesid=${ids.message}`));
    ready(text);
  };

  // Opens the create-topic form with an empty editor in the WYSIWYG mode.
  const openCreateForm = () => {
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    ready();
    body().should('not.be.empty');
    editor().then((instance) => {
      instance.sourceMode(false);
      instance.val('');
    });
  };

  // Fills the drop-down of the map button and clicks its insert button.
  const insertMap = (type, zoom, city) => {
    cy.get('#postform .sceditor-button-map').first().click();
    cy.get('.sceditor-insertmap #type-select').select(type);
    cy.get('.sceditor-insertmap #zoom-select').select(zoom);

    if (city) {
      cy.get('.sceditor-insertmap #city').type(city);
    }

    cy.get('.sceditor-insertmap .button').click();
  };

  const savedMessage = () => cy.task('queryDB', `SELECT t.id AS topic, t.first_post_id AS message, x.message AS text FROM #__kunena_topics AS t INNER JOIN #__kunena_messages_text AS x ON x.mesid = t.first_post_id WHERE t.subject = '${subject}'`)
    .then((rows) => {
      expect(rows, 'the saved topic').to.have.length(1);

      return rows[0];
    });

  it('inserts a map, keeps it through a switch of the mode, a save and the edit form', () => {
    openCreateForm();
    insertMap('roadmap', '10', 'Amsterdam');

    value().should('include', inserted);
    body().then(($body) => {
      const map = $body.find('[data-kunena-map]');

      expect(map, 'the placeholder').to.have.length(1);
      expect(map.text(), 'the place name of the placeholder').to.equal('Amsterdam');
      expect($body.find('img, iframe'), 'no image or frame').to.have.length(0);
    });

    value().then((before) => {
      switchModes();
      value().should('equal', before).and('include', inserted);
    });

    cy.get('#postform input[name="subject"]').type(subject);
    cy.get('#form_submit_button').should('not.be.disabled').click();
    cy.location('href').should('not.contain', 'layout=create');

    savedMessage().then((row) => {
      expect(row.text, 'the stored message').to.include(inserted);
      ids.topic = row.topic;
      ids.message = row.message;
      openEditForm(inserted);
      cy.get('#form_submit_button').should('not.be.disabled').click();
      cy.location('href').should('not.contain', 'layout=edit');
      savedMessage().its('text').should('equal', row.text);
    });
  });

  it('keeps a map without attributes', () => {
    const text = '[map]Paris, France[/map]';

    setUp(text);
    openEditForm(text);
    value().then((loaded) => expect(loaded.trim(), 'the editor value on load').to.equal(text));
    body().find('[data-kunena-map]').should('have.text', 'Paris, France');
    switchModes();
    value().then((switched) => expect(switched.trim(), 'the editor value after the switch').to.equal(text));
  });

  it('shows the place name with HTML characters as text and gives it back unchanged', () => {
    const text = '[map type=hybrid zoom=4]A & <b>B</b> "C"[/map]';

    setUp(text);
    openEditForm(text);
    body().then(($body) => {
      expect($body.find('b'), 'no b element').to.have.length(0);
      expect($body.find('[data-kunena-map]').text(), 'the place name of the placeholder').to.equal('A & <b>B</b> "C"');
    });
    value().then((loaded) => expect(loaded.trim(), 'the editor value').to.equal(text));
  });

  // The text comes from the stored message, not from val(): a text set with val() right after the load of the create form was
  // gone by the time of the check (the editor held '' again), which is not what this test is about.
  it('inserts nothing when the city is empty', () => {
    const text = 'Some text.';

    setUp(text);
    openEditForm(text);
    value().then((before) => {
      expect(before.trim(), 'the editor value before').to.equal(text);
      insertMap('hybrid', '8');
      value().should('equal', before).and('not.include', '[map');
    });
  });
});
