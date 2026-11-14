// The links of the page work while a quick reply is open (issue #8820): the quick reply is a modal of Bootstrap without a
// backdrop, whose focus trap pulled the focus (and the page) back to the form and whose layer over the window took the clicks.
// For the settings "Default" and "Modal" of the template Aurelia, a breadcrumb link outside the open quick reply is clicked and
// the browser lands on its page. The spec switches the setting in the file of the options and puts the file back.
describe('Kunena quick reply: the links outside an open quick reply', () => {
  const base = '/index.php?option=com_kunena';
  const prefix = `Cypress quick reply links ${Date.now()}`;
  const user = { name: 'ci-user', password: 'ci-user-17082005-x' };
  const admin = 797;
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/aurelia/config/params.ini`;
  const ids = {};
  const old = {};

  // The setting of the quick reply of the template Aurelia: the ini file gets the value, the cache of the template is removed.
  const useQuick = (quick) => {
    const line = `quick="${quick}"`;
    const text = /^quick=.*$/m.test(old.params) ? old.params.replace(/^quick=.*$/m, line) : `${old.params.trimEnd()}\n${line}\n`;

    return cy.writeFile(params, text).then(() => cy.task('deleteRelativePath', 'cache/com_kunena_template'));
  };

  // A breadcrumb link to another page, beside the dialog of the quick reply (a link under the dialog is rightly covered by it).
  const outsideLink = () => cy.get(`#kreply${ids.message}_form .modal-dialog`).then(($dialog) => {
    const dialog = $dialog[0].getBoundingClientRect();

    return cy.get('.mod-kunena-breadcrumbs a[itemprop="item"]').then(($links) => {
      const link = [...$links].find((a) => {
        const rect = a.getBoundingClientRect();

        return a.href !== a.ownerDocument.location.href && (rect.right < dialog.left || rect.left > dialog.right);
      });

      expect(link, 'a breadcrumb link beside the quick reply').to.exist;

      return cy.wrap(link);
    });
  });

  // Opens the quick reply of the message, clicks the link outside it and checks that the browser is on the page of the link.
  const clickOutside = (quick) => {
    useQuick(quick);
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&catid=3&id=${ids.topic}`);
    cy.get(`[data-bs-target="#kreply${ids.message}_form"]`).filter(':visible').first().click();
    cy.get(`#kreply${ids.message}_form`).should('be.visible').and('have.class', 'show');
    cy.get(`#postform-${ids.message}`).should('be.visible');
    cy.window().then((win) => {
      win.quickReplyLinksMarker = true;
    });

    outsideLink().then(($link) => {
      const target = new URL($link[0].href);

      if (quick === 1) {
        // Nothing of the modal layer lies over the link.
        cy.wrap($link).scrollIntoView();
        cy.wrap($link).then(($a) => {
          const rect = $a[0].getBoundingClientRect();
          const hit = $a[0].ownerDocument.elementFromPoint(rect.left + rect.width / 2, rect.top + rect.height / 2);

          expect($a[0].contains(hit), 'the element at the middle of the link is the link').to.eq(true);
        });
      }

      cy.wrap($link).click();
      cy.window().should('not.have.property', 'quickReplyLinksMarker');
      cy.location().should((location) => {
        expect(location.pathname + location.search, 'the page of the link').to.eq(target.pathname + target.search);
      });
    });
  };

  before(() => {
    cy.kunena_setOption('template', 'aurelia').then((value) => {
      old.template = value;
    });
    cy.kunena_setOption('quickReply', '1').then((value) => {
      old.quickReply = value;
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
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE '${prefix}%'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
  });

  beforeEach(() => {
    // A topic of the administrator with one message, which the user may answer with a quick reply.
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
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('follows a link outside the quick reply with the setting "Default"', () => {
    clickOutside(0);
  });

  it('follows a link outside the quick reply with the setting "Modal"', () => {
    clickOutside(1);
  });
});
