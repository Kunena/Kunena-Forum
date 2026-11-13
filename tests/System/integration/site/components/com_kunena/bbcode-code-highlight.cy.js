// [code] blocks are a <pre><code> block with the language as a class "language-...". With "Enable Code Highlighting" on, the
// bundled highlight.js colours them, loaded from the site only on a page which shows a code block.
describe('Kunena BBCode: code blocks coloured by highlight.js', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  // The compiled style sheets, which the site makes again from the SCSS of the checkout when they are missing. Only these files
  // are deleted, never their folders: the folders of Kunena on the site are links into a checkout.
  const stylesheets = ['media/kunena/cache/aurelia/css/kunena.css', 'media/kunena/cache/agora/css/kunena.css'];
  const ids = { messages: [] };
  const texts = [
    '[code=php]<?php echo 1; ?>[/code]',
    '[code=html]<b id="kcode-x">bold</b>[/code]',
    '[code]\n    indented[/code]',
    '[code type=x"onmouseover="alert(1)]text[/code]',
    '[code=nolanguage]plain[/code]',
  ];
  // A member with a code block in the signature: the signature is shown under the message, outside .kmsg.
  const signature = '[code=php]$sig = 1;[/code]';
  let highlightCode;
  let signatures;

  const sql = (text) => text.replace(/\\/g, '\\\\').replace(/'/g, "''");
  const addMessage = (parent, text, userid = 797, name = 'ci-admin') => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, ${ids.category}, '${name}', ${userid}, '', 'Cypress code highlight ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${sql(text)}')`).then(() => {
    ids.messages.push(message.insertId);

    return message.insertId;
  }));
  const style = ($element) => $element[0].ownerDocument.defaultView.getComputedStyle($element[0]);
  const openTopic = (template) => {
    cy.setCookie('kunena_template', template);
    cy.then(() => cy.visit(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`));
  };
  const block = (text) => cy.contains('pre.bbcode_code_body > code', text);
  // The scripts of Kunena only: the debug bar of Joomla (debug on) brings a highlight.js of its own, in media/vendor/debugbar.
  const ownScripts = 'script[src*="/media/kunena/core/highlightjs/"], script[src*="/media/kunena/core/js/highlight-code.js"]';

  beforeEach(() => {
    ids.messages = [];
    cy.kunena_getOption('highlightCode').then((value) => {
      highlightCode = value;
    });
    cy.kunena_getOption('signature').then((value) => {
      signatures = value;
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Signature', 'ci-signature-${stamp}', 'ci-signature-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((user) => {
      ids.user = user.insertId;
      cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${user.insertId}, 2)`);
      cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts, signature) VALUES (${user.insertId}, 1, '${sql(signature)}')`);
    });

    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, review, params, iconset) VALUES (1, 'Cypress code highlight ${stamp}', 'cypress-code-highlight-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, 0, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-code-highlight-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Cypress code highlight ${stamp}', 0, 0, 0, 0, 0, ${texts.length + 1}, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return addMessage(0, texts[0]);
    }).then((first) => {
      texts.slice(1).forEach((text) => addMessage(first, text));
      cy.then(() => addMessage(first, 'A reply with a signature.', ids.user, `ci-signature-${stamp}`));
      cy.then(() => {
        const last = ids.messages[ids.messages.length - 1];

        cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${first}, last_post_id = ${last} WHERE id = ${ids.topic}`);
        cy.task('queryDB', `UPDATE #__kunena_categories SET numTopics = 1, numPosts = ${texts.length + 1} WHERE id = ${ids.category}`);
      });
    });

    stylesheets.forEach((stylesheet) => cy.task('deleteRelativePath', stylesheet));
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  });

  afterEach(() => {
    cy.clearCookie('kunena_template');
    cy.then(() => cy.kunena_setOption('highlightCode', highlightCode === undefined ? null : highlightCode));
    cy.then(() => cy.kunena_setOption('signature', signatures === undefined ? null : signatures));
    cy.then(() => {
      const messages = ids.messages.join(', ');

      cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid IN (${messages})`);
      cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id IN (${messages})`);
    });
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-code-highlight-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${ids.user}`);
    cy.task('queryDB', `DELETE FROM #__users WHERE id = ${ids.user}`);
    cy.kunena_fixUserCounters();
  });

  // The site compiles the style sheets again from the checkout which it points at next.
  after(() => {
    stylesheets.forEach((stylesheet) => cy.task('deleteRelativePath', stylesheet));
  });

  it('shows the block with its language and loads nothing with the setting off (Aurelia)', () => {
    cy.kunena_setOption('highlightCode', '0');
    openTopic('aurelia');
    cy.get('#kunena.layout', { log: false }).should('not.have.class', 'agora');

    block('<?php echo 1; ?>').should('have.class', 'language-php').and('not.have.class', 'hljs');
    cy.get(ownScripts).should('not.exist');
    cy.get('link[href*="github-dark"]').should('not.exist');
  });

  it('colours the blocks from the site with the setting on (Aurelia)', () => {
    cy.kunena_setOption('highlightCode', '1');
    openTopic('aurelia');
    cy.get('#kunena.layout', { log: false }).should('not.have.class', 'agora');

    cy.location('host').then((host) => {
      cy.get('link[href*="/media/kunena/core/highlightjs/styles/github-dark.min.css"]').should('have.length', 1)
        .then(($link) => expect(new URL($link[0].href).host).to.equal(host));
      cy.get('script[src*="/media/kunena/core/highlightjs/highlight.min.js"]').should('have.length', 1)
        .then(($script) => expect(new URL($script[0].src).host).to.equal(host));
    });

    block('<?php echo 1; ?>').should('have.class', 'hljs').find('span[class^="hljs-"]').should('exist');

    // HTML in a code block is text.
    cy.get('#kcode-x').should('not.exist');
    block('<b id="kcode-x">bold</b>').should('exist');

    // The indentation is kept.
    block('indented').should(($code) => {
      expect($code[0].textContent).to.contain('    indented');
    }).parent('pre').should(($pre) => {
      expect(style($pre).whiteSpace).to.not.be.oneOf(['pre-line', 'normal']);
    });

    // The whole box shows the background of the theme.
    block('<?php echo 1; ?>').should('have.class', 'hljs').then(($code) => {
      expect(style($code.parent('pre')).backgroundColor).to.equal(style($code).backgroundColor);
    });
  });

  it('loads no script on a page without a code block', () => {
    cy.kunena_setOption('highlightCode', '1');
    cy.setCookie('kunena_template', 'aurelia');
    cy.visit(`${base}&view=category&layout=list`);
    cy.get('#kunena.layout').should('exist');
    cy.get(ownScripts).should('not.exist');
  });

  it('keeps a hostile or unknown language out of the block', () => {
    cy.kunena_setOption('highlightCode', '1');
    openTopic('aurelia');

    // The site sends the block without a language: highlight.js then adds the class of the language it detects in the browser.
    cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.topic}`).its('body')
      .should('contain', '<pre class="bbcode_code_body"><code>text</code></pre>');

    block('text').should(($code) => {
      expect($code[0].textContent).to.equal('text');
      expect($code[0].hasAttribute('onmouseover')).to.equal(false);
      expect($code[0].className).to.not.match(/onmouseover|alert|"/);
    });

    // highlight.js does not know the language: the block stays plain, and no exception is thrown.
    block('<?php echo 1; ?>').should('have.class', 'hljs');
    block('plain').should('have.class', 'language-nolanguage').and('not.have.class', 'hljs');
  });

  it('colours the blocks in Agora, in the light and in the dark scheme', () => {
    cy.kunena_setOption('highlightCode', '1');
    openTopic('agora');
    cy.get('#kunena.layout.agora').should('exist');

    block('<?php echo 1; ?>').should('have.class', 'hljs').then(($code) => {
      const code = style($code);

      expect(code.backgroundColor).to.equal('rgb(13, 17, 23)');
      expect(style($code.parent('pre')).backgroundColor).to.equal(code.backgroundColor);

      const colours = { background: code.backgroundColor, color: code.color };

      cy.get('#kunena').invoke('attr', 'data-bs-theme', 'dark');
      block('<?php echo 1; ?>').should(($dark) => {
        expect(style($dark).backgroundColor).to.equal(colours.background);
        expect(style($dark).color).to.equal(colours.color);
        expect(style($dark.parent('pre')).backgroundColor).to.equal(colours.background);
      });
    });
  });

  it('gives a code block in a signature the same box as in a message (Agora)', () => {
    const box = ($pre) => {
      const pre = style($pre);

      return { border: pre.borderTopWidth, radius: pre.borderTopLeftRadius, whiteSpace: pre.whiteSpace, background: pre.backgroundColor };
    };

    cy.kunena_setOption('signature', '1');
    cy.kunena_setOption('highlightCode', '0');
    openTopic('agora');
    cy.get('#kunena.layout.agora').should('exist');

    // Not highlighted: the box of Agora (surface, border, radius, no wrapping), outside .kmsg as inside it.
    cy.contains('.ksignature pre.bbcode_code_body > code', '$sig = 1;').parent('pre').then(($signature) => {
      block('<?php echo 1; ?>').parent('pre').then(($message) => {
        expect($signature.closest('.kmsg').length, 'the signature is outside .kmsg').to.equal(0);
        expect(box($signature)).to.deep.equal(box($message));
        expect(box($signature).border).to.equal('1px');
        expect(box($signature).whiteSpace).to.equal('pre');
      });
    });

    // Highlighted: the dark theme inside the same frame.
    cy.kunena_setOption('highlightCode', '1');
    cy.reload();
    cy.contains('.ksignature pre.bbcode_code_body > code', '$sig = 1;').should('have.class', 'hljs').parent('pre').should(($pre) => {
      expect(style($pre).backgroundColor).to.equal('rgb(13, 17, 23)');
      expect(style($pre).borderTopWidth).to.equal('1px');
    });
  });
});
