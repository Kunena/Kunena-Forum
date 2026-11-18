// "Go to editor" of the quick reply copies the typed text to the full form of the reply (localStorage copyKunenaeditor), where it
// is the text of the message. With CKEditor the editor of the private part took it when it was ready first (the setting
// "Load Localstorage", "0" by default, is a string which is true), so it landed on the tab of the private part; with SCEditor it was
// lost: it was set into the editor just after it was made, before its frame was loaded. Both editors of the template Agora.
describe('Kunena quick reply: the text goes to the full editor', () => {
  const base = '/index.php?option=com_kunena';
  const prefix = `Cypress quick reply to editor ${Date.now()}`;
  const admin = 797;
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/agora/config/params.ini`;
  const ids = {};
  const old = {};

  // The editor of the template Agora: the ini file gets the value, the cache of the template is removed.
  const useEditor = (editor) => {
    const line = `editorType="${editor}"`;
    const text = /^editorType=.*$/m.test(old.params) ? old.params.replace(/^editorType=.*$/m, line) : `${old.params.trimEnd()}\n${line}\n`;

    return cy.writeFile(params, text).then(() => cy.task('deleteRelativePath', 'cache/com_kunena_template'));
  };

  // Types in the quick reply of the message and follows "Go to editor".
  const goToEditor = (text) => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.visit(`${base}&view=topic&catid=3&id=${ids.topic}`);
    cy.get(`[data-bs-target="#kreply${ids.message}_form"]`).filter(':visible').first().click();
    cy.get(`#editor${ids.message}`).should('be.visible').type(text);
    cy.get(`#qrlocalstorage${ids.message}`).click();
    cy.location('pathname').should('include', '/reply');
  };

  before(() => {
    cy.kunena_setOption('template', 'agora').then((value) => {
      old.template = value;
    });
    cy.kunena_setOption('quickReply', '1').then((value) => {
      old.quickReply = value;
    });
    // The form of the reply has the private part, whose editor took the text.
    cy.kunena_setOption('privateMessage', '1').then((value) => {
      old.privateMessage = value;
    });
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
    cy.then(() => cy.kunena_setOption('quickReply', old.quickReply ?? null));
    cy.then(() => cy.kunena_setOption('privateMessage', old.privateMessage ?? null));
  });

  beforeEach(() => {
    // A topic of the administrator with one message, which he answers.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${prefix}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, 3, 'author', ${admin}, '', '${prefix}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${ids.message}, 'A text.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.message}, last_post_id = ${ids.message} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${admin}, ${ids.topic}, 3, 1, ${ids.message}, 1, 0, 0, '')`);
    });
  });

  afterEach(() => {
    cy.window().then((win) => win.localStorage.removeItem('copyKunenaeditor'));
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('puts the text into the message of CKEditor, not into the private part', () => {
    const text = `quick text ckeditor ${Date.now()}`;

    useEditor('ckeditor');
    goToEditor(text);
    cy.editor_ready();
    cy.editor_ready('message_private');
    cy.window().should((win) => {
      expect(win.KunenaEditor.get('message').getData(), 'the message').to.contain(text);
      expect(win.KunenaEditor.get('message_private').getData(), 'the private part').to.not.contain(text);
      expect(win.localStorage.getItem('copyKunenaeditor'), 'the copy is used').to.eq(null);
    });
    cy.get('#form_submit_button').should('not.be.disabled');
  });

  it('puts the text into the message of SCEditor', () => {
    const text = `quick text sceditor ${Date.now()}`;

    useEditor('sceditor');
    goToEditor(text);
    // After the frame of the editor was loaded: the text was lost then.
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('contain.text', text);
    cy.window().should((win) => {
      expect(win.sceditor.instance(win.document.getElementById('message')).val(), 'the message').to.contain(text);
      expect(win.localStorage.getItem('copyKunenaeditor'), 'the copy is used').to.eq(null);
    });
    cy.get('#form_submit_button').should('not.be.disabled');
  });
});
