// The life of a topic through the forms and the buttons of the forum: a topic with a tag, a reply, an edit, a thank you, a favorite and a
// subscription, the status solved, and what a moderator does (sticky, lock, delete, undelete, move). The answer is checked in the
// database, and the afterEach of the support file fails a test for a PHP notice or warning on a page.
describe('Kunena writing: the life of a topic', () => {
  const base = '/index.php?option=com_kunena';
  const admin = { id: 797 };
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const subject = `Cypress life ${Date.now()}`;
  let topicId;
  let firstMessage;
  const old = {};

  const typeInEditor = (text) => {
    cy.window().should('have.property', 'sceditor');
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty')
      .then(($body) => cy.wrap($body).click().type(text, { parseSpecialCharSequences: false }));
  };
  const topicRow = () => cy.task('queryDB', `SELECT * FROM #__kunena_topics WHERE id = ${topicId}`).then((rows) => rows[0]);
  const openTopic = () => cy.visit(`${base}&view=topic&catid=3&id=${topicId}`);
  // A button or a link of the menus of the topic: it is opened, then clicked.
  // The last button or link of the actions of the messages with this text (the reply is the last message of the topic).
  const clickLast = (pattern) => cy.get('.topic-item-messages a, .topic-item-messages button').then(($items) => {
    const found = [...$items].filter((item) => pattern.test(item.textContent));

    expect(found.length, 'an entry of the menu').to.be.greaterThan(0);
    cy.wrap(found[found.length - 1]).click({ force: true });
  });
  const choose = (label) => cy.contains('.dropdown-menu a, .dropdown-menu button, a.btn, button.btn', label).first().click({ force: true });

  before(() => {
    ['enableTags'].forEach((name) => cy.kunena_setOption(name, '1').then((value) => {
      old[name] = value;
    }));
  });

  after(() => {
    cy.kunena_setOption('enableTags', old.enableTags ?? '0');
    // The topic of the test, with everything which belongs to it.
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE 'Cypress life %'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__contentitem_tag_map WHERE type_alias = 'com_kunena.topic' AND content_item_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', 'DELETE t FROM #__kunena_thankyou AS t LEFT JOIN #__kunena_messages AS m ON m.id = t.postid WHERE m.id IS NULL');
  });

  describe('as the administrator', () => {
    beforeEach(() => {
      cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
    });

    it('writes a topic with a tag', () => {
      cy.visit(`${base}&view=topic&layout=create&catid=3`);

      cy.get('#postform input[name="subject"]').type(subject);
      cy.get('#topic-tags').select(['Music'], { force: true });
      typeInEditor('The first message of the topic.');
      cy.get('#form_submit_button').should('not.be.disabled').click();

      cy.task('queryDB', `SELECT id, first_post_id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
        expect(rows, 'the new topic').to.have.length(1);
        topicId = rows[0].id;
        firstMessage = rows[0].first_post_id;
      });
      cy.then(() => cy.task('queryDB', `SELECT tag_id FROM #__contentitem_tag_map WHERE type_alias = 'com_kunena.topic' AND content_item_id = ${topicId}`)).then((tags) => {
        expect(tags, 'the topic has its tag').to.have.length(1);
      });
    });

    it('changes the first message with the form to edit', () => {
      cy.visit(`${base}&view=topic&layout=edit&catid=3&id=${topicId}&mesid=${firstMessage}`);

      cy.get('#postform input[name="subject"]').should('have.value', subject);
      typeInEditor(' It is changed.');
      cy.get('#form_submit_button').should('not.be.disabled').click();

      cy.task('queryDB', `SELECT message FROM #__kunena_messages_text WHERE mesid = ${firstMessage}`).then((rows) => {
        expect(rows[0].message).to.include('It is changed.');
      });
    });

    it('makes the topic sticky and locks it', () => {
      openTopic();
      cy.get('#topic-actions-toolbar .btn-group').eq(1).find('a.dropdown-toggle').click();
      cy.contains('#topic-actions-toolbar .dropdown-menu.show a', /^\s*Sticky/i).click();
      topicRow().then((row) => expect(Number(row.ordering), 'sticky').to.be.greaterThan(0));

      openTopic();
      cy.get('#topic-actions-toolbar .btn-group').eq(1).find('a.dropdown-toggle').click();
      cy.contains('#topic-actions-toolbar .dropdown-menu.show a', /^\s*Lock/i).click();
      topicRow().then((row) => expect(Number(row.locked), 'locked').to.eq(1));
    });

    it('unlocks the topic and takes the sticky away', () => {
      openTopic();
      cy.get('#topic-actions-toolbar .btn-group').eq(1).find('a.dropdown-toggle').click();
      cy.contains('#topic-actions-toolbar .dropdown-menu.show a', /Unlock/i).click();
      topicRow().then((row) => expect(Number(row.locked), 'unlocked').to.eq(0));

      openTopic();
      cy.get('#topic-actions-toolbar .btn-group').eq(1).find('a.dropdown-toggle').click();
      cy.contains('#topic-actions-toolbar .dropdown-menu.show a', /Unsticky|Remove sticky|Un-?sticky/i).click();
      topicRow().then((row) => expect(Number(row.ordering), 'not sticky').to.eq(0));
    });
  });

  describe('as a user', () => {
    beforeEach(() => {
      cy.doFrontendLogin(user.name, user.password);
    });

    it('writes a reply with the form', () => {
      cy.visit(`${base}&view=topic&layout=reply&catid=3&id=${topicId}`);

      typeInEditor('A reply of the user.');
      cy.get('#form_submit_button').should('not.be.disabled').click();

      cy.task('queryDB', `SELECT id FROM #__kunena_messages WHERE thread = ${topicId} AND userid = ${user.id}`).then((rows) => {
        expect(rows, 'the reply').to.have.length(1);
      });
    });

    it('says thank you for the first message', () => {
      openTopic();
      cy.contains('.topic-item-messages a, .topic-item-messages button', /Thank You/i).first().click();

      cy.task('queryDB', `SELECT userid FROM #__kunena_thankyou WHERE postid = ${firstMessage}`).then((rows) => {
        expect(rows.map((row) => Number(row.userid))).to.include(user.id);
      });
    });

    it('makes the topic a favorite, and stops and starts the subscription', () => {
      openTopic();
      cy.get('#topic-actions-toolbar .btn-group').first().find('a.dropdown-toggle').click();
      cy.contains('#topic-actions-toolbar .dropdown-menu.show a', /Favorite/i).click();
      cy.task('queryDB', `SELECT favorite FROM #__kunena_user_topics WHERE topic_id = ${topicId} AND user_id = ${user.id}`).then((rows) => {
        expect(rows.length, 'a row of the user for the topic').to.eq(1);
        expect(Number(rows[0].favorite), 'favorite').to.eq(1);
      });

      // The reply was written with the box "subscribe" on (its start value): the user is subscribed now.
      cy.task('queryDB', `SELECT subscribed FROM #__kunena_user_topics WHERE topic_id = ${topicId} AND user_id = ${user.id}`).then((rows) => {
        expect(Number(rows[0].subscribed), 'subscribed by the reply').to.eq(1);
      });

      openTopic();
      cy.get('#topic-actions-toolbar .btn-group').first().find('a.dropdown-toggle').click();
      cy.contains('#topic-actions-toolbar .dropdown-menu.show a', /Unsubscribe/i).click();
      cy.task('queryDB', `SELECT subscribed FROM #__kunena_user_topics WHERE topic_id = ${topicId} AND user_id = ${user.id}`).then((rows) => {
        expect(Number(rows[0].subscribed), 'not subscribed').to.eq(0);
      });

      openTopic();
      cy.get('#topic-actions-toolbar .btn-group').first().find('a.dropdown-toggle').click();
      cy.contains('#topic-actions-toolbar .dropdown-menu.show a', /^\s*Subscribe/i).click();
      cy.task('queryDB', `SELECT subscribed FROM #__kunena_user_topics WHERE topic_id = ${topicId} AND user_id = ${user.id}`).then((rows) => {
        expect(Number(rows[0].subscribed), 'subscribed again').to.eq(1);
      });
    });
  });

  describe('as the administrator again: delete, undelete and move', () => {
    beforeEach(() => {
      cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
    });

    it('deletes the reply of the user and brings it back', () => {
      cy.task('queryDB', `SELECT id FROM #__kunena_messages WHERE thread = ${topicId} AND userid = ${user.id}`).then((rows) => {
        const reply = rows[0].id;

        cy.on('window:confirm', () => true);
        openTopic();
        clickLast(/^\s*Delete/i);
        cy.task('queryDB', `SELECT hold FROM #__kunena_messages WHERE id = ${reply}`).then((held) => {
          expect(Number(held[0].hold), 'deleted (hold 2 or 3)').to.be.greaterThan(1);
        });

        openTopic();
        clickLast(/Restore/i);
        cy.task('queryDB', `SELECT hold FROM #__kunena_messages WHERE id = ${reply}`).then((held) => {
          expect(Number(held[0].hold), 'brought back').to.eq(0);
        });
      });
    });

    it('moves the topic to another category', () => {
      cy.visit(`${base}&view=topic&layout=moderate&catid=3&id=${topicId}`);

      cy.get('#tab2-tab').click();
      cy.get('#modcategorieslist select').select('4', { force: true });
      cy.get('button[name="submit"]').filter(':visible').click();

      topicRow().then((row) => expect(Number(row.category_id), 'moved').to.eq(4));
    });
  });
});
