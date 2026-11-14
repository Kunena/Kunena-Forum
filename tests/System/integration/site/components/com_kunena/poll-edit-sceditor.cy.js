// The poll dialog of the SCEditor on the form to edit the first message of a topic (issue #10096): it shows the poll of the topic,
// sends the existing options under their own ids (so they keep their votes) and the added ones as new<N>, and replaces its fields
// in #poll_options at every insert. On the form of a new topic it opens empty. The spec switches the editor of the template Agora
// for its run and puts the file of the options back.
describe('Kunena polls: the poll dialog of the SCEditor edits the existing poll', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const htmlText = 'Ja & "nee" <b>';
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/agora/config/params.ini`;
  const ids = {};
  const old = {};

  const addMessage = (parent, author, subject) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, 3, 'author', ${author}, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A text.')`).then(() => message.insertId));

  const options = () => cy.task('queryDB', `SELECT id, text, votes FROM #__kunena_polls_options WHERE pollid = ${ids.poll} ORDER BY id`);

  // The save strips HTML tags from the option texts: the tests which save give the option with HTML characters a text which the
  // save keeps as it is, so an unchanged option keeps its text.
  const keepableTexts = () => cy.task('queryDB', `UPDATE #__kunena_polls_options SET text = 'Ja & "nee"' WHERE id = ${ids.options[1]}`);

  // The owner opens the form to edit the first message of his topic.
  const openEdit = () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&layout=edit&catid=3&id=${ids.topic}&mesid=${ids.first}`);
  };

  const openDialog = () => {
    cy.get('#postform .sceditor-container .sceditor-button-poll').first().should('not.have.class', 'disabled').click();
    cy.get('#polltitle').should('be.visible');
  };
  const insert = () => {
    cy.get('#polltitle').closest('.sceditor-dropdown').find('input.button').click();
    cy.get('#polltitle').should('not.exist');
  };
  // The page after the save says that it succeeded (an error sends the form back, under a SEF address too).
  const save = () => {
    cy.get('#form_submit_button').should('not.be.disabled').click();
    cy.contains('The message has been successfully edited.').should('exist');
  };

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
      const line = 'editorType="sceditor"';
      const changed = /^editorType=.*$/m.test(text) ? text.replace(/^editorType=.*$/m, line) : `${text.trimEnd()}\n${line}\n`;

      return cy.writeFile(params, changed);
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
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
    // A topic of the user with a poll nobody has voted in, and a reply of the administrator.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress poll sceditor ${stamp}', 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${user.id}, 'x', '', 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return addMessage(0, user.id, `Cypress poll sceditor ${stamp}`);
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
      cy.task('queryDB', `INSERT INTO #__kunena_polls_options (pollid, text, votes) VALUES (${poll.insertId}, 'Yes', 0), (${poll.insertId}, '${htmlText}', 0)`);

      return options();
    }).then((rows) => {
      ids.options = rows.map((row) => row.id);
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

  it('shows the title, the life span and the options of the poll, the HTML characters as text', () => {
    openEdit();
    openDialog();
    cy.get('#polltitle').should('have.value', 'The poll of the owner');
    cy.get('#ckeditor_dialog_polltimetolive').invoke('val').then((value) => {
      cy.get('#polllifespan').should('have.value', value);
    });
    cy.get('#poll_option1').should('have.value', 'Yes').and('have.attr', 'data-poll-option-id', String(ids.options[0]));
    cy.get('#poll_option2').should('have.value', htmlText).and('have.attr', 'data-poll-option-id', String(ids.options[1]));
    cy.get('#poll_option3').should('not.exist');
    cy.get('#poll_option_label2').should('have.text', 'Poll option 2');
    cy.get('#sceditor_poll_options b').should('not.exist');
  });

  it('sends the existing options once under their ids, also after a second insert, and keeps them on the save', () => {
    keepableTexts();
    options().then((before) => {
      openEdit();
      openDialog();
      insert();
      openDialog();
      cy.get('#poll_option2').should('have.value', 'Ja & "nee"');
      insert();
      cy.get('#poll_options input[name="poll_title"]').should('have.length', 1).and('have.value', 'The poll of the owner');
      cy.get('#poll_options input[name="poll_time_to_live"]').should('have.length', 1);
      cy.get('#poll_options input[name^="polloptionsID"]').should('have.length', 2);
      cy.get(`#poll_options input[name="polloptionsID[${ids.options[0]}]"]`).should('have.length', 1).and('have.value', 'Yes');
      cy.get(`#poll_options input[name="polloptionsID[${ids.options[1]}]"]`).should('have.length', 1).and('have.value', 'Ja & "nee"');
      save();
      options().should('deep.equal', before);
    });
  });

  it('adds an option after the existing ones', () => {
    keepableTexts();
    options().then((before) => {
      openEdit();
      openDialog();
      cy.get('#polltitle').closest('.sceditor-dropdown').find('.addpolloption').click();
      cy.get('#poll_option_label3').should('have.text', 'Poll option 3');
      cy.get('#poll_option3').should('not.have.attr', 'data-poll-option-id');
      cy.get('#poll_option3').type('Maybe');
      insert();
      cy.get('#poll_options input[name="polloptionsID[new3]"]').should('have.value', 'Maybe');
      save();
      options().then((after) => {
        expect(after, 'the options').to.have.length(3);
        expect(after.slice(0, 2), 'the old options').to.deep.equal(before);
        expect(after[2].text, 'the new option').to.eq('Maybe');
      });
    });
  });

  it('removes the last existing option', () => {
    keepableTexts();
    options().then((before) => {
      openEdit();
      openDialog();
      cy.get('#polltitle').closest('.sceditor-dropdown').find('.removepolloption').click();
      cy.get('#poll_option2').should('not.exist');
      cy.get('#poll_option1').should('have.value', 'Yes');
      insert();
      cy.get(`#poll_options input[name="polloptionsID[${ids.options[1]}]"]`).should('not.exist');
      cy.get(`#poll_options input[name="polloptionsID[${ids.options[0]}]"]`).should('have.value', 'Yes');
      save();
      options().should('deep.equal', before.slice(0, 1));
    });
  });

  it('opens empty on the form of a new topic', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    openDialog();
    cy.get('#polltitle').should('have.value', '');
    cy.get('#polllifespan').should('have.value', '');
    cy.get('#poll_option1').should('not.exist');
  });
});
