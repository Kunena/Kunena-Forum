// A category with review keeps its numbers of topics and posts right through every step of the review: a new topic of a user
// waits, the approval counts it, a change of the author takes it back until the next approval, a change of a moderator keeps a
// waiting message waiting, and the topics and replies of a moderator count at once. No recount of the category is needed.
describe('Kunena review: the numbers of a category with review', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x' };
  const ids = {};

  const typeInEditor = (text) => {
    cy.window().should('have.property', 'sceditor');
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty')
      .then(($body) => cy.wrap($body).click().type(text, { parseSpecialCharSequences: false }));
  };

  const asUser = () => {
    cy.clearAllCookies();
    cy.doFrontendLogin(user.name, user.password, false);
  };

  const asAdministrator = () => {
    cy.clearAllCookies();
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  };

  // The topic and its first message.
  const topicOf = (subject) => cy.task(
    'queryDB',
    `SELECT t.id, MIN(m.id) AS first FROM #__kunena_topics AS t INNER JOIN #__kunena_messages AS m ON m.thread = t.id WHERE t.subject = '${subject}' GROUP BY t.id`,
  ).then((rows) => ({ id: rows[0].id, first: rows[0].first }));

  const holds = (topic, mesid) => cy.task(
    'queryDB',
    `SELECT (SELECT hold FROM #__kunena_topics WHERE id = ${topic}) AS topic, (SELECT hold FROM #__kunena_messages WHERE id = ${mesid}) AS message`,
  ).then((rows) => ({ topic: Number(rows[0].topic), message: Number(rows[0].message) }));

  const newTopic = (subject) => {
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    cy.get('#postform input[name="subject"]').type(subject);
    typeInEditor(`The text of ${subject}.`);
    cy.get('#form_submit_button').should('not.be.disabled').click();
    return topicOf(subject);
  };

  const reply = (topic, text) => {
    cy.visit(`${base}&view=topic&layout=reply&catid=${ids.category}&id=${topic}`);
    typeInEditor(text);
    cy.get('#form_submit_button').should('not.be.disabled').click();
    return cy.task('queryDB', `SELECT id FROM #__kunena_messages WHERE thread = ${topic} ORDER BY id DESC LIMIT 1`).then((rows) => rows[0].id);
  };

  const edit = (topic, mesid) => {
    cy.visit(`${base}&view=topic&layout=edit&catid=${ids.category}&id=${topic}&mesid=${mesid}`);
    typeInEditor(' Changed.');
    cy.get('#form_submit_button').should('not.be.disabled').click();
  };

  // A task of the buttons of the topic page (approve), with the token of that page.
  const task = (task, query) => cy.request(`${base}&view=topic&catid=${ids.category}&id=${query.topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];
    const mes = query.mesid ? `&mesid=${query.mesid}` : '';

    return cy.request({ url: `${base}&view=topic&catid=${ids.category}&id=${query.topic}${mes}&task=${task}&${token}=1`, failOnStatusCode: false, followRedirect: false });
  });

  // What the stored numbers of a topic and of a category must be, from a count of the messages.
  const problems = (categoryIds) => cy.task(
    'queryDB',
    `SELECT t.id, t.category_id, t.posts, t.first_post_id, t.last_post_id,
       (SELECT COUNT(*) FROM #__kunena_messages AS m WHERE m.thread = t.id AND m.hold = 0) AS real_posts,
       (SELECT m.id FROM #__kunena_messages AS m WHERE m.thread = t.id AND m.hold = 0 ORDER BY m.time ASC, m.id ASC LIMIT 1) AS real_first,
       (SELECT m.id FROM #__kunena_messages AS m WHERE m.thread = t.id AND m.hold = 0 ORDER BY m.time DESC, m.id DESC LIMIT 1) AS real_last
     FROM #__kunena_topics AS t WHERE t.hold = 0 AND t.moved_id = 0 AND t.category_id IN (${categoryIds.join(',')})`,
  ).then((topics) => {
    const found = [];

    topics.forEach((topic) => {
      if (Number(topic.posts) !== Number(topic.real_posts)) {
        found.push(`topic ${topic.id}: posts ${topic.posts}, a count gives ${topic.real_posts}`);
      }

      if (Number(topic.real_posts) > 0 && Number(topic.first_post_id) !== Number(topic.real_first)) {
        found.push(`topic ${topic.id}: first post ${topic.first_post_id}, a count gives ${topic.real_first}`);
      }

      if (Number(topic.real_posts) > 0 && Number(topic.last_post_id) !== Number(topic.real_last)) {
        found.push(`topic ${topic.id}: last post ${topic.last_post_id}, a count gives ${topic.real_last}`);
      }
    });

    return cy.task(
      'queryDB',
      `SELECT c.id, c.numTopics, c.numPosts,
         (SELECT COUNT(*) FROM #__kunena_topics AS t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0) AS real_topics,
         (SELECT COUNT(*) FROM #__kunena_messages AS m INNER JOIN #__kunena_topics AS t ON t.id = m.thread WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0 AND m.hold = 0) AS real_posts
       FROM #__kunena_categories AS c WHERE c.id IN (${categoryIds.join(',')})`,
    ).then((categories) => {
      categories.forEach((category) => {
        if (Number(category.numTopics) !== Number(category.real_topics)) {
          found.push(`category ${category.id}: ${category.numTopics} topics, a count gives ${category.real_topics}`);
        }

        if (Number(category.numPosts) !== Number(category.real_posts)) {
          found.push(`category ${category.id}: ${category.numPosts} posts, a count gives ${category.real_posts}`);
        }
      });

      return found;
    });
  });

  // The stored numbers of the category are the expected ones, and every number agrees with a count.
  const expectNumbers = (step, expected) => {
    cy.task('queryDB', `SELECT numTopics, numPosts FROM #__kunena_categories WHERE id = ${ids.category}`)
      .then((rows) => expect({ topics: Number(rows[0].numTopics), posts: Number(rows[0].numPosts) }, `the numbers of the category after: ${step}`).to.deep.eq(expected));
    problems([ids.category]).then((found) => expect(found, `the numbers agree with a count after: ${step}`).to.deep.eq([]));
  };

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, review, params, iconset) VALUES (1, 'Cypress review counters ${stamp}', 'cypress-review-counters-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 97, 1, 1, '{}', 'default')`,
    ).then((result) => {
      ids.category = result.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-review-counters-${stamp}', 'catid', ${result.insertId}, 0)`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${ids.category}`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_logs WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-review-counters-${stamp}'`);
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    // The rows of the users and the notifications of the inbox about the messages which are gone.
    cy.kunena_fixUserCounters();
  });

  it('counts a topic of a user when it is approved, not while it waits again after a change of the author', () => {
    // Step 1: the topic of the user waits.
    asUser();
    newTopic(`Cypress review counters one ${stamp}`).then((topic) => {
      ids.topic = topic.id;
      ids.first = topic.first;
    });
    cy.then(() => holds(ids.topic, ids.first)).then((hold) => expect(hold, 'the new topic waits').to.deep.eq({ topic: 1, message: 1 }));
    expectNumbers('step 1, a new topic of the user', { topics: 0, posts: 0 });

    // Step 2: the administrator approves the topic.
    asAdministrator();
    cy.then(() => task('approve', { topic: ids.topic }));
    cy.then(() => holds(ids.topic, ids.first)).then((hold) => expect(hold, 'the approved topic').to.deep.eq({ topic: 0, message: 0 }));
    expectNumbers('step 2, the approval of the topic', { topics: 1, posts: 1 });

    // Step 3: the author changes the first message of his topic, which has no replies: the message and the topic wait again.
    asUser();
    cy.then(() => edit(ids.topic, ids.first));
    cy.then(() => holds(ids.topic, ids.first)).then((hold) => expect(hold, 'the changed topic waits again').to.deep.eq({ topic: 1, message: 1 }));
    expectNumbers('step 3, a change of the author', { topics: 0, posts: 0 });

    // Step 4: the administrator approves the topic again.
    asAdministrator();
    cy.then(() => task('approve', { topic: ids.topic }));
    cy.then(() => holds(ids.topic, ids.first)).then((hold) => expect(hold, 'the topic approved again').to.deep.eq({ topic: 0, message: 0 }));
    expectNumbers('step 4, the second approval', { topics: 1, posts: 1 });
  });

  it('keeps a waiting topic waiting and uncounted when a moderator changes it', () => {
    asUser();
    newTopic(`Cypress review counters two ${stamp}`).then((topic) => {
      ids.topic = topic.id;
      ids.first = topic.first;
    });
    expectNumbers('a new topic of the user', { topics: 0, posts: 0 });

    // Step 5: the administrator changes the message of the waiting topic.
    asAdministrator();
    cy.then(() => edit(ids.topic, ids.first));
    cy.then(() => holds(ids.topic, ids.first)).then((hold) => expect(hold, 'the topic changed by a moderator still waits').to.deep.eq({ topic: 1, message: 1 }));
    expectNumbers('step 5, a change of a moderator', { topics: 0, posts: 0 });
  });

  it('counts a reply of a user when it is approved, not while it waits again after a change of the author', () => {
    asAdministrator();
    newTopic(`Cypress review counters three ${stamp}`).then((topic) => {
      ids.topic = topic.id;
    });
    expectNumbers('a topic of the administrator', { topics: 1, posts: 1 });

    // Step 6: the user replies, the reply is approved, changed by its author and approved again.
    asUser();
    cy.then(() => reply(ids.topic, 'A reply of the user.')).then((mesid) => {
      ids.reply = mesid;
    });
    cy.then(() => holds(ids.topic, ids.reply)).then((hold) => expect(hold, 'the reply waits').to.deep.eq({ topic: 0, message: 1 }));
    expectNumbers('step 6, a reply of the user', { topics: 1, posts: 1 });

    asAdministrator();
    cy.then(() => task('approve', { topic: ids.topic, mesid: ids.reply }));
    cy.then(() => holds(ids.topic, ids.reply)).then((hold) => expect(hold, 'the approved reply').to.deep.eq({ topic: 0, message: 0 }));
    expectNumbers('step 6, the approval of the reply', { topics: 1, posts: 2 });

    asUser();
    cy.then(() => edit(ids.topic, ids.reply));
    cy.then(() => holds(ids.topic, ids.reply)).then((hold) => expect(hold, 'the changed reply waits again').to.deep.eq({ topic: 0, message: 1 }));
    expectNumbers('step 6, a change of the reply by its author', { topics: 1, posts: 1 });

    asAdministrator();
    cy.then(() => task('approve', { topic: ids.topic, mesid: ids.reply }));
    cy.then(() => holds(ids.topic, ids.reply)).then((hold) => expect(hold, 'the reply approved again').to.deep.eq({ topic: 0, message: 0 }));
    expectNumbers('step 6, the second approval of the reply', { topics: 1, posts: 2 });
  });

  it('counts a topic and a reply of a moderator at once', () => {
    // Step 7: the administrator writes a topic and a reply, nothing waits.
    asAdministrator();
    newTopic(`Cypress review counters four ${stamp}`).then((topic) => {
      ids.topic = topic.id;
    });
    expectNumbers('step 7, a topic of the administrator', { topics: 1, posts: 1 });
    cy.then(() => reply(ids.topic, 'A reply of the administrator.'));
    expectNumbers('step 7, a reply of the administrator', { topics: 1, posts: 2 });
  });
});
