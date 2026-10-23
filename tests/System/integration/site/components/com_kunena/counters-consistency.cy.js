// The forum keeps numbers in the rows of the topics and the categories (the number of posts, the first and the last post, the number of
// topics and posts of a category). After every action of a user or a moderator the numbers must be what a count of the messages gives.
describe('Kunena counters: the numbers of topics and categories', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ids = {};

  const typeInEditor = (text) => {
    cy.window().should('have.property', 'sceditor');
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty')
      .then(($body) => cy.wrap($body).click().type(text, { parseSpecialCharSequences: false }));
  };

  const topicOf = (subject) => cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => rows[0].id);

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

  const expectConsistent = (step) => problems([ids.category, ids.other]).then((found) => expect(found, `the numbers after: ${step}`).to.deep.eq([]));

  const task = (task, query) => cy.request(`${base}&view=topic&catid=${ids.category}&id=${query.topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];
    const mes = query.mesid ? `&mesid=${query.mesid}` : '';

    return cy.request({ url: `${base}&view=topic&catid=${query.catid ?? ids.category}&id=${query.topic}${mes}&task=${task}&${token}=1`, failOnStatusCode: false, followRedirect: false });
  });

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

  before(() => {
    // The categories of this test (a table with an id: the rows are removed after the first test, so they are made in the first hook).
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias LIKE 'cypress-counters-%'`);
  });

  beforeEach(() => {
    ['category', 'other'].forEach((name, index) => {
      cy.task(
        'queryDB',
        `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress counters ${name} ${stamp}', 'cypress-counters-${name}-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, ${95 + index}, 1, '{}', 'default')`,
      ).then((category) => {
        ids[name] = category.insertId;
        cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-counters-${name}-${stamp}', 'catid', ${category.insertId}, 0)`);
      });
    });
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id IN (${ids.category}, ${ids.other})`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias LIKE 'cypress-counters-%'`);
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id IN (${ids.category}, ${ids.other})`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id IN (${ids.category}, ${ids.other})`);
    // The counters of the users and the rows of the topics which are gone.
    cy.kunena_fixUserCounters();
  });

  it('keeps the numbers right when topics and replies are written', () => {
    newTopic(`Cypress counters one ${stamp}`).then((first) => {
      ids.first = first;
      expectConsistent('a new topic');
      reply(first, 'The first reply.');
      expectConsistent('a reply');
      reply(first, 'The second reply.');
      expectConsistent('a second reply');
    });
    newTopic(`Cypress counters two ${stamp}`).then(() => expectConsistent('a second topic'));
  });

  it('keeps the numbers right when a reply is deleted, brought back and removed for ever', () => {
    newTopic(`Cypress counters three ${stamp}`).then((topic) => {
      reply(topic, 'A reply to delete.').then((message) => {
        reply(topic, 'A reply to keep.');

        task('delete', { topic, mesid: message });
        expectConsistent('a reply deleted');
        task('undelete', { topic, mesid: message });
        expectConsistent('a reply brought back');
        task('delete', { topic, mesid: message });
        task('permdelete', { topic, mesid: message });
        expectConsistent('a reply removed for ever');
      });
    });
  });

  it('keeps the numbers right when a topic is deleted, brought back and moved', () => {
    newTopic(`Cypress counters four ${stamp}`).then((topic) => {
      reply(topic, 'A reply.');
      newTopic(`Cypress counters five ${stamp}`);

      task('delete', { topic });
      expectConsistent('a topic deleted');
      task('undelete', { topic });
      expectConsistent('a topic brought back');

      cy.visit(`${base}&view=topic&layout=moderate&catid=${ids.category}&id=${topic}`);
      cy.get('#tab2-tab').click();
      cy.get('#modcategorieslist select').select(String(ids.other), { force: true });
      cy.get('button[name="submit"]').filter(':visible').click();
      expectConsistent('a topic moved to another category');
    });
  });

  // The form of the moderator (move, merge and split) sends these fields.
  const move = (fields) => cy.request(`${base}&view=topic&catid=${ids.category}&id=${fields.id}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({ method: 'POST', url: `${base}&view=topic&task=move`, form: true, body: { catid: ids.category, targettopic: 0, targetid: '', ...fields, [token]: '1' }, failOnStatusCode: false, followRedirect: false });
  });

  it('keeps the numbers right when a topic is moved with a shadow, split and merged', () => {
    newTopic(`Cypress counters six ${stamp}`).then((first) => {
      reply(first, 'A reply for the split.').then((splitMessage) => {
        reply(first, 'Another reply.');

        // The reply goes to a new topic of the same category.
        move({ id: first, mesid: splitMessage, targetcategory: ids.category, subject: `Cypress counters split ${stamp}`, mode: 'selected' });
        expectConsistent('a reply split into a new topic');

        topicOf(`Cypress counters split ${stamp}`).then((second) => {
          // The new topic is merged into the first one.
          move({ id: second, targetcategory: ids.category, targettopic: -1, targetid: first });
          expectConsistent('a topic merged into another topic');
        });

        // The first topic goes to the other category and leaves a shadow.
        move({ id: first, targetcategory: ids.other, shadow: 1 });
        expectConsistent('a topic moved with a shadow');
      });
    });
  });

  it('keeps the numbers right when the last reply is deleted, and when a reply goes to a topic of another category', () => {
    newTopic(`Cypress counters seven ${stamp}`).then((first) => {
      reply(first, 'The first reply.');
      reply(first, 'The last reply.').then((last) => {
        task('delete', { topic: first, mesid: last });
        expectConsistent('the last reply deleted');
        task('undelete', { topic: first, mesid: last });
        expectConsistent('the last reply brought back');
      });
    });

    newTopic(`Cypress counters eight ${stamp}`).then((target) => {
      newTopic(`Cypress counters nine ${stamp}`).then((source) => {
        reply(source, 'A reply which moves.').then((mes) => {
          // The reply goes to a topic of the other category.
          cy.visit(`${base}&view=topic&layout=moderate&catid=${ids.category}&id=${source}&mesid=${mes}`);
          move({ id: source, mesid: mes, targetcategory: ids.other, subject: `Cypress counters ten ${stamp}`, mode: 'selected' });
          expectConsistent('a reply moved to a new topic of another category');
          move({ id: target, targetcategory: ids.other });
          expectConsistent('a topic moved into the other category');
        });
      });
    });
  });
});
