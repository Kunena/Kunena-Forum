// What a guest and a user may not read: a topic in a category for the access level Special, a topic which waits for approval, and a
// deleted and an unapproved message in a public topic. A secret text is in each of them; it must not be in the answer of any page,
// search, feed or list. The administrator reads it (so the data of the test is right).
describe('Kunena permissions: what may not be read', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const secret = { restricted: `SECRETRESTRICTED${stamp}`, waiting: `SECRETWAITING${stamp}`, deleted: `SECRETDELETED${stamp}`, unapproved: `SECRETUNAPPROVED${stamp}` };
  const ids = {};

  const addMessage = (parent, thread, category, subject, text, hold) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${thread}, ${category}, 'ci-admin', 797, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, ${hold}, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`).then(() => message.insertId));

  const addTopic = (category, subject, text, hold) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category}, '${subject}', 0, 0, 0, ${hold}, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, '${text}', 'ci-admin', 0, UNIX_TIMESTAMP(), 797, '${text}', 'ci-admin', 0, '')`,
  ).then((topic) => addMessage(0, topic.insertId, category, subject, text, hold).then((message) => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message}, last_post_id = ${message} WHERE id = ${topic.insertId}`,
  ).then(() => ({ topic: topic.insertId, message }))));

  // The queryDB task removes what is inserted (with an id) after each test: the data is made again for every test.
  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params) VALUES (1, 'Cypress restricted ${stamp}', 'cypress-restricted-${stamp}', '', '', '', '', '', 'joomla.level', 3, 3, 1, 8, 1, 90, 1, '{}')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-restricted-${stamp}', 'catid', ${ids.category}, 0)`);

      return addTopic(ids.category, `Cypress restricted ${stamp}`, secret.restricted, 0);
    }).then((restricted) => {
      ids.restricted = restricted;

      return addTopic(3, `Cypress waiting ${stamp}`, secret.waiting, 1);
    }).then((waiting) => {
      ids.waiting = waiting;

      return addTopic(3, `Cypress public ${stamp}`, 'A public topic of the test.', 0);
    }).then((visible) => {
      ids.visible = visible;

      return addMessage(visible.message, visible.topic, 3, 'Re: reply', secret.deleted, 2);
    }).then((deleted) => {
      ids.deleted = deleted;

      return addMessage(ids.visible.message, ids.visible.topic, 3, 'Re: reply', secret.unapproved, 1);
    }).then((unapproved) => {
      ids.unapproved = unapproved;
      cy.task('queryDB', `UPDATE #__kunena_topics SET posts = 3 WHERE id = ${ids.visible.topic}`);
    });
  });

  // What the task does not remove: the alias of the category, and the texts of the messages which it removed.
  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias LIKE 'cypress-restricted-%'`);
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
    cy.task('queryDB', 'DELETE x FROM #__kunena_user_topics AS x LEFT JOIN #__kunena_topics AS t ON t.id = x.topic_id WHERE t.id IS NULL');
  });

  const pages = () => [
    `${base}&view=topic&catid=${ids.category}&id=${ids.restricted.topic}`,
    `${base}&view=topic&catid=${ids.category}&id=${ids.restricted.topic}&mesid=${ids.restricted.message}`,
    `${base}&view=topic&id=${ids.restricted.topic}`,
    `${base}&view=topic&catid=3&id=${ids.restricted.topic}`,
    `${base}&view=topic&catid=3&id=${ids.waiting.topic}`,
    `${base}&view=topic&id=${ids.waiting.topic}`,
    `${base}&view=topic&catid=3&id=${ids.visible.topic}`,
    `${base}&view=topic&catid=3&id=${ids.visible.topic}&mesid=${ids.deleted}`,
    `${base}&view=topic&catid=3&id=${ids.visible.topic}&mesid=${ids.unapproved}`,
    `${base}&view=topic&layout=reply&catid=3&id=${ids.visible.topic}&mesid=${ids.deleted}`,
    `${base}&view=topic&layout=reply&catid=3&id=${ids.visible.topic}&mesid=${ids.unapproved}`,
    `${base}&view=topic&layout=reply&catid=${ids.category}&id=${ids.restricted.topic}`,
    `${base}&view=topic&layout=edit&catid=3&id=${ids.visible.topic}&mesid=${ids.deleted}`,
    `${base}&view=topic&format=raw&catid=3&id=${ids.visible.topic}`,
    `${base}&view=topic&format=json&catid=${ids.category}&id=${ids.restricted.topic}`,
    `${base}&view=topic&format=json&catid=3&id=${ids.visible.topic}`,
    `${base}&view=category&catid=${ids.category}`,
    `${base}&view=category&layout=list`,
    `${base}&view=category&catid=3`,
    `${base}&view=category&catid=3&format=feed&layout=feed&type=rss`,
    `${base}&view=category&catid=${ids.category}&format=feed&layout=feed&type=rss`,
    `${base}&view=topics`,
    `${base}&view=topics&mode=replies`,
    `${base}&view=topics&mode=noreplies`,
    `${base}&view=topics&format=feed&layout=feed&type=rss`,
    `${base}&view=topics&format=json`,
    `${base}&view=topics&layout=user&mode=topics&userid=797`,
    `${base}&view=topics&layout=user&mode=posts&userid=797`,
    `${base}&view=topics&layout=posts&mode=recent`,
    `${base}&view=topics&layout=unread`,
    `${base}&view=topics&layout=default&mode=sticky`,
    `${base}&view=topics&layout=default&mode=locked`,
    `${base}&view=topics&layout=user&mode=default`,
    `${base}&view=topics&layout=user&mode=started`,
    `${base}&view=search&task=results&query=SECRET${stamp}`,
    `${base}&view=search&task=results&query=SECRET${stamp}&titleonly=0&searchuser=&starteronly=0&exactname=0`,
    `${base}&view=search&task=results&query=Cypress+${stamp}&titleonly=1`,
    `${base}&view=search&task=results&query=Cypress&catids=${ids.category}`,
    `${base}&view=statistics`,
    `${base}&view=home`,
    `${base}&view=user&layout=default&userid=797`,
    `${base}&view=user&layout=list`,
  ];

  const secretsIn = (body) => Object.entries(secret).filter(([, value]) => String(body).includes(value)).map(([name]) => name);

  const sweep = (who) => {
    const leaks = [];

    pages().forEach((url) => {
      cy.request({ url, failOnStatusCode: false }).then((response) => {
        const found = secretsIn(typeof response.body === 'string' ? response.body : JSON.stringify(response.body));

        if (found.length) {
          leaks.push(`${url.replace(base, '')} shows ${found.join(', ')}`);
        }
      });
    });
    cy.then(() => expect(leaks, `${who}: the pages which show what may not be read`).to.deep.eq([]));
  };

  it('shows nothing of it to a guest', () => {
    sweep('guest');
  });

  it('shows nothing of it to a user', () => {
    // A new login: the categories which a user may read are kept in the session, and the category of this test is new.
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    sweep('user');
  });

  it('shows it to the administrator (the data of the test is right)', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.restricted.topic}`).its('body').should('include', secret.restricted);
    cy.request(`${base}&view=topic&catid=3&id=${ids.waiting.topic}`).its('body').should('include', secret.waiting);
    cy.request(`${base}&view=topic&catid=3&id=${ids.visible.topic}`).its('body').should('include', secret.deleted);
  });
});
