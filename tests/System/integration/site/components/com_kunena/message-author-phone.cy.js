// Issue #9320: on a phone the topic view of Aurelia named the author of each message twice ("by Author" under the date and
// "Replied by Author on topic ...") and showed no avatar. With the avatar on the left or right the header of a message shows,
// on a phone, the author once with a small avatar next to the date; with the avatar on top or bottom the horizontal profile
// already does, and only the "by Author" line is gone. From the sm breakpoint up nothing changes. The spec sets avatarPosition
// in the file of the options of Aurelia and puts the file back afterwards.
describe('Kunena Aurelia: the author of a message on a phone', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const admin = 797;
  const user = 798;
  const params = `${Cypress.expose('cmsPath')}/components/com_kunena/template/aurelia/config/params.ini`;
  const phone = [375, 800];
  const desktop = [1200, 900];
  let original;
  let ids;

  // The ini file gets the position of the avatar; the cache of the template is removed.
  const setPosition = (value) => {
    const line = `avatarPosition="${value}"`;
    const text = /^avatarPosition=.*$/m.test(original) ? original.replace(/^avatarPosition=.*$/m, line) : `${original.trimEnd()}\n${line}\n`;

    return cy.writeFile(params, text).then(() => cy.task('deleteRelativePath', 'cache/com_kunena_template'));
  };

  const addMessage = (parent, author, name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, 3, '${name}', ${author}, '', 'Cypress author on a phone ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A text.')`).then(() => message.insertId));

  const visit = (size) => {
    cy.viewport(...size);
    cy.visit(`${base}&view=topic&catid=3&id=${ids.topic}`);
    cy.get('#kunena.layout', { log: false }).should('not.have.class', 'agora');
    cy.checkForPhpNoticesOrWarnings();
  };

  // Each message of the topic, with the name of its author as the link in the header sentence gives it (in the page also when
  // the sentence is hidden).
  const eachMessage = (check) => {
    cy.get('.message').should('have.length', 2).each(($message) => {
      const name = $message.find('.mykmsg-header a:not(.topictitle)').first().text().trim();

      expect(name, 'the name of the author').to.not.equal('');
      check($message, name);
    });
  };

  // The visible links with the name of the author, outside the text and the signature of the message and the forms.
  const visibleNames = ($message, name) => $message.find('a:visible').filter((i, el) => el.textContent.trim() === name
    && !el.closest('.kmsg, .ksig, form, .modal'));

  const dateAndNumber = ($message) => {
    const $small = $message.find('small.float-end');
    const $number = $small.find('a[rel="canonical"]');

    expect($small.find('.kdate').filter(':visible').length, 'the date').to.be.greaterThan(0);
    expect($number.filter(':visible'), 'the #n link').to.have.length(1);
    expect($number.attr('href'), 'the target of the #n link').to.equal(`#${$number.attr('id')}`);
  };

  before(() => {
    cy.readFile(params).then((text) => {
      original = text;
    });
  });

  after(() => {
    cy.then(() => {
      if (original !== undefined) {
        cy.writeFile(params, original);
      }
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress author on a phone ${stamp}', 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, UNIX_TIMESTAMP(), ${user}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids = { topic: topic.insertId };
      addMessage(0, admin, 'ci-admin').then((first) => {
        ids.first = first;
        addMessage(first, user, 'ci-user').then((reply) => {
          ids.reply = reply;
          cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${first}, last_post_id = ${reply} WHERE id = ${ids.topic}`);
        });
      });
    });

    // The test site may use another template (Agora): the cookie shows this visitor the forum in Aurelia.
    cy.setCookie('kunena_template', 'aurelia');
  });

  afterEach(() => {
    cy.clearCookie('kunena_template');

    if (ids) {
      cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid IN (${ids.first ?? 0}, ${ids.reply ?? 0})`);
      cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
      cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
      ids = null;
    }
  });

  ['left', 'right'].forEach((position) => {
    it(`shows the author once with a small avatar on a phone, avatar on the ${position}`, () => {
      setPosition(position);
      visit(phone);

      eachMessage(($message, name) => {
        const $line = $message.find('.kmsg-author-phone');

        expect($line, 'the line of the author on a phone').to.have.length(1);
        expect($line.is(':visible'), 'the line of the author is visible').to.equal(true);
        expect($line.find('a img, a svg').filter(':visible').length, 'the avatar in the line of the author').to.be.greaterThan(0);
        expect($line.find('a').filter((i, el) => el.textContent.trim() === name).filter(':visible'), 'the name in the line of the author').to.have.length(1);

        expect(visibleNames($message, name), `the visible links with the name ${name}`).to.have.length(1);
        expect($message.find('.mykmsg-header a.topictitle').filter(':visible'), 'the sentence with the topic').to.have.length(0);
        expect($message.find('small.float-end')[0].innerText, 'the text "by" before the name').to.not.match(new RegExp(`\\bby\\s+${Cypress._.escapeRegExp(name)}`, 'i'));
        expect($message.find('.mykmsg-header').filter(':visible'), 'the header of a message without a private message').to.have.length(0);
        expect($message.find('.col-md-2').filter(':visible'), 'the side profile').to.have.length(0);
        // The text of the message starts below the floated date and author line, not beside it.
        expect($message.find('.kmsg').last()[0].getBoundingClientRect().top, 'the top of the text of the message')
          .to.be.at.least($message.find('small.float-end')[0].getBoundingClientRect().bottom);
        dateAndNumber($message);
      });
    });
  });

  ['top', 'bottom'].forEach((position) => {
    it(`shows the horizontal profile and no "by" line on a phone, avatar on ${position}`, () => {
      setPosition(position);
      visit(phone);

      eachMessage(($message, name) => {
        const $profile = $message.find(`.profile-horizontal-${position}`);

        expect($profile.is(':visible'), 'the horizontal profile').to.equal(true);
        expect($profile.find('a').filter((i, el) => el.textContent.trim() === name).filter(':visible').length, 'the name in the horizontal profile').to.be.greaterThan(0);
        expect($message.find('.kmsg-author-phone'), 'the line of the author on a phone').to.have.length(0);
        expect($message.find('small.float-end')[0].innerText, 'the name next to the date').to.not.include(name);
        dateAndNumber($message);
      });
    });
  });

  ['left', 'right', 'top', 'bottom'].forEach((position) => {
    it(`shows the header as before on a desktop, avatar on ${position}`, () => {
      setPosition(position);
      visit(desktop);

      eachMessage(($message, name) => {
        expect($message.find('.kmsg-author-phone').filter(':visible'), 'the line of the author on a phone').to.have.length(0);
        expect($message.find('.mykmsg-header').filter(':visible'), 'the header of the message').to.have.length(1);
        expect($message.find('.mykmsg-header a.topictitle').filter(':visible'), 'the sentence with the topic').to.have.length(1);
        expect($message.find('.mykmsg-header a:not(.topictitle)').filter(':visible').first().text().trim(), 'the author in the sentence').to.equal(name);

        if (position === 'left' || position === 'right') {
          expect($message.find('.col-md-2').filter(':visible'), 'the side profile').to.have.length(1);
        }

        dateAndNumber($message);
      });
    });
  });
});
