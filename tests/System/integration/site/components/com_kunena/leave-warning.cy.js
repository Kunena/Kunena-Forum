// The posting form warns before it is left with unsent text (issue #6856): assets/js/leavewarning.js prevents the beforeunload of
// the page when the subject or the message text was changed, a submit leaves without a warning, and Cancel asks once
// (COM_KUNENA_POST_CANCEL_CONFIRM) before it empties the form and goes back. A form shown again after a failed post warns from the
// start. Cypress itself accepts the browser's leave dialog, so the spec reads defaultPrevented of the beforeunload event: whether
// the browser would show its dialog. The rows are inserted by the spec; no POST which would write content reaches the site.
describe('Kunena writing: a warning before leaving the posting form', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const alias = `cypress-leave-warning-${stamp}`;
  const confirmText = 'Leave this form? The text you have written will be lost.';
  const ids = {};
  const garbageAttachment = 999999991;

  const editorType = () => cy.window().then((win) => win.Joomla.getOptions('com_kunena.editor_type'));
  // Waits until the message editor is ready, so that leavewarning.js has taken its baseline.
  const editorReady = () => editorType().then((type) => {
    if (type === 'ckeditor') {
      cy.window().should((win) => {
        expect(win.CKEDITOR.instances.message && win.CKEDITOR.instances.message.status, 'the editor').to.eq('ready');
      });
    } else {
      cy.window().should('have.property', 'sceditor');
      cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty');
    }
  }).wait(100);
  const editorBody = () => editorType().then((type) => cy.get(type === 'ckeditor' ? '#cke_message iframe.cke_wysiwyg_frame' : '#postform .sceditor-container iframe')
    .first().its('0.contentDocument.body').should('not.be.empty'));
  const typeInEditor = (text) => editorBody().then(($body) => cy.wrap($body).click().type(text, { parseSpecialCharSequences: false }));
  const editorText = () => editorType().then((type) => cy.window().then((win) => (type === 'ckeditor'
    ? win.CKEDITOR.instances.message.getData()
    : win.sceditor.instance(win.document.getElementById('message')).val())));
  // Whether a beforeunload of the page now would show the browser's dialog.
  const leaveIsPrevented = (win) => {
    const event = new win.Event('beforeunload', { cancelable: true });

    win.dispatchEvent(event);

    return event.defaultPrevented;
  };
  const shouldWarn = (expected) => cy.window().should((win) => {
    expect(leaveIsPrevented(win), expected ? 'the page warns' : 'the page does not warn').to.eq(expected);
  });
  // Records defaultPrevented of the real beforeunload of the page, after the listener of leavewarning.js.
  const recordLeave = () => {
    const record = [];

    cy.window().then((win) => win.addEventListener('beforeunload', (event) => record.push(event.defaultPrevented)));

    return cy.wrap(record);
  };
  const openNewTopic = () => {
    cy.visit(`${base}&view=category&catid=${ids.category}`);
    cy.location('href').as('previous');
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    cy.get('#postform').should('have.attr', 'data-kunena-leave-warning');
    editorReady();
  };

  beforeEach(() => {
    cy.task('queryDB', `SELECT id, username FROM #__users WHERE username = '${Cypress.expose('username')}'`).then((users) => {
      ids.user = users[0].id;
      ids.username = users[0].username;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress leave warning ${stamp}', '${alias}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, '{}', 'default')`,
      );
    }).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('${alias}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Leave warning ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${ids.user}, 'x', '', 0, UNIX_TIMESTAMP(), ${ids.user}, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, '${ids.username}', ${ids.user}, '', 'Leave warning ${stamp}', UNIX_TIMESTAMP(), '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'The [b]stored[/b] text of the message.')`);
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

  it('does not warn on an untouched form, and Cancel goes back without a question', () => {
    const confirm = cy.stub().returns(true);

    cy.on('window:confirm', confirm);
    openNewTopic();
    shouldWarn(false);
    recordLeave().as('leaves');
    cy.get('#form_cancel_button').should('have.attr', 'type', 'button').click();
    cy.get('@previous').then((previous) => cy.location('href').should('eq', previous));
    cy.wrap(confirm).should('not.have.been.called');
    cy.get('@leaves').should('deep.eq', [false]);
  });

  it('warns after a subject is typed, and not after it is cleared again', () => {
    openNewTopic();
    cy.get('#postform input[name="subject"]').type(`Leave warning subject ${stamp}`);
    shouldWarn(true);
    cy.get('#postform input[name="subject"]').clear();
    shouldWarn(false);
  });

  it('warns after text is typed in the message editor', () => {
    openNewTopic();
    typeInEditor('Some text which is not sent.');
    shouldWarn(true);
  });

  it('keeps the form and its text when the question of Cancel is answered with Cancel', () => {
    const confirm = cy.stub().returns(false);
    const subject = `Leave warning kept ${stamp}`;

    cy.on('window:confirm', confirm);
    openNewTopic();
    cy.get('#postform input[name="subject"]').type(subject);
    typeInEditor('The text which stays.');
    cy.location('href').then((href) => {
      cy.get('#form_cancel_button').click();
      cy.wrap(confirm).should('have.been.calledOnceWith', confirmText);
      cy.location('href').should('eq', href);
    });
    cy.get('#postform input[name="subject"]').should('have.value', subject);
    editorText().should('contain', 'The text which stays.');
    shouldWarn(true);
  });

  it('goes back without a second question when the question of Cancel is answered with OK', () => {
    const confirm = cy.stub().returns(true);

    cy.on('window:confirm', confirm);
    openNewTopic();
    cy.get('#postform input[name="subject"]').type(`Leave warning left ${stamp}`);
    typeInEditor('The text which is thrown away.');
    recordLeave().as('leaves');
    cy.get('#form_cancel_button').click();
    cy.get('@previous').then((previous) => cy.location('href').should('eq', previous));
    cy.wrap(confirm).should('have.been.calledOnceWith', confirmText);
    cy.get('@leaves').should('deep.eq', [false]);
  });

  // A garbage id only: no real upload is made, and the answer of the site is stubbed.
  const injectUpload = () => cy.get('#postform #kattach-list').then(($list) => {
    $list.append(`<input type="hidden" name="attachments[${garbageAttachment}]" value="1">`);
  });
  // With SEF URLs the task is a path segment (task-removependingattachments), not a query parameter.
  const interceptCancelRemoval = () => cy.intercept({ method: 'POST', url: /removependingattachments/ }, { statusCode: 200, body: { removed: 0 } }).as('cancelRemoval');

  it('sends the uploads of the form to the site on Cancel, and goes back', () => {
    interceptCancelRemoval();
    openNewTopic();
    injectUpload();
    cy.get('#form_cancel_button').click();
    cy.wait('@cancelRemoval').then((interception) => {
      const body = typeof interception.request.body === 'string' ? interception.request.body : new TextDecoder().decode(interception.request.body);

      expect(new URLSearchParams(body).getAll('ids[]'), 'the ids').to.deep.eq([String(garbageAttachment)]);
    });
    cy.get('@previous').then((previous) => cy.location('href').should('eq', previous));
  });

  it('sends no uploads when the question of Cancel is answered with Cancel', () => {
    const confirm = cy.stub().returns(false);

    cy.on('window:confirm', confirm);
    interceptCancelRemoval();
    openNewTopic();
    injectUpload();
    cy.get('#postform input[name="subject"]').type(`Leave warning upload kept ${stamp}`);
    cy.location('href').then((href) => {
      cy.get('#form_cancel_button').click();
      cy.wrap(confirm).should('have.been.calledOnce');
      cy.location('href').should('eq', href);
    });
    cy.get(`#postform input[name="attachments[${garbageAttachment}]"]`).should('exist');
    cy.get('@cancelRemoval.all').should('have.length', 0);
  });

  it('sends nothing on Cancel when the form has no uploads', () => {
    interceptCancelRemoval();
    openNewTopic();
    cy.get('#postform #kattach-list input[name^="attachments["]').should('not.exist');
    cy.get('#form_cancel_button').click();
    cy.get('@previous').then((previous) => cy.location('href').should('eq', previous));
    cy.get('@cancelRemoval.all').should('have.length', 0);
  });

  it('sends the form without a warning, and still warns when the validation of the browser blocks the submit', () => {
    const subject = `Leave warning sent ${stamp}`;

    // Nothing is written: the POST of the form is answered by Cypress with a redirect to a page of the site, as a real post is.
    // (An empty page as the answer has no @view-transition of the site template, and the browser rejects the skipped transition.)
    cy.intercept('POST', '**', (req) => {
      const body = typeof req.body === 'string' ? req.body : new TextDecoder().decode(req.body);

      if (body.includes('Leave warning')) {
        req.reply({ statusCode: 303, headers: { location: new URL(`${base.slice(1)}&view=category&catid=${ids.category}`, Cypress.config('baseUrl')).href } });
      }
    }).as('post');

    openNewTopic();
    typeInEditor('A topic without a subject at first.');
    cy.location('href').then((href) => {
      cy.get('#form_submit_button').should('not.be.disabled').click();
      cy.location('href').should('eq', href);
    });
    shouldWarn(true);

    cy.get('#postform input[name="subject"]').type(subject);
    recordLeave().as('leaves');
    cy.get('#form_submit_button').click();
    cy.wait('@post');
    cy.get('@leaves').should('deep.eq', [false]);
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      expect(Number(rows[0].n), 'no topic').to.eq(0);
    });
  });

  it('does not warn on an untouched edit form, and warns on the form returned after a failed save', () => {
    cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.message}`);
    cy.get('#postform').should('have.attr', 'data-kunena-leave-warning');
    cy.get('#postform').should('not.have.attr', 'data-kunena-unsent');
    editorReady();
    shouldWarn(false);

    cy.get('#postform input[name="subject"]').clear().type(`Leave warning changed ${stamp}`);
    // A token value which the filter of Joomla empties: edit() fails the token check and returns to the form.
    cy.get('#postform input[type="hidden"][value="1"]').filter((index, input) => /^[0-9a-f]{32}$/.test(input.name)).invoke('val', '***');
    cy.get('#form_submit_button').should('not.be.disabled').click();

    cy.get('#postform').should('have.attr', 'data-kunena-unsent', '1');
    cy.get('#postform input[name="subject"]').should('have.value', `Leave warning changed ${stamp}`);
    editorReady();
    shouldWarn(true);
    cy.task('queryDB', `SELECT m.subject, t.message FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON t.mesid = m.id WHERE m.id = ${ids.message}`).then((rows) => {
      expect(rows[0].subject, 'the subject in the database').to.eq(`Leave warning ${stamp}`);
      expect(rows[0].message, 'the text in the database').to.eq('The [b]stored[/b] text of the message.');
    });
  });
});
