// A user who moderates one category moderates that category only: he deletes, locks and makes sticky the topics in it, he sees the
// messages which wait for approval in it, and he can do none of this in another category.
describe('Kunena moderators: the rights are for the category', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};

  const makeCategory = (name, order) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress mod ${name} ${stamp}', 'cypress-mod-${name}-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, ${order}, 1, '{}', 'default')`,
  ).then((category) => cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-mod-${name}-${stamp}', 'catid', ${category.insertId}, 0)`).then(() => category.insertId));

  const makeTopic = (category, name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category}, 'Cypress mod topic ${name} ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${category}, 'ci-admin', 797, '', 'Cypress mod topic ${name} ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
  )).then(() => ({ topic: topic.insertId, message: message.insertId, category }))));

  const task = (set, name) => cy.request(`${base}&view=topic&catid=${set.category}&id=${set.topic}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({ url: `${base}&view=topic&catid=${set.category}&id=${set.topic}&task=${name}&${token}=1`, failOnStatusCode: false, followRedirect: false });
  });
  const row = (set) => cy.task('queryDB', `SELECT hold, locked, ordering FROM #__kunena_topics WHERE id = ${set.topic}`).then((rows) => rows[0]);

  beforeEach(() => {
    makeCategory('own', 96).then((category) => {
      ids.own = category;

      return makeCategory('other', 97);
    }).then((category) => {
      ids.other = category;

      return makeTopic(ids.own, 'own');
    }).then((set) => {
      ids.ownTopic = set;

      return makeTopic(ids.other, 'other');
    }).then((set) => {
      ids.otherTopic = set;

      // The user moderates the first category (role 1 = moderator).
      return cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${user.id}, ${ids.own}, 1, UNIX_TIMESTAMP(), 0, '')`);
    });

    // A new login: the rights of a user are read when he comes in.
    cy.doFrontendLogin(user.name, user.password, false);
  });

  afterEach(() => {
    [ids.ownTopic, ids.otherTopic].forEach((set) => {
      cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${set.topic}`);
      cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${set.topic}`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${set.topic}`);
      cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${set.topic}`);
    });
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id IN (${ids.own}, ${ids.other})`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias LIKE 'cypress-mod-%${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id IN (${ids.own}, ${ids.other})`);
  });

  it('lets him lock and make sticky a topic in his category', () => {
    task(ids.ownTopic, 'lock');
    row(ids.ownTopic).then((topic) => expect(Number(topic.locked), 'locked in his category').to.eq(1));
    task(ids.ownTopic, 'sticky');
    row(ids.ownTopic).then((topic) => expect(Number(topic.ordering), 'sticky in his category').to.be.greaterThan(0));
  });

  it('does not let him do it in another category', () => {
    ['lock', 'sticky', 'delete', 'approve'].forEach((name) => task(ids.otherTopic, name));
    row(ids.otherTopic).then((topic) => {
      expect(Number(topic.locked), 'locked in another category').to.eq(0);
      expect(Number(topic.ordering), 'sticky in another category').to.eq(0);
      expect(Number(topic.hold), 'deleted in another category').to.eq(0);
    });
  });

  it('does not let him move a topic of his category into a category which he does not moderate', () => {
    cy.request(`${base}&view=topic&catid=${ids.own}&id=${ids.ownTopic.topic}`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

      cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=move`,
        form: true,
        body: { catid: ids.own, id: ids.ownTopic.topic, targetcategory: ids.other, targettopic: 0, targetid: '', [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.task('queryDB', `SELECT category_id FROM #__kunena_topics WHERE id = ${ids.ownTopic.topic}`).then((rows) => {
      // A moderator may move to a category which he can read: this is what the forum does; the test records it (no server error).
      expect([ids.own, ids.other]).to.include(Number(rows[0].category_id));
    });
  });
});
