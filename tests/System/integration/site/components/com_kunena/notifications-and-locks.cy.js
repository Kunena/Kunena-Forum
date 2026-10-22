// Three rules around a new message: a follower and a subscriber of a category get no notification of a topic in a category which they
// may not read; a topic without a new message for the days of the category option is closed for the replies of the users (the
// moderators can still reply); and the private part of a message is for its author and the moderators, not for a visitor.
describe('Kunena notifications, old topics and private parts', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const old = {};
  const ids = {};

  const typeInEditor = (text) => {
    cy.window().should('have.property', 'sceditor');
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty')
      .then(($body) => cy.wrap($body).click().type(text, { parseSpecialCharSequences: false }));
  };

  const makeCategory = (name, access, params, order) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress notify ${name} ${stamp}', 'cypress-notify-${name}-${stamp}', '', '', '', '', '', 'joomla.level', ${access}, ${access}, 1, 8, 1, ${order}, 1, '${params}', 'default')`,
  ).then((category) => cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-notify-${name}-${stamp}', 'catid', ${category.insertId}, 0)`).then(() => category.insertId));

  const makeTopic = (category, author, name, age) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category}, 'Cypress notify ${name} ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - ${age}, ${author}, 'x', '', 0, UNIX_TIMESTAMP() - ${age}, ${author}, 'x', '', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${category}, 'author', ${author}, '', 'Cypress notify ${name} ${stamp}', UNIX_TIMESTAMP() - ${age}, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
  )).then(() => ({ topic: topic.insertId, message: message.insertId, category }))));

  const writeTopic = (category, subject, text = 'A new topic.') => {
    cy.visit(`${base}&view=topic&layout=create&catid=${category}`);
    cy.get('#postform input[name="subject"]').type(subject);
    typeInEditor(text);
    cy.get('#form_submit_button').should('not.be.disabled').click();

    return cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      expect(rows, `the topic ${subject}`).to.have.length(1);

      return rows[0].id;
    });
  };

  const reply = (set, text, extra = {}) => cy.request(`${base}&view=topic&catid=${set.category}&id=${set.topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=post`,
      form: true,
      body: { catid: set.category, id: set.topic, parentid: set.message, subject: 'Re: reply', message: text, ...extra, [token]: '1' },
      failOnStatusCode: false,
      followRedirect: false,
    });
  });
  const repliesOf = (set) => cy.task('queryDB', `SELECT id, userid FROM #__kunena_messages WHERE thread = ${set.topic} AND parent > 0`);
  const inboxOf = (userId, topic, type) => cy.task('queryDB', `SELECT id FROM #__kunena_inbox WHERE user_id = ${userId} AND topic_id = ${topic} AND type = '${type}'`);

  before(() => {
    ['allowSubscriptions', 'enableFollow', 'privateMessage'].forEach((name) => cy.kunena_setOption(name, '1').then((value) => {
      old[name] = value;
    }));
    cy.kunena_setOption('categorySubscriptions', 'post').then((value) => {
      old.categorySubscriptions = value;
    });
  });

  after(() => {
    ['allowSubscriptions', 'enableFollow', 'privateMessage'].forEach((name) => cy.kunena_setOption(name, old[name] ?? '1'));
    cy.kunena_setOption('categorySubscriptions', old.categorySubscriptions ?? 'post');
  });

  afterEach(() => {
    // A reply of a moderator with another subject renames the topic (the subject of the reply form is the subject of the topic, so
    // that happens only here): the topics are found by their numbers too, not only by their subject.
    const known = Object.values(ids).map((set) => set?.topic).filter(Boolean);

    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE 'Cypress notify % ${stamp}'${known.length ? ` OR id IN (${known.join(',')})` : ''}`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE FROM #__kunena_inbox WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE pm FROM #__kunena_private_post_map AS pm INNER JOIN #__kunena_messages AS m ON m.id = pm.message_id WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', 'DELETE u FROM #__kunena_private_user_map AS u LEFT JOIN #__kunena_private_post_map AS p ON p.private_id = u.private_id WHERE p.private_id IS NULL');
    cy.task('queryDB', 'DELETE p FROM #__kunena_private AS p LEFT JOIN #__kunena_private_post_map AS m ON m.private_id = p.id WHERE m.private_id IS NULL');
    cy.task('queryDB', `DELETE FROM #__kunena_user_follow WHERE user_id = ${user.id} AND followed_id = ${admin}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id IN (SELECT id FROM #__kunena_categories WHERE name LIKE 'Cypress notify % ${stamp}')`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias LIKE 'cypress-notify-%-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE catid IN (SELECT id FROM #__kunena_categories WHERE name LIKE 'Cypress notify % ${stamp}')`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE name LIKE 'Cypress notify % ${stamp}'`);
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
  });

  it('tells a follower about a new topic, but not about one in a category which he may not read', () => {
    makeCategory('restricted', 3, '{}', 93).then((category) => {
      ids.restricted = category;
    });
    cy.task('queryDB', `INSERT INTO #__kunena_user_follow (user_id, followed_id, created_time) VALUES (${user.id}, ${admin}, UNIX_TIMESTAMP())`);

    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    writeTopic(3, `Cypress notify public ${stamp}`).then((topic) => {
      inboxOf(user.id, topic, 'follow').then((rows) => expect(rows, 'the notification of the topic which he may read').to.have.length(1));
    });
    cy.then(() => writeTopic(ids.restricted, `Cypress notify hidden ${stamp}`)).then((topic) => {
      inboxOf(user.id, topic, 'follow').then((rows) => expect(rows, 'a notification of a topic which he may not read').to.have.length(0));
    });
  });

  it('tells a subscriber of a category about a new topic, but not after the category was closed to him', () => {
    makeCategory('subscribed', 1, '{}', 94).then((category) => {
      ids.subscribed = category;

      return cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${user.id}, ${category}, 0, UNIX_TIMESTAMP(), 1, '')`);
    });

    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.then(() => writeTopic(ids.subscribed, `Cypress notify open ${stamp}`)).then((topic) => {
      inboxOf(user.id, topic, 'subscription').then((rows) => expect(rows, 'the notification while he may read the category').to.have.length(1));
    });

    // The category is closed to the users (the access level Special): the subscription must not bring the topics to him.
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET access = 3, pubAccess = 3 WHERE id = ${ids.subscribed}`));
    cy.then(() => writeTopic(ids.subscribed, `Cypress notify closed ${stamp}`)).then((topic) => {
      inboxOf(user.id, topic, 'subscription').then((rows) => expect(rows, 'a notification after the category was closed to him').to.have.length(0));
    });
  });

  it('closes an old topic for the replies of a user, and not for a moderator', () => {
    makeCategory('old', 1, '{"inactiveLockDays":"1"}', 95).then((category) => {
      ids.old = category;

      return makeTopic(category, admin, 'ancient', 3 * 86400);
    }).then((set) => {
      ids.ancient = set;

      return makeTopic(ids.old, admin, 'fresh', 60);
    }).then((set) => {
      ids.fresh = set;
    });

    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => reply(ids.ancient, 'A reply to an old topic.'));
    cy.then(() => repliesOf(ids.ancient)).then((rows) => expect(rows, 'the replies of a user to an old topic').to.have.length(0));
    cy.then(() => reply(ids.fresh, 'A reply to a fresh topic.'));
    cy.then(() => repliesOf(ids.fresh)).then((rows) => expect(rows, 'the replies of a user to a fresh topic').to.have.length(1));

    cy.clearAllCookies();
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.then(() => reply(ids.ancient, 'A reply of the moderator.'));
    cy.then(() => repliesOf(ids.ancient)).then((rows) => expect(rows.map((row) => Number(row.userid)), 'the replies of the moderator to an old topic').to.deep.eq([admin]));
  });

  it('keeps the private part of a message from a visitor', () => {
    const secret = `PRIVATENOTE${stamp}`;

    makeTopic(3, admin, 'private', 60).then((set) => {
      ids.private = set;
    });
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => reply(ids.private, 'A public reply.', { message_private: secret }));
    cy.then(() => repliesOf(ids.private)).then((rows) => expect(rows, 'the reply').to.have.length(1));

    // The author sees his private part, a visitor does not, the moderator does.
    cy.then(() => cy.request(`${base}&view=topic&catid=3&id=${ids.private.topic}`)).its('body').should('include', secret);
    cy.clearAllCookies();
    cy.then(() => cy.request(`${base}&view=topic&catid=3&id=${ids.private.topic}`)).its('body').should('include', 'A public reply').and('not.include', secret);
    cy.then(() => cy.request(`${base}&view=search&task=results&query=${secret}&searchdate=all`)).its('body').should('not.include', secret.toLowerCase() + '<');
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.then(() => cy.request(`${base}&view=topic&catid=3&id=${ids.private.topic}`)).its('body').should('include', secret);
  });
});
