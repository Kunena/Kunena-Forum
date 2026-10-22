// Two options of a category which take text away from some readers: "the users see their own topics only" (a user sees the topics
// of the others nowhere: not on the page of the topic, not in a list, a feed, the search or a JSON answer) and the teaser (a guest
// sees the first message of a topic, and no reply). The moderators see everything (so the data of the test is right).
describe('Kunena category options: own topics only, and the teaser', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};

  const makeCategory = (name, params, order) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress ${name} ${stamp}', 'cypress-${name}-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, ${order}, 1, '${params}', 'default')`,
  ).then((category) => cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-${name}-${stamp}', 'catid', ${category.insertId}, 0)`).then(() => category.insertId));

  const makeTopic = (category, author, subject, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category}, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${author}, '${text}', '', 0, UNIX_TIMESTAMP(), ${author}, '${text}', '', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${category}, 'author', ${author}, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
  )).then(() => ({ topic: topic.insertId, message: message.insertId, category }))));

  const addReply = (set, author, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${set.message}, ${set.topic}, ${set.category}, 'author', ${author}, '', 'Re: reply', UNIX_TIMESTAMP() + 1, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET posts = 2, last_post_id = ${message.insertId}, last_post_time = UNIX_TIMESTAMP() + 1, last_post_message = '${text}' WHERE id = ${set.topic}`,
  )).then(() => message.insertId));

  // The pages which can show the text of a topic of the category.
  const pages = (category, set) => [
    `${base}&view=topic&catid=${category}&id=${set.topic}`,
    `${base}&view=topic&catid=${category}&id=${set.topic}&mesid=${set.message}`,
    `${base}&view=topic&id=${set.topic}`,
    `${base}&view=topic&catid=${category}&id=${set.topic}&format=json`,
    `${base}&view=topic&catid=${category}&id=${set.topic}&format=raw`,
    `${base}&view=category&catid=${category}`,
    `${base}&view=category&catid=${category}&format=feed&layout=feed&type=rss`,
    `${base}&view=category&layout=list`,
    `${base}&view=topics`,
    `${base}&view=topics&mode=replies`,
    `${base}&view=topics&format=feed&layout=feed&type=rss`,
    `${base}&view=topics&format=json`,
    `${base}&view=topics&layout=unread`,
    `${base}&view=topics&layout=user&mode=started&userid=797`,
    `${base}&view=topics&layout=user&mode=posted&userid=797`,
    `${base}&view=search&task=results&query=${stamp}&searchdate=all`,
    `${base}&view=search&task=results&query=${stamp}&searchdate=all&catids=${category}`,
    `${base}&view=home`,
    `${base}&view=statistics`,
  ];

  const leaksOf = (urls, words) => {
    const leaks = [];

    urls.forEach((url) => {
      cy.request({ url, failOnStatusCode: false }).then((response) => {
        const body = typeof response.body === 'string' ? response.body : JSON.stringify(response.body);
        const found = words.filter((word) => body.includes(word));

        if (found.length) {
          leaks.push(`${url.replace(base, '')} shows ${found.join(', ')}`);
        }
      });
    });

    return cy.then(() => leaks);
  };

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE 'Cypress % ${stamp}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias LIKE 'cypress-%-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id IN (SELECT id FROM #__kunena_categories WHERE name LIKE 'Cypress % ${stamp}')`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE name LIKE 'Cypress % ${stamp}'`);
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
  });

  describe('the users see their own topics only', () => {
    const secret = `OWNTOPICSECRET${stamp}`;
    const mine = `MYOWNTOPIC${stamp}`;

    beforeEach(() => {
      makeCategory('own', '{"ownTopicsOnly":"1"}', 90).then((category) => {
        ids.category = category;

        return makeTopic(category, 797, `Cypress own admin ${stamp}`, secret);
      }).then((set) => {
        ids.adminTopic = set;

        return makeTopic(ids.category, user.id, `Cypress own user ${stamp}`, mine);
      }).then((set) => {
        ids.userTopic = set;
      });
    });

    it('shows a user his own topic, and nothing of the topic of another user', () => {
      cy.doFrontendLogin(user.name, user.password, false);
      cy.then(() => cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.userTopic.topic}`)).its('body').should('include', mine);
      cy.then(() => leaksOf(pages(ids.category, ids.adminTopic), [secret, `Cypress own admin ${stamp}`])).then((leaks) => expect(leaks, 'the pages which show the topic of the other user').to.deep.eq([]));
    });

    it('shows nothing of the topics to a guest', () => {
      cy.then(() => leaksOf(pages(ids.category, ids.adminTopic), [secret, `Cypress own admin ${stamp}`])).then((leaks) => expect(leaks, 'the pages for a guest').to.deep.eq([]));
    });

    it('shows both topics to the moderator', () => {
      cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
      cy.then(() => cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.userTopic.topic}`)).its('body').should('include', mine);
      cy.then(() => cy.request(`${base}&view=category&catid=${ids.category}`)).its('body').should('include', `Cypress own user ${stamp}`).and('include', `Cypress own admin ${stamp}`);
    });
  });

  describe('the teaser: a guest sees the first message only', () => {
    const first = `FIRSTMESSAGE${stamp}`;
    const reply = `REPLYSECRET${stamp}`;

    beforeEach(() => {
      makeCategory('teaser', '{"teaser":"1"}', 91).then((category) => {
        ids.category = category;

        return makeTopic(category, 797, `Cypress teaser ${stamp}`, first);
      }).then((set) => {
        ids.set = set;

        return addReply(set, 797, reply);
      }).then((message) => {
        ids.reply = message;
      });
    });

    it('shows the first message and no reply to a guest', () => {
      cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.set.topic}`).its('body').should('include', first);
      cy.then(() => leaksOf([...pages(ids.category, ids.set), `${base}&view=topic&catid=${ids.category}&id=${ids.set.topic}&mesid=${ids.reply}`], [reply])).then((leaks) => expect(leaks, 'the pages which show a reply to a guest').to.deep.eq([]));
    });

    it('shows the reply to a user', () => {
      cy.doFrontendLogin(user.name, user.password, false);
      cy.then(() => cy.request(`${base}&view=topic&catid=${ids.category}&id=${ids.set.topic}`)).its('body').should('include', reply);
    });
  });
});
