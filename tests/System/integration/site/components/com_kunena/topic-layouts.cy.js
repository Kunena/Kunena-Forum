// The topic layouts flat, threaded and indented (issue #10246): an address with layout=flat, threaded or indented gave a 404, and
// Aurelia had no threaded or indented layout. The topic of the test has a first message, a reply to it and a reply to that reply.
// The visitor is a guest: the layout is kept in the session, no profile is changed.
describe('Kunena topics: the flat, threaded and indented layouts', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const admin = 797;
  const ids = {};

  const message = (parent, age, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, 3, 'ci-admin', ${admin}, '', 'Cypress layouts ${stamp}', UNIX_TIMESTAMP() - ${age}, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((row) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${row.insertId}, '${text}')`).then(() => row.insertId));

  const topicUrl = (layout) => `${base}&view=topic&catid=3&id=${ids.topic}${layout ? `&layout=${layout}` : ''}`;

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress layouts ${stamp}', 0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - 300, ${admin}, 'x', '', 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return message(0, 300, 'The first message.');
    }).then((first) => {
      ids.first = first;

      return message(first, 200, 'A reply to the first message.');
    }).then((reply) => {
      ids.reply = reply;

      return message(reply, 100, 'A reply to the reply.');
    }).then((last) => {
      ids.last = last;
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.first}, last_post_id = ${last} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('opens the topic in each layout of its address', () => {
    cy.visit(topicUrl('threaded'));
    cy.get('.kunena-topic-tree tbody tr').should('have.length', 3);
    cy.get('.kunena-topic-tree tbody tr').eq(2).find('.ktree').should('have.length', 4);
    cy.get('.kunena-topic-tree tbody tr[aria-current="true"]').should('have.length', 1);
    cy.get('.kunena-topic-indent').should('not.exist');

    cy.visit(topicUrl('indented'));
    cy.get('.kunena-topic-tree').should('not.exist');
    cy.get('.topic-item-messages').should('contain', 'A reply to the reply.');
    cy.get('.kunena-topic-indent').should('have.length', 2);

    cy.visit(topicUrl('flat'));
    cy.get('.kunena-topic-tree').should('not.exist');
    cy.get('.kunena-topic-indent').should('not.exist');
    cy.get('.topic-item-messages').should('contain', 'The first message.').and('contain', 'A reply to the reply.');
  });

  it('keeps the layout chosen in the Layout menu of the topic', () => {
    cy.visit(topicUrl());
    cy.get('.kunena-topic-tree').should('not.exist');

    cy.get('.kbutton-threaded').first().parents('.btn-group').find('.dropdown-toggle-split').click();
    cy.get('.kbutton-threaded:visible').click();

    cy.get('.kunena-topic-tree tbody tr').should('have.length', 3);
    cy.visit(topicUrl());
    cy.get('.kunena-topic-tree tbody tr').should('have.length', 3);

    cy.get('.kbutton-flat').first().parents('.btn-group').find('.dropdown-toggle-split').click();
    cy.get('.kbutton-flat:visible').click();

    cy.get('.kunena-topic-tree').should('not.exist');
    cy.get('.topic-item-messages').should('contain', 'A reply to the reply.');
  });
});
