// "Hide the replies from the guests" (the teaser of a category) shows a visitor the first post of a topic only. When visitors may also
// reply there, the quote of a reply in the reply form (one quote with quote=1, or several with Multi-quote) put the text of the hidden
// reply into the editor of the visitor.
describe('Kunena visitors: the quote of a reply which the teaser hides', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress teaser quote ${stamp}`;
  const old = {};
  const ids = {};

  before(() => {
    cy.kunena_setOption('pubWrite', '1').then((value) => {
      old.pubWrite = value ?? null;
    });
    // The groups which may write in the category: the visitors too (Public and Guest), besides the standard groups.
    cy.task('queryDB', 'SELECT params FROM #__kunena_categories WHERE id = 3').then((rows) => {
      old.params = rows[0].params;
      const params = JSON.parse(rows[0].params || '{}');
      params.access_post = ['1', '2', '6', '8', '9'];
      params.access_reply = ['1', '2', '6', '8', '9'];
      params.teaser = '1';
      cy.task('queryDB', `UPDATE #__kunena_categories SET params = '${JSON.stringify(params)}' WHERE id = 3`);
    });
  });

  after(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET params = '${old.params}' WHERE id = 3`));
    cy.kunena_setOption('pubWrite', old.pubWrite);
  });

  beforeEach(() => {
    // A topic of a member whose first message has hidden text.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 798, 'x', '', 0, UNIX_TIMESTAMP(), 798, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-user', 798, '', '${subject}', UNIX_TIMESTAMP() - 3600, '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.first = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Seen by all ${stamp}.')`);

      // A reply of a member, which the teaser hides from the visitors.
      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${message.insertId}, ${ids.topic}, 3, 'ci-user', 798, '', 'Re: ${subject}', UNIX_TIMESTAMP() - 1800, '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((reply) => {
      ids.reply = reply.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${reply.insertId}, 'THE HIDDEN REPLY ${stamp}')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET posts = 2, first_post_id = ${ids.first}, last_post_id = ${reply.insertId} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('does not quote the hidden reply', () => {
    cy.request(`${base}&view=topic&layout=reply&catid=3&id=${ids.topic}&mesid=${ids.reply}&quote=1`).then((page) => {
      expect(page.body, 'the reply form').to.include('name="message"');
      expect(page.body, 'the hidden reply').to.not.include(`THE HIDDEN REPLY ${stamp}`);
    });
  });

  it('leaves the hidden reply out of a Multi-quote, and quotes the first post', () => {
    cy.request(`${base}&view=topic&layout=reply&catid=3&id=${ids.topic}&quotes=${ids.first},${ids.reply}`).then((page) => {
      expect(page.body, 'the first post is quoted').to.include(`Seen by all ${stamp}.`);
      expect(page.body, 'the hidden reply').to.not.include(`THE HIDDEN REPLY ${stamp}`);
    });
  });
});
