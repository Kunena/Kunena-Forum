// "Hide the replies from the guests" (the teaser of a category) shows a visitor the first post of a topic only. The search of a visitor
// searched the text of the replies all the same: a reply was found and counted (with the teaser in place of its text), so a visitor
// learnt which words a hidden reply has.
describe('Kunena visitors: the search of the replies which the teaser hides', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress teaser search ${stamp}`;
  const old = {};
  const ids = {};

  before(() => {
    cy.task('queryDB', 'SELECT params FROM #__kunena_categories WHERE id = 3').then((rows) => {
      old.params = rows[0].params;
      const params = JSON.parse(rows[0].params || '{}');
      params.teaser = '1';
      cy.task('queryDB', `UPDATE #__kunena_categories SET params = '${JSON.stringify(params)}' WHERE id = 3`);
    });
  });

  after(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET params = '${old.params}' WHERE id = 3`));
  });

  beforeEach(() => {
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
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Seenbyall${stamp} in the first post.')`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${message.insertId}, ${ids.topic}, 3, 'ci-user', 798, '', 'Re: ${subject}', UNIX_TIMESTAMP() - 1800, '', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((reply) => {
      ids.reply = reply.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${reply.insertId}, 'Hiddenword${stamp} in the reply.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET posts = 2, first_post_id = ${ids.first}, last_post_id = ${reply.insertId} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  const search = (word) => cy.request(`${base}&view=search&query=${word}&searchdate=all`).its('body');

  it('finds the first post, not the reply', () => {
    search(`Seenbyall${stamp}`).should('include', subject);
    search(`Hiddenword${stamp}`).then((body) => {
      expect(body, 'the hidden reply').to.not.include(`Re: ${subject}`);
      expect(body, 'the hidden reply').to.not.include(`#${ids.reply}`);
    });
  });
});
