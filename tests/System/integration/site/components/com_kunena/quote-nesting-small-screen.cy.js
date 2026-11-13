// Aurelia indents every quote by 40px: on a phone, five quotes in each other left the deepest one a few characters wide, one
// word per line. On narrow screens (up to 575.98px) a quote gets a small indent and a left border, and a long word wraps in it.
describe('Kunena BBCode: nested quotes on a small screen', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const plainMarker = `deepplainquote${stamp}`;
  const linkMarker = `deeplinkquote${stamp}`;
  const longWord = `longword${stamp}${'a'.repeat(300)}`.slice(0, 300);
  // The compiled style sheet of Aurelia, which the site makes again from the SCSS of the checkout when it is missing. Only this
  // one file is deleted, never its folder: the folders of Kunena on the site are links into a checkout.
  const stylesheet = 'media/kunena/cache/aurelia/css/kunena.css';
  let ids;

  const nest = (open, inner) => [1, 2, 3, 4].reduceRight((text, level) => `${open}Level ${level} ${text} after ${level}[/quote]`, `${open}${inner}[/quote]`);

  // The quote which holds the marker: the deepest of the five, the one without a quote inside it which holds the marker too.
  const deepest = (marker) => cy.get('.kmsg blockquote').then(($all) => $all.filter((i, el) => el.textContent.includes(marker)
    && !Array.from(el.querySelectorAll('blockquote')).some((inner) => inner.textContent.includes(marker)))).should('have.length', 1);
  const style = (el) => el.ownerDocument.defaultView.getComputedStyle(el);

  beforeEach(() => {
    const text = `${nest('[quote]', `${plainMarker} ${longWord}`)} ${nest('[quote="ci-user post=11 userid=798"]', linkMarker)}`;

    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress nested quotes ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    ).then((topic) => cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', 'Cypress nested quotes ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      ids = { topic: topic.insertId, message: message.insertId };
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`);
    }));

    cy.task('deleteRelativePath', stylesheet);
  });

  afterEach(() => {
    if (ids) {
      cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
      cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
      cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
      ids = null;
    }
  });

  // The site compiles the style sheet again from the checkout which it points at next.
  after(() => {
    cy.task('deleteRelativePath', stylesheet);
  });

  it('keeps the deepest quotes readable on a phone', () => {
    cy.viewport(320, 800);
    cy.visit(`${base}&view=topic&catid=3&id=${ids.topic}`);

    deepest(plainMarker).then(($quote) => {
      expect($quote.parents('blockquote'), 'the quotes around the deepest plain quote').to.have.length(4);
      expect($quote[0].getBoundingClientRect().width, 'the width of the deepest plain quote').to.be.at.least(128);
      expect(parseFloat(style($quote[0]).borderLeftWidth), 'the left border of the deepest plain quote').to.be.above(0);
    });

    deepest(linkMarker).then(($quote) => {
      expect($quote.parents('blockquote'), 'the quotes around the deepest quote with a link').to.have.length(4);
      expect($quote.find('a[href*="which-template-do-you-use"]'), 'the link to the quoted message').to.have.length(1);
      expect($quote[0].getBoundingClientRect().width, 'the width of the deepest quote with a link').to.be.at.least(128);
    });

    cy.window().then((win) => {
      expect(win.document.documentElement.scrollWidth, 'the width of the page').to.be.at.most(win.innerWidth);
    });
  });

  it('keeps the indent of the quotes on a wide screen', () => {
    cy.viewport(1200, 800);
    cy.visit(`${base}&view=topic&catid=3&id=${ids.topic}`);

    deepest(plainMarker).then(($quote) => {
      const outer = $quote.parents('blockquote').last()[0];

      expect(style(outer).marginLeft, 'the indent of the outer plain quote').to.equal('40px');
    });
  });
});
