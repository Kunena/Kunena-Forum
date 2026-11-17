// The lightbox of Kunena (media/kunena/core/js/lightbox.js) in place of Fancybox: a click on an image of a message opens it over the
// page with the other images of the page to go through (the buttons, the arrow keys, Home and End), Escape closes it and the focus
// goes back to the link. "Use existing one" leaves the images to the lightbox of the site (the attributes of Fancybox, no script of
// Kunena), "No" opens the image itself. The words of [img alt=...] are the caption, as text only. The option is put back afterwards;
// the rows are inserted by the spec, nothing is posted.
describe('Kunena lightbox: the images of a message', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};
  const old = {};
  const image = (name) => `${Cypress.config('baseUrl').replace(/\/$/, '')}/media/kunena/ranks/${name}`;
  const text = `First [img]${image('rank1.gif')}[/img] second [img alt="><b>alt-probe-${stamp}</b>]${image('rank2.gif')}[/img] third [img]${image('rank3.gif')}[/img]`;
  const sql = (value) => value.replace(/\\/g, '\\\\').replace(/'/g, "''");

  const dialog = () => cy.get('dialog.kunena-lightbox');
  const counter = () => cy.get('dialog.kunena-lightbox .kunena-lightbox__counter');
  const shown = () => cy.get('dialog.kunena-lightbox .kunena-lightbox__image');
  const links = () => cy.get(`#kunena a[href^="${image('rank')}"]`);
  const openTopic = () => cy.visit(`${base}&view=topic&catid=3&id=${ids.topic}`);

  before(() => {
    cy.kunena_setOption('lightbox', 1).then((value) => {
      old.lightbox = value;
    });
  });

  after(() => {
    cy.then(() => cy.kunena_setOption('lightbox', old.lightbox ?? null));
  });

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress lightbox ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', 'Cypress lightbox ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${sql(text)}')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
    });
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.then(() => cy.kunena_setOption('lightbox', 1));
  });

  it('opens an image over the page and goes through the images of the page', () => {
    openTopic();
    links().should('have.length', 3).each(($link) => {
      expect($link.attr('data-kunena-lightbox'), 'the group of the image').to.eq('gallery');
    });

    links().eq(1).click();
    dialog().should('have.attr', 'open');
    counter().should('have.text', 'Image 2 of 3');
    shown().should('have.attr', 'src', image('rank2.gif')).and(($image) => {
      expect($image[0].naturalWidth, 'the image is loaded').to.be.greaterThan(0);
    });
    cy.focused().should('have.class', 'kunena-lightbox__close');
    // An image of the site is downloaded by the browser (an image of another site would open in a new tab).
    cy.get('dialog.kunena-lightbox .kunena-lightbox__download')
      .should('have.attr', 'href', image('rank2.gif'))
      .and('have.attr', 'download')
      .and('have.attr', 'aria-label', 'Download');
    cy.get('dialog.kunena-lightbox .kunena-lightbox__download').should('not.have.attr', 'target');

    dialog().trigger('keydown', { key: 'ArrowRight' });
    counter().should('have.text', 'Image 3 of 3');
    cy.get('dialog.kunena-lightbox .kunena-lightbox__next').should('be.disabled');
    dialog().trigger('keydown', { key: 'Home' });
    counter().should('have.text', 'Image 1 of 3');
    cy.get('dialog.kunena-lightbox .kunena-lightbox__previous').should('be.disabled');
    cy.get('dialog.kunena-lightbox .kunena-lightbox__next').click();
    shown().should('have.attr', 'src', image('rank2.gif'));

    // Escape closes the dialog of the browser; the focus goes back to the link which opened it.
    cy.get('dialog.kunena-lightbox').then(($dialog) => $dialog[0].close());
    dialog().should('not.have.attr', 'open');
    cy.focused().should('have.attr', 'href', image('rank2.gif'));
    cy.document().its('documentElement').should('not.have.class', 'kunena-lightbox-open');
  });

  it('shows the words of [img alt=...] as text only', () => {
    openTopic();
    cy.get('#kunena b').filter(`:contains("alt-probe-${stamp}")`).should('have.length', 0);
    links().eq(1).should('have.attr', 'data-bs-original-title', `"><b>alt-probe-${stamp}</b>`);
    links().eq(0).should('not.have.attr', 'title').and('not.have.attr', 'data-bs-original-title');

    links().eq(1).click();
    cy.get('dialog.kunena-lightbox figcaption').should('have.text', `"><b>alt-probe-${stamp}</b>`).find('b').should('have.length', 0);
  });

  it('leaves the images to the lightbox of the site with "Use existing one"', () => {
    cy.kunena_setOption('lightbox', 2);
    openTopic();
    links().should('have.length', 3).each(($link) => {
      expect($link.attr('data-fancybox'), 'the attribute of Fancybox').to.eq('gallery');
      expect($link.attr('data-type')).to.eq('image');
      expect($link.attr('data-kunena-lightbox')).to.eq(undefined);
    });
    cy.get('script[src*="core/js/lightbox"]').should('not.exist');
  });

  it('opens the image itself without a lightbox', () => {
    cy.kunena_setOption('lightbox', 0);
    openTopic();
    links().should('have.length', 3).each(($link) => {
      expect($link.attr('data-kunena-lightbox')).to.eq(undefined);
      expect($link.attr('data-fancybox')).to.eq(undefined);
    });
    cy.get('script[src*="core/js/lightbox"]').should('not.exist');
  });
});
