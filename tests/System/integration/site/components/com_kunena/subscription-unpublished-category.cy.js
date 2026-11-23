// An unpublished category is hidden from the members (its moderators still see it and may still post in it). The subscribers of its
// topics were found by the access of the category alone, which does not look at the state: a reply of a moderator gave every
// subscriber a notification in his inbox, with the subject of the topic, for a category which he can not open.
describe('Kunena subscriptions: a topic of an unpublished category', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const admin = { name: Cypress.expose('username'), password: Cypress.expose('password'), id: 797 };
  const subscriber = 798;
  const subject = `Cypress hidden subscription ${stamp}`;
  const ids = {};
  const old = {};

  beforeEach(() => {
    cy.kunena_setOption('topicSubscriptions', 'every').then((value) => {
      old.topicSubscriptions = value ?? null;
    });
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.floodProtection = value ?? null;
    });
    // An unpublished category in the published section, open to the registered users.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress hidden sub ${stamp}', 'cypress-hidden-sub-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 97, 0, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-hidden-sub-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - 3600, ${admin.id}, 'x', '', 0, UNIX_TIMESTAMP() - 3600, ${admin.id}, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, 'ci-admin', ${admin.id}, '', '${subject}', UNIX_TIMESTAMP() - 3600, '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.first = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'The first post ${stamp}.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      // The member subscribed to the topic when the category was still published.
      cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${subscriber}, ${ids.topic}, ${ids.category}, 0, 0, 0, 0, 1, '')`);
    });
  });

  afterEach(() => {
    cy.kunena_setOption('topicSubscriptions', old.topicSubscriptions);
    cy.then(() => cy.kunena_setOption('floodProtection', old.floodProtection));
    cy.then(() => cy.task('queryDB', `DELETE FROM #__kunena_inbox WHERE topic_id = ${ids.topic}`));
    cy.task('queryDB', `DELETE FROM #__kunena_notifications_mailsqueue WHERE messageId IN (SELECT id FROM #__kunena_messages WHERE thread = ${ids.topic})`);
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-hidden-sub-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.kunena_fixUserCounters();
  });

  it('gives the subscriber no notification of a reply', () => {
    cy.doFrontendLogin(admin.name, admin.password, false);
    cy.request(`${base}&view=topic&layout=reply&catid=${ids.category}&id=${ids.topic}`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: { catid: ids.category, id: ids.topic, parentid: ids.first, subject: `Re: ${subject}`, message: `A reply of the moderator ${stamp}.`, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_messages WHERE thread = ${ids.topic}`).then((rows) => {
      expect(Number(rows[0].n), 'the reply is posted').to.eq(2);
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_inbox WHERE topic_id = ${ids.topic} AND user_id = ${subscriber}`).then((rows) => {
      expect(Number(rows[0].n), 'the notifications of the subscriber').to.eq(0);
    });
    cy.task('queryDB', `SELECT emailListJson FROM #__kunena_notifications_mailsqueue WHERE messageId IN (SELECT id FROM #__kunena_messages WHERE thread = ${ids.topic})`).then((rows) => {
      expect(rows.map((row) => row.emailListJson).join(' '), 'the e-mails in the queue').to.not.include(`"id":${subscriber},`);
    });
  });
});
