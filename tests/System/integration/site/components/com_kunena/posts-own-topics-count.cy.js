// The lists of posts (the posts of a profile, recent posts, thank you) counted the posts in topics of other members in the
// categories where everybody sees only his own topics ("own topics only"): the list left them out, but the number of the posts and
// the pages showed them (and how much a member wrote there).
describe('Kunena lists of posts: the categories with own topics only', () => {
  const stamp = Date.now();
  const user = 798;
  const alias = `cypress-own-${stamp}`;
  const ids = {};

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress own topics ${stamp}', '${alias}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 90, 1, '{"ownTopicsOnly":"1"}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;

      // A topic of the administrator, with a reply of ci-user.
      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Cypress own ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), ${user}, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, ${ids.category}, 'ci-user', ${user}, '', 'Re: Cypress own ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A reply in a topic of somebody else')`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE alias = '${alias}'`);
  });

  it('does not count a post which the visitor may not see', () => {
    // The posts of ci-user of the last 30 days, for a guest: only the reply in the topic of the administrator.
    cy.request(`/index.php?option=com_kunena&view=topics&layout=posts&mode=recent&userid=${user}&sel=720`).then((page) => {
      const body = String(page.body);
      const count = /\(\s*(\d+)\s[^)]*\)\s*<\/small>/.exec(body);

      expect(body, 'the reply').not.to.include(`Re: Cypress own ${stamp}`);
      expect(count, 'the count of the posts').not.to.equal(null);
      expect(Number(count[1]), 'the count of the posts').to.eq(0);
    });
  });
});
