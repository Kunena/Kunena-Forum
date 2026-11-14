// A [table] in a message gets bordered cells and set apart header cells, in Aurelia and in Agora; the layout table of [columns]
// stays without borders. In Aurelia a [code] block which highlight.js has not coloured gets a light background and padding.
describe('Kunena BBCode: borders on message tables and a background on plain code blocks', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  // The compiled style sheets, which the site makes again from the SCSS of the checkout when they are missing. Only these files
  // are deleted, never their folders: the folders of Kunena on the site are links into a checkout.
  const stylesheets = ['media/kunena/cache/aurelia/css/kunena.css', 'media/kunena/cache/agora/css/kunena.css'];
  const ids = { messages: [] };
  const texts = [
    '[table][tr][th]Header cell[/th][/tr][tr][td]Data cell[/td][/tr][/table]',
    '[columns]Left column[nextcol]Right column[/columns]',
    '[code=php]<?php echo 1; ?>[/code]',
  ];
  let highlightCode;

  const sql = (text) => text.replace(/\\/g, '\\\\').replace(/'/g, "''");
  const addMessage = (parent, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, ${ids.category}, 'ci-admin', 797, '', 'Cypress table styles ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${sql(text)}')`).then(() => {
    ids.messages.push(message.insertId);

    return message.insertId;
  }));
  const style = ($element) => $element[0].ownerDocument.defaultView.getComputedStyle($element[0]);
  const openTopic = (template) => {
    cy.setCookie('kunena_template', template);
    cy.then(() => cy.visit(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`));
  };
  const messageTable = () => cy.get('.kmsg table:not(.bbcode_columns)');
  const cells = () => messageTable().find('th, td');
  const columns = () => cy.get('.kmsg table.bbcode_columns td.bbcode_column');
  const codeBlock = () => cy.contains('pre.bbcode_code_body > code', '<?php echo 1; ?>');
  // The colour --agora-border resolves to, read from an element inside #kunena which takes its border from it.
  const agoraBorder = () => cy.get('#kunena').then(($kunena) => {
    const probe = $kunena[0].ownerDocument.createElement('div');

    probe.style.borderTop = '1px solid var(--agora-border)';
    $kunena[0].appendChild(probe);
    const colour = style(Cypress.$(probe)).borderTopColor;
    probe.remove();

    return colour;
  });

  beforeEach(() => {
    ids.messages = [];
    cy.kunena_getOption('highlightCode').then((value) => {
      highlightCode = value;
    });

    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, review, params, iconset) VALUES (1, 'Cypress table styles ${stamp}', 'cypress-table-styles-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, 0, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-table-styles-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Cypress table styles ${stamp}', 0, 0, 0, 0, 0, ${texts.length}, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return addMessage(0, texts[0]);
    }).then((first) => {
      texts.slice(1).forEach((text) => addMessage(first, text));
      cy.then(() => {
        const last = ids.messages[ids.messages.length - 1];

        cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${first}, last_post_id = ${last} WHERE id = ${ids.topic}`);
        cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 1, numPosts = ${texts.length} WHERE id = ${ids.category}`);
      });
    });

    stylesheets.forEach((stylesheet) => cy.task('deleteRelativePath', stylesheet));
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  });

  afterEach(() => {
    cy.clearCookie('kunena_template');
    cy.then(() => cy.kunena_setOption('highlightCode', highlightCode === undefined ? null : highlightCode));
    cy.then(() => {
      const messages = ids.messages.join(', ');

      cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid IN (${messages})`);
      cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id IN (${messages})`);
    });
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-table-styles-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.kunena_fixUserCounters();
  });

  // The site compiles the style sheets again from the checkout which it points at next.
  after(() => {
    stylesheets.forEach((stylesheet) => cy.task('deleteRelativePath', stylesheet));
  });

  it('borders the cells of a message table and sets the header cells apart (Aurelia)', () => {
    openTopic('aurelia');
    cy.get('#kunena.layout', { log: false }).should('not.have.class', 'agora');

    cells().should('have.length', 2).each(($cell) => {
      const cell = style($cell);

      expect(cell.borderTopWidth).to.equal('1px');
      expect(cell.borderTopStyle).to.not.equal('none');
      expect(parseFloat(cell.paddingTop)).to.be.greaterThan(0);
      expect(parseFloat(cell.paddingLeft)).to.be.greaterThan(0);
    });

    messageTable().find('th').then(($th) => {
      messageTable().find('td').then(($td) => {
        expect(style($th).backgroundColor).to.not.equal(style($td).backgroundColor);
      });
    });

    columns().should('have.length', 2).each(($column) => {
      const column = style($column);

      expect(column.borderTopWidth === '0px' || column.borderTopStyle === 'none').to.equal(true);
    });
  });

  it('borders the cells with the colours of Agora, in the light and in the dark scheme', () => {
    openTopic('agora');
    cy.get('#kunena.layout.agora').should('exist');

    // A wide table still scrolls sideways.
    messageTable().should(($table) => {
      expect(style($table).display).to.equal('block');
      expect(style($table).overflowX).to.equal('auto');
    });

    columns().should('have.length', 2).each(($column) => {
      const column = style($column);

      expect(column.borderTopWidth === '0px' || column.borderTopStyle === 'none').to.equal(true);
    });

    // The scheme follows the device by default, which may be dark: start from the light scheme.
    cy.get('#kunena').invoke('attr', 'data-bs-theme', 'light');
    agoraBorder().then((light) => {
      messageTable().find('td').should(($td) => {
        expect(style($td).borderTopWidth).to.equal('1px');
        expect(style($td).borderTopColor).to.equal(light);
      });

      cy.get('#kunena').invoke('attr', 'data-bs-theme', 'dark');
      agoraBorder().then((dark) => {
        expect(dark).to.not.equal(light);
        messageTable().find('td').should(($td) => {
          expect(style($td).borderTopColor).to.equal(dark);
        });
      });
    });
  });

  it('gives a plain code block a light background and padding (Aurelia)', () => {
    cy.kunena_setOption('highlightCode', '0');
    openTopic('aurelia');
    cy.get('#kunena.layout', { log: false }).should('not.have.class', 'agora');

    codeBlock().should('not.have.class', 'hljs').parent('pre').should(($pre) => {
      const pre = style($pre);

      expect(pre.backgroundColor).to.not.be.oneOf(['rgba(0, 0, 0, 0)', 'transparent', 'rgb(13, 17, 23)']);
      expect(parseFloat(pre.paddingTop)).to.be.greaterThan(0);
    });
  });

  it('keeps the dark theme of a highlighted code block (Aurelia)', () => {
    cy.kunena_setOption('highlightCode', '1');
    openTopic('aurelia');
    cy.get('#kunena.layout', { log: false }).should('not.have.class', 'agora');

    codeBlock().should('have.class', 'hljs').parent('pre').should(($pre) => {
      expect(style($pre).backgroundColor).to.equal('rgb(13, 17, 23)');
      expect(style($pre).paddingTop).to.equal('0px');
    });
  });
});
