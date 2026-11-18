// The parameters of [map] from the map dialogs: the dialog of CKEditor wrote the zoom as mode= (which nothing read, so every map had
// the zoom by default) and both dialogs offered the type SATELITE (which Google Maps does not know, so it showed the road map). The
// dialogs write zoom= and satellite now, and a map which was posted with mode= shows the chosen zoom.
describe('Kunena BBCode: the zoom and the type of [map]', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress map params ${stamp}`;
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/agora/config/params.ini`;
  const old = {};
  const ids = {};

  // A line of the ini file of the template Agora.
  const set = (text, name, value) => {
    const line = `${name}="${value}"`;
    const pattern = new RegExp(`^${name}=.*$`, 'm');

    return pattern.test(text) ? text.replace(pattern, line) : `${text.trimEnd()}\n${line}\n`;
  };
  const useEditor = (editor) => cy.then(() => cy.writeFile(params, set(set(old.params, 'editorType', editor), 'editorButtons', '')))
    .then(() => cy.task('deleteRelativePath', 'cache/com_kunena_template'));

  before(() => {
    cy.kunena_setOption('template', 'agora').then((value) => {
      old.template = value;
    });
    // OpenStreetMap: the zoom is in the address of the map.
    cy.kunena_setOption('mapProvider', 'osm').then((value) => {
      old.mapProvider = value;
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
    cy.then(() => cy.kunena_setOption('mapProvider', old.mapProvider ?? null));
  });

  afterEach(() => {
    cy.then(() => {
      if (ids.topic) {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
        delete ids.topic;
      }
    });
  });

  it('shows the zoom of a map which was posted with mode=', () => {
    const text = 'Old [map type=ROADMAP mode=4]Amsterdam[/map] and [map type=roadmap zoom=6]Amsterdam[/map]';

    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, 3, 'ci-admin', 797, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
    });

    cy.then(() => cy.request(`${base}&view=topic&catid=3&id=${ids.topic}`)).its('body').then((body) => {
      // The link to the larger map has the zoom: map=<zoom>/<latitude>/<longitude>.
      const zooms = [...body.matchAll(/[#?&;]map=(\d+)\//g)].map((match) => match[1]);

      expect(zooms, 'the zoom of each map').to.deep.eq(['4', '6']);
    });
  });

  it('writes zoom= and satellite with the map dialog of CKEditor', () => {
    useEditor('ckeditor');
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    cy.editor_ready();
    cy.editor_button('map').click();
    cy.get('.kunena-editor-dialog.show select').eq(0).select('SATELLITE');
    cy.get('.kunena-editor-dialog.show select').eq(1).select('4');
    cy.get('.kunena-editor-dialog.show input[type="text"]').type('Amsterdam');
    cy.get('.kunena-editor-dialog.show .btn-primary').click();
    cy.editor_data().should('contain', '[map type=SATELLITE zoom=4]Amsterdam[/map]');
  });

  it('offers satellite in the map dialog of SCEditor', () => {
    useEditor('sceditor');
    cy.writeFile(params, set(set(old.params, 'editorType', 'sceditor'), 'editorButtons', 'bold,map,source'));
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty');
    cy.get('#postform .sceditor-button-map').first().click();
    cy.get('.sceditor-insertmap #type-select option').then(($options) => {
      const values = [...$options].map((option) => option.value);

      expect(values, 'the types of the map').to.include('satellite').and.not.include('satelite');
    });
  });
});
