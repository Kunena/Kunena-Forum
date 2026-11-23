// The edit history shows the versions of a message as they were typed. The text in [confidential] (for the author and the
// moderators), [private] (for nobody) and [hide] (for the members who are logged in) was shown there to every reader who may see the
// history (option editHistory "everybody"), a guest too, while the message itself hides it.
describe('Kunena edit history: the hidden blocks of a message', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress history ${stamp}`;
  const blocks = `[confidential]CONFTEXT${stamp}[/confidential] [private]PRIVTEXT${stamp}[/private] [hide]HIDETEXT${stamp}[/hide]`;
  const old = {};
  const ids = {};

  before(() => {
    cy.kunena_setOption('editHistory', '2').then((value) => {
      old.editHistory = value ?? null;
    });
  });

  beforeEach(() => {
    // The message of ci-admin (797) in a category which guests may read, as it is now, and its first version.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, '', '', 0, UNIX_TIMESTAMP(), 797, '', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 797, UNIX_TIMESTAMP(), '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'OPENNOW ${blocks} second')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
      cy.task('queryDB', `INSERT INTO #__kunena_messages_history (message_id, edited_by, edited_time, reason, subject, message) VALUES (${message.insertId}, 797, UNIX_TIMESTAMP(), '', '${subject}', 'OPENBEFORE ${blocks} first')`);
    });
  });

  after(() => {
    cy.kunena_setOption('editHistory', old.editHistory);
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE h FROM #__kunena_messages_history AS h INNER JOIN #__kunena_messages AS m ON m.id = h.message_id WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
  });

  it('shows a guest the open text of the versions, not the hidden blocks', () => {
    cy.request(`${base}&view=topic&layout=history&catid=3&id=${ids.topic}&mesid=${ids.message}`).its('body').then((body) => {
      expect(body, 'the old version').to.contain('OPENBEFORE');
      expect(body, 'the version of now').to.contain('OPENNOW');
      expect(body, 'the text in [confidential]').not.to.contain(`CONFTEXT${stamp}`);
      expect(body, 'the text in [private]').not.to.contain(`PRIVTEXT${stamp}`);
      expect(body, 'the text in [hide]').not.to.contain(`HIDETEXT${stamp}`);
    });
  });
});
