// With "allow the author to change the subject of the topic" (allowChangeSubject), the author of a topic may change its subject with
// a reply. The author was compared by the id of the user only: a guest (0) was the author of every topic of a guest or of an anonymous
// member, so any visitor who may write could rename such a topic with a reply.
describe('Kunena visitors: the subject of the topic of another visitor', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress guest subject ${stamp}`;
  const old = {};
  const ids = {};

  before(() => {
    cy.kunena_setOption('pubWrite', '1').then((value) => {
      old.pubWrite = value ?? null;
    });
    cy.kunena_setOption('allowChangeSubject', '1').then((value) => {
      old.allowChangeSubject = value ?? null;
    });
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.floodProtection = value ?? null;
    });
    // The groups which may write in the category: the visitors too (Public and Guest), besides the standard groups.
    cy.task('queryDB', 'SELECT params FROM #__kunena_categories WHERE id = 3').then((rows) => {
      old.params = rows[0].params;
      const params = JSON.parse(rows[0].params || '{}');
      params.access_post = ['1', '2', '6', '8', '9'];
      params.access_reply = ['1', '2', '6', '8', '9'];
      cy.task('queryDB', `UPDATE #__kunena_categories SET params = '${JSON.stringify(params)}' WHERE id = 3`);
    });
  });

  after(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET params = '${old.params}' WHERE id = 3`));
    cy.kunena_setOption('pubWrite', old.pubWrite);
    cy.then(() => cy.kunena_setOption('allowChangeSubject', old.allowChangeSubject));
    cy.then(() => cy.kunena_setOption('floodProtection', old.floodProtection));
  });

  beforeEach(() => {
    // A topic of a visitor.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 0, 'x', 'Visitor', 0, UNIX_TIMESTAMP(), 0, 'x', 'Visitor', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'Visitor', 0, '', '${subject}', UNIX_TIMESTAMP() - 3600, '10.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.first = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'The first post of a visitor.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('keeps the subject when another visitor replies with another one', () => {
    cy.request(`${base}&view=topic&layout=reply&catid=3&id=${ids.topic}`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=post`,
        form: true,
        body: {
          catid: 3,
          id: ids.topic,
          parentid: ids.first,
          authorname: `Other${stamp}`,
          email: '',
          subject: `RENAMED ${stamp}`,
          message: `A reply of another visitor ${stamp}.`,
          [token]: '1',
        },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_messages WHERE thread = ${ids.topic}`).then((rows) => {
      expect(rows[0].n, 'the reply is posted').to.eq(2);
    });
    cy.task('queryDB', `SELECT subject FROM #__kunena_topics WHERE id = ${ids.topic}`).then((rows) => {
      expect(rows[0].subject, 'the subject of the topic').to.eq(subject);
    });
  });
});
