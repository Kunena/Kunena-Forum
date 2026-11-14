// The [instagram], [tweet] (button x_social), [ebay] and [soundcloud] buttons of the SCEditor (issue #10436): each tag is shown in the
// WYSIWYG mode as a placeholder with its text and the tooltip of its button as title (no image, no frame), and gives the same BBCode
// back after a switch to the source mode and back, after a save and in the edit form; an attribute and HTML characters stay as they
// are. Cypress fails a test on an uncaught exception of the page, so a switch of the mode which throws fails too. The forum template
// and the editor of the site are put back.
describe('Kunena SCEditor: the [instagram], [tweet], [ebay] and [soundcloud] BBCodes', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress SCEditor embeds ${stamp}`;
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/aurelia/config/params.ini`;
  // The button, the field of its drop-down, the tag it inserts, the text typed and the tooltip shown as title of the placeholder.
  const embeds = [
    { button: 'instagram', field: 'instagram_id', tag: 'instagram', text: 'https://www.instagram.com/p/abc/', title: 'Insert an instagram item' },
    { button: 'x_social', field: 'tweet_id', tag: 'tweet', text: '1234567890', title: 'Insert a tweet' },
    { button: 'ebay', field: 'ebay_id', tag: 'ebay', text: '123456789012', title: 'Insert ebay item' },
    { button: 'soundcloud', field: 'soundcloud_id', tag: 'soundcloud', text: 'https://soundcloud.com/a/b', title: 'Insert soundcloud item' },
  ];
  const bbcode = (embed) => `[${embed.tag}]${embed.text}[/${embed.tag}]`;
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

  // Fills the drop-down of the button and clicks its insert button.
  const insertEmbed = (embed) => {
    cy.get(`#postform .sceditor-button-${embed.button}`).first().click();
    cy.get(`.sceditor-dropdown #${embed.field}`).type(embed.text);
    cy.get('.sceditor-dropdown .button').click();
  };

  const savedMessage = () => cy.task('queryDB', `SELECT t.id AS topic, t.first_post_id AS message, x.message AS text FROM #__kunena_topics AS t INNER JOIN #__kunena_messages_text AS x ON x.mesid = t.first_post_id WHERE t.subject = '${subject}'`)
    .then((rows) => {
      expect(rows, 'the saved topic').to.have.length(1);

      return rows[0];
    });

  embeds.forEach((embed) => {
    it(`inserts [${embed.tag}] with the ${embed.button} button as a placeholder and keeps it through a switch of the mode`, () => {
      openCreateForm();
      insertEmbed(embed);

      value().should('include', bbcode(embed));
      body().then(($body) => {
        const placeholder = $body.find(`[data-kunena-${embed.tag}]`);

        expect(placeholder, 'the placeholder').to.have.length(1);
        expect(placeholder.text(), 'the text of the placeholder').to.equal(embed.text);
        expect(placeholder.attr('title'), 'the title of the placeholder').to.equal(embed.title);
        expect($body.find('img, iframe'), 'no image or frame').to.have.length(0);
      });

      value().then((before) => {
        switchModes();
        value().should('equal', before).and('include', bbcode(embed));
      });
    });
  });

  // The four tags are inserted in the source mode: in the WYSIWYG mode the caret stays inside the placeholder just inserted (as after
  // [map] or a quote), so the next insert would go inside it.
  it('keeps the four tags through a save and the edit form', () => {
    openCreateForm();
    cy.get('#postform .sceditor-button-source').first().click();
    editor().then((instance) => expect(instance.sourceMode(), 'the source mode').to.equal(true));
    embeds.forEach((embed) => insertEmbed(embed));
    cy.get('#postform .sceditor-button-source').first().click();
    editor().then((instance) => expect(instance.sourceMode(), 'the WYSIWYG mode').to.equal(false));

    value().then((inserted) => {
      embeds.forEach((embed) => expect(inserted, `the editor value with [${embed.tag}]`).to.include(bbcode(embed)));
    });
    body().then(($body) => {
      embeds.forEach((embed) => expect($body.find(`[data-kunena-${embed.tag}]`), `the placeholder of [${embed.tag}]`).to.have.length(1));
    });

    cy.get('#postform input[name="subject"]').type(subject);
    cy.get('#form_submit_button').should('not.be.disabled').click();
    cy.location('href').should('not.contain', 'layout=create');

    savedMessage().then((row) => {
      embeds.forEach((embed) => expect(row.text, `the stored message with [${embed.tag}]`).to.include(bbcode(embed)));
      ids.topic = row.topic;
      ids.message = row.message;
      openEditForm(bbcode(embeds[0]));
      cy.get('#form_submit_button').should('not.be.disabled').click();
      cy.location('href').should('not.contain', 'layout=edit');
      savedMessage().its('text').should('equal', row.text);
    });
  });

  [
    { tag: 'tweet', text: '[tweet]1234567890[/tweet]', content: '1234567890', title: 'Insert a tweet' },
    { tag: 'ebay', text: '[ebay]123456789012[/ebay]', content: '123456789012', title: 'Insert ebay item' },
    { tag: 'soundcloud', text: '[soundcloud]https://soundcloud.com/a/b[/soundcloud]', content: 'https://soundcloud.com/a/b', title: 'Insert soundcloud item' },
    { tag: 'instagram', text: '[instagram colortext=red]https://www.instagram.com/p/abc/[/instagram]', content: 'https://www.instagram.com/p/abc/', title: 'Insert an instagram item' },
  ].forEach((stored) => {
    it(`keeps a stored ${stored.text} in the edit form`, () => {
      setUp(stored.text);
      openEditForm(stored.text);
      value().then((loaded) => expect(loaded.trim(), 'the editor value on load').to.equal(stored.text));
      body().find(`[data-kunena-${stored.tag}]`).should('have.text', stored.content).and('have.attr', 'title', stored.title);
      body().then(($body) => expect($body.find('img, iframe'), 'no image or frame').to.have.length(0));
      switchModes();
      switchModes();
      value().then((switched) => {
        expect(switched.trim(), 'the editor value after two switches').to.equal(stored.text);
        expect(switched, 'no [img]').not.to.include('[img');
      });
    });
  });

  it('shows HTML characters in the tag as text and gives them back unchanged', () => {
    const content = 'A & <b>B</b> "C" <img src=x onerror=alert(1)>';
    const text = `[soundcloud]${content}[/soundcloud]`;

    setUp(text);
    openEditForm(text);
    body().then(($body) => {
      expect($body.find('b'), 'no b element').to.have.length(0);
      expect($body.find('img'), 'no img element').to.have.length(0);
      expect($body.find('[data-kunena-soundcloud]').text(), 'the text of the placeholder').to.equal(content);
    });
    value().then((loaded) => expect(loaded.trim(), 'the editor value').to.equal(text));
  });
});
