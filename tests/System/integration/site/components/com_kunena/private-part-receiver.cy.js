// The private part of a reply is for its writer, the moderators and the author of the post replied to, also when both users are
// regular users (no moderator). A third user and a visitor do not get it. The first post of a topic and a reply to a guest post
// have no receiver: the private part is for the writer only.
describe('Kunena private part: the author of the post replied to receives it', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const writer = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const folder = `media/kunena/attachments/cypress-receiver-${stamp}`;
  const old = {};
  const ids = {};

  const makeUser = (key, name) => cy.task(
    'queryDB',
    `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI ${name}', 'ci-${name}-${stamp}', 'ci-${name}-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
  ).then((result) => {
    ids[key] = result.insertId;
    cy.task('queryDB', `INSERT INTO #__user_usergroup_map (user_id, group_id) VALUES (${result.insertId}, 2)`);
    cy.task('queryDB', `INSERT INTO #__kunena_users (userid, posts) VALUES (${result.insertId}, 1)`);
  });

  const login = (key) => {
    cy.clearAllCookies();

    if (key === 'writer') {
      cy.doFrontendLogin(writer.name, writer.password, false);
    } else {
      cy.doFrontendLogin(`ci-${key}-${stamp}`, writer.password, false);
    }
  };

  // A topic in the public category 3 whose first post is of the given user (0: a guest).
  const makeTopic = (name, author) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress receiver ${name} ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - 60, ${author}, 'x', '', 0, UNIX_TIMESTAMP() - 60, ${author}, 'x', '', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'author', ${author}, '', 'Cypress receiver ${name} ${stamp}', UNIX_TIMESTAMP() - 60, '127.0.0.2', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'The first post.')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
  )).then(() => ({ topic: topic.insertId, message: message.insertId }))));

  const token = () => cy.request(`${base}&view=home`).then((page) => /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x');
  const send = (task, body) => token().then((value) => cy.request({
    method: 'POST',
    url: `${base}&view=topic&task=${task}`,
    form: true,
    body: { catid: 3, ...body, [value]: '1' },
    failOnStatusCode: false,
    followRedirect: false,
  }));
  const reply = (set, text, secret) => send('post', { id: set.topic, parentid: set.message, subject: 'Re: reply', message: text, message_private: secret });

  // The private part of the message, and the users of its map.
  const privateOf = (messageId) => cy.task('queryDB', `SELECT private_id FROM #__kunena_private_post_map WHERE message_id = ${messageId}`).then((rows) => {
    expect(rows, 'the private part of the message').to.have.length(1);

    return Number(rows[0].private_id);
  });
  const usersOf = (privateId) => cy.task('queryDB', `SELECT user_id FROM #__kunena_private_user_map WHERE private_id = ${privateId}`)
    .then((rows) => rows.map((row) => Number(row.user_id)).sort((a, b) => a - b));
  const replyOf = (set) => cy.task('queryDB', `SELECT id FROM #__kunena_messages WHERE thread = ${set.topic} AND parent = ${set.message} AND userid = ${writer.id}`).then((rows) => {
    expect(rows, 'the reply of the writer').to.have.length(1);

    return Number(rows[0].id);
  });
  const page = (set) => cy.request(`${base}&view=topic&catid=3&id=${set.topic}`).its('body');

  before(() => {
    cy.kunena_setOption('privateMessage', '1').then((value) => {
      old.privateMessage = value;
    });
    cy.kunena_setOption('attachmentProtection', '1').then((value) => {
      old.attachmentProtection = value;
    });
    cy.kunena_setOption('floodProtection', '0').then((value) => {
      old.floodProtection = value;
    });
  });

  after(() => {
    cy.kunena_setOption('privateMessage', old.privateMessage === undefined ? null : old.privateMessage);
    cy.kunena_setOption('attachmentProtection', old.attachmentProtection === undefined ? null : old.attachmentProtection);
    cy.kunena_setOption('floodProtection', old.floodProtection === undefined ? null : old.floodProtection);
  });

  beforeEach(() => {
    // B receives the private part, C is a third user: both are regular registered users.
    makeUser('receiver', 'receiver');
    makeUser('third', 'third');
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE 'Cypress receiver % ${stamp}'`).then((rows) => {
      rows.forEach(({ id }) => {
        const privates = `SELECT pm.private_id FROM #__kunena_private_post_map AS pm INNER JOIN #__kunena_messages AS m ON m.id = pm.message_id WHERE m.thread = ${id}`;

        cy.task('queryDB', `DELETE a FROM #__kunena_attachments AS a INNER JOIN #__kunena_private_attachment_map AS am ON am.attachment_id = a.id WHERE am.private_id IN (${privates})`);
        cy.task('queryDB', `DELETE FROM #__kunena_private_attachment_map WHERE private_id IN (SELECT private_id FROM (${privates}) AS x)`);
        cy.task('queryDB', `DELETE FROM #__kunena_private_user_map WHERE private_id IN (SELECT private_id FROM (${privates}) AS x)`);
        cy.task('queryDB', `DELETE FROM #__kunena_private WHERE id IN (SELECT private_id FROM (${privates}) AS x)`);
        cy.task('queryDB', `DELETE pm FROM #__kunena_private_post_map AS pm INNER JOIN #__kunena_messages AS m ON m.id = pm.message_id WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE a FROM #__kunena_attachments AS a INNER JOIN #__kunena_messages AS m ON m.id = a.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('deleteRelativePath', folder);
    [ids.receiver, ids.third].forEach((id) => {
      cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${id}`);
      cy.task('queryDB', `DELETE FROM #__kunena_sessions WHERE userid = ${id}`);
      cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${id}`);
      cy.task('queryDB', `DELETE FROM #__user_usergroup_map WHERE user_id = ${id}`);
      cy.task('queryDB', `DELETE FROM #__users WHERE id = ${id}`);
    });
    cy.kunena_fixUserCounters();
  });

  it('gives the private part of a reply between two regular users to the author of the post replied to', () => {
    const secret = `RECEIVERNOTE${stamp}`;

    cy.then(() => makeTopic('reply', ids.receiver)).then((set) => {
      ids.set = set;
    });
    login('writer');
    cy.then(() => reply(ids.set, 'A public reply.', secret));
    cy.then(() => replyOf(ids.set)).then((id) => privateOf(id)).then((id) => usersOf(id))
      .should('deep.eq', [writer.id, ids.receiver].sort((a, b) => a - b));

    login('receiver');
    cy.then(() => page(ids.set)).should('include', secret);
    login('third');
    cy.then(() => page(ids.set)).should('include', 'A public reply').and('not.include', secret);
    cy.clearAllCookies();
    cy.then(() => page(ids.set)).should('include', 'A public reply').and('not.include', secret);
  });

  it('gives the private file of the reply to the author of the post replied to, not to a third user', () => {
    const name = `receiverfile${stamp}.txt`;
    const file = () => cy.request({ url: `${base}&view=attachment&id=${ids.file}&format=raw`, failOnStatusCode: false }).then((response) => String(response.body));

    cy.then(() => makeTopic('file', ids.receiver)).then((set) => {
      ids.set = set;
    });
    login('writer');
    cy.then(() => reply(ids.set, 'A public reply with a private file.', `RECEIVERFILE${stamp}`));
    cy.then(() => replyOf(ids.set)).then((messageId) => privateOf(messageId).then((privateId) => {
      cy.writeFile(`${Cypress.expose('cmsPath')}/${folder}/${name}`, `the content of ${name}`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${messageId}, ${writer.id}, 32, '${name}', 30, '${folder}', 'text/plain', '${name}', '${name}', '', 0, 0)`,
      ).then((attachment) => {
        ids.file = attachment.insertId;

        return cy.task('queryDB', `INSERT INTO #__kunena_private_attachment_map (private_id, attachment_id) VALUES (${privateId}, ${attachment.insertId})`);
      });
    }));

    login('receiver');
    cy.then(() => file()).should('include', `the content of ${name}`);
    login('third');
    cy.then(() => file()).should('not.include', `the content of ${name}`);
  });

  it('gives the private part added with an edit to the author of the post replied to', () => {
    const secret = `RECEIVEREDIT${stamp}`;

    cy.then(() => makeTopic('edit', ids.receiver)).then((set) => {
      ids.set = set;

      // The reply of the writer, without a private part.
      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${set.message}, ${set.topic}, 3, '${writer.name}', ${writer.id}, '', 'Re: reply', UNIX_TIMESTAMP() - 30, '127.0.0.2', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.reply = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A public reply.')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET posts = 2, last_post_id = ${message.insertId}, last_post_time = UNIX_TIMESTAMP() - 30, last_post_userid = ${writer.id} WHERE id = ${ids.set.topic}`);
    });

    login('writer');
    cy.then(() => send('edit', { mesid: ids.reply, id: ids.set.topic, subject: 'Re: reply', message: 'A public reply, edited.', message_private: secret }));
    cy.then(() => privateOf(ids.reply)).then((id) => usersOf(id)).should('deep.eq', [writer.id, ids.receiver].sort((a, b) => a - b));

    login('receiver');
    cy.then(() => page(ids.set)).should('include', secret);
    login('third');
    cy.then(() => page(ids.set)).should('include', 'A public reply, edited').and('not.include', secret);
  });

  it('gives the private part of a reply to a guest post to the writer only', () => {
    cy.then(() => makeTopic('guest', 0)).then((set) => {
      ids.set = set;
    });
    login('writer');
    cy.then(() => reply(ids.set, 'A reply to a guest.', `RECEIVERGUEST${stamp}`));
    cy.then(() => replyOf(ids.set)).then((id) => privateOf(id)).then((id) => usersOf(id)).should('deep.eq', [writer.id]);
  });

  it('gives the private part of the first post of a topic to the writer only', () => {
    const subject = `Cypress receiver first ${stamp}`;

    login('writer');
    cy.then(() => send('post', { subject, message: 'A new topic.', message_private: `RECEIVERFIRST${stamp}` }));
    cy.task('queryDB', `SELECT first_post_id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      expect(rows, 'the new topic').to.have.length(1);

      return privateOf(Number(rows[0].first_post_id));
    }).then((id) => usersOf(id)).should('deep.eq', [writer.id]);
  });
});
