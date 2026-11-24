// In the lists of posts (the posts of a profile, the recent posts) the column of the last post of the topic shows the avatar of the
// last poster, with "View <last poster>'s profile" as its title, but the avatar linked to the author of the listed post: a click on
// the last poster's picture opened the profile of somebody else.
describe('Kunena lists of posts: the avatar of the last poster', () => {
  const stamp = Date.now();
  const subject = `Cypress avatar ${stamp}`;
  const admin = 797;
  const user = 798;
  const old = {};
  const ids = {};

  const addMessage = (parent, userid, name) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${parent}, ${ids.topic}, 3, '${name}', ${userid}, '', '${subject}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'x')`).then(() => message.insertId));

  before(() => {
    cy.kunena_setOption('avatarOnCategory', '1').then((value) => {
      old.avatarOnCategory = value ?? null;
    });
  });

  after(() => {
    cy.kunena_setOption('avatarOnCategory', old.avatarOnCategory);
  });

  beforeEach(() => {
    // A topic of the administrator, answered last by ci-user.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${admin}, 'x', '', 0, UNIX_TIMESTAMP(), ${user}, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;
    });
    cy.then(() => addMessage(0, admin, 'ci-admin')).then((id) => {
      ids.first = id;
    });
    cy.then(() => addMessage(ids.first, user, 'ci-user')).then((id) => {
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.first}, last_post_id = ${id} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.subject = '${subject}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE subject = '${subject}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE subject = '${subject}'`);
  });

  it('links the avatar to the last poster', () => {
    cy.request(`/index.php?option=com_kunena&view=topics&layout=posts&mode=recent&userid=${admin}&sel=720`).then((page) => {
      const body = String(page.body);
      const row = body.slice(body.indexOf(subject));
      const cell = /class="col-6 col-md-3">\s*<a[^>]*href="([^"]*)"/.exec(row);

      expect(body, 'the post of the administrator').to.include(subject);
      expect(cell, 'the avatar of the last poster').not.to.equal(null);
      expect(cell[1], 'the link of the avatar').to.match(new RegExp(`user/${user}\\b|userid=${user}\\b`));
    });
  });
});
