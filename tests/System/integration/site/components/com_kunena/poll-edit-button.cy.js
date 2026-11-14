// The poll button of the editors on the form to edit the first message of a topic (issue #7730): when the poll may not be edited
// any more (somebody has voted and allowUserEditPoll is off), the button of the CKEditor and of the SCEditor is disabled, also after
// a switch to the source mode and back, and its tooltip says why. The button is only a hint: the save checks the poll again
// (poll-edit-rights.cy.js). The spec switches the editor of the template Agora for its run and puts the file of the options back.
describe('Kunena polls: the poll button of a poll which may not be edited', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const notice = 'Poll cannot be edited once someone has voted in it!';
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/agora/config/params.ini`;
  const ids = {};
  const old = {};

  const addMessage = (parent, author, subject) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, 3, 'author', ${author}, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A text.')`).then(() => message.insertId));

  // The editor of the template Agora: the ini file gets the value, the cache of the template is removed.
  const useEditor = (editor) => {
    const line = `editorType="${editor}"`;
    const text = /^editorType=.*$/m.test(old.params) ? old.params.replace(/^editorType=.*$/m, line) : `${old.params.trimEnd()}\n${line}\n`;

    return cy.writeFile(params, text).then(() => cy.task('deleteRelativePath', 'cache/com_kunena_template'));
  };

  // The administrator has voted in the poll.
  const vote = () => cy.task('queryDB', `INSERT INTO #__kunena_polls_users (pollid, userid, votes, lasttime, lastvote) VALUES (${ids.poll}, ${admin}, 1, UTC_TIMESTAMP(), ${ids.option})`);

  // The owner opens the form to edit the first message of his topic.
  const openEdit = () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&layout=edit&catid=3&id=${ids.topic}&mesid=${ids.first}`);
  };

  const ckeditorReady = () => cy.window().should((win) => {
    expect(win.CKEDITOR && win.CKEDITOR.instances.message && win.CKEDITOR.instances.message.status, 'the editor').to.eq('ready');
  });
  const ckeditorMode = (mode) => cy.window().then((win) => new Cypress.Promise((resolve) => {
    win.CKEDITOR.instances.message.setMode(mode, resolve);
  }));
  const ckeditorPollDisabled = () => {
    cy.window().should((win) => {
      expect(win.CKEDITOR.instances.message.getCommand('polls').state, 'the state of the command polls').to.eq(win.CKEDITOR.TRISTATE_DISABLED);
    });
    cy.get('#cke_message .cke_button__polls').should('have.attr', 'aria-disabled', 'true');
  };

  const sceditorPoll = () => cy.get('#postform .sceditor-container .sceditor-button-poll').first();

  before(() => {
    cy.kunena_setOption('template', 'agora').then((value) => {
      old.template = value;
    });
    cy.kunena_setOption('pollEnabled', '1').then((value) => {
      old.pollEnabled = value;
    });
    cy.kunena_setOption('allowUserEditPoll', '0').then((value) => {
      old.allowUserEditPoll = value;
    });
    cy.task('queryDB', 'SELECT allowPolls FROM #__kunena_categories WHERE id = 3').then((rows) => {
      old.allowPolls = rows[0].allowPolls;
    });
    cy.task('queryDB', 'UPDATE #__kunena_categories SET allowPolls = 1 WHERE id = 3');
    cy.readFile(params).then((text) => {
      old.params = text;
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
    cy.then(() => cy.kunena_setOption('pollEnabled', old.pollEnabled ?? null));
    cy.then(() => cy.kunena_setOption('allowUserEditPoll', old.allowUserEditPoll ?? null));
    cy.then(() => {
      if (old.allowPolls !== undefined) {
        cy.task('queryDB', `UPDATE #__kunena_categories SET allowPolls = ${Number(old.allowPolls)} WHERE id = 3`);
      }
    });
  });

  beforeEach(() => {
    // A topic of the user with a poll, and a reply of the administrator.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress poll button ${stamp}', 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return addMessage(0, user.id, `Cypress poll button ${stamp}`);
    }).then((first) => {
      ids.first = first;

      return addMessage(first, admin, 'Re: a poll');
    }).then((reply) => {
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.first}, last_post_id = ${reply} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${user.id}, ${ids.topic}, 3, 1, ${ids.first}, 1, 0, 0, ''), (${admin}, ${ids.topic}, 3, 1, ${reply}, 0, 0, 0, '')`);

      return cy.task('queryDB', `INSERT INTO #__kunena_polls (title, threadid, polltimetolive) VALUES ('The poll of the owner', ${ids.topic}, '1000-01-01 00:00:00')`);
    }).then((poll) => {
      ids.poll = poll.insertId;
      cy.task('queryDB', `UPDATE #__kunena_topics SET poll_id = ${poll.insertId} WHERE id = ${ids.topic}`);

      return cy.task('queryDB', `INSERT INTO #__kunena_polls_options (pollid, text, votes) VALUES (${poll.insertId}, 'Yes', 1), (${poll.insertId}, 'No', 0)`);
    }).then((options) => {
      ids.option = options.insertId;
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_polls_users WHERE pollid = ${ids.poll}`);
    cy.task('queryDB', `DELETE FROM #__kunena_polls_options WHERE pollid = ${ids.poll}`);
    cy.task('queryDB', `DELETE FROM #__kunena_polls WHERE id = ${ids.poll}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('disables the poll button of the CKEditor, also after the source mode', () => {
    vote();
    useEditor('ckeditor');
    openEdit();
    ckeditorReady();
    ckeditorPollDisabled();
    cy.get('#cke_message .cke_button__polls').should('have.attr', 'title', notice);
    ckeditorMode('source');
    ckeditorPollDisabled();
    ckeditorMode('wysiwyg');
    ckeditorPollDisabled();
    cy.get('#cke_message .cke_button__polls').should('have.attr', 'title', notice);
  });

  it('disables the poll button of the SCEditor, also after the source mode, and opens no dialog', () => {
    vote();
    useEditor('sceditor');
    openEdit();
    sceditorPoll().should('have.class', 'disabled').and('have.attr', 'title', notice);
    cy.get('#postform .sceditor-container .sceditor-button-source').first().click();
    sceditorPoll().should('have.class', 'disabled');
    cy.get('#postform .sceditor-container .sceditor-button-source').first().click();
    sceditorPoll().should('have.class', 'disabled').and('have.attr', 'title', notice);
    sceditorPoll().click();
    cy.get('#polltitle').should('not.exist');
  });

  it('enables the poll button when nobody has voted', () => {
    useEditor('sceditor');
    openEdit();
    sceditorPoll().should('not.have.class', 'disabled');
    cy.get('#poll_exist_edit').should('exist');
  });
});
