// Removing a file in the editor (task topic.removeattachments, AJAX) follows the rule of the file itself: its uploader, or a
// moderator of the category of its message. A banned uploader, another user and a visitor remove nothing.
describe('Kunena attachments: who may remove a file in the editor', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};
  let oldBan;

  const exists = (id) => cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_attachments WHERE id = ${id}`).then((rows) => Number(rows[0].n) === 1);
  const remove = (id) => cy.request(`${base}&view=home`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x';

    return cy.request({ url: `${base}&view=topic&task=removeattachments&format=json&files_id_delete=${id}&${token}=1`, failOnStatusCode: false });
  });

  // A message in topic 9 with a file of its author (no file on the disk: only the row).
  const addMessage = (userid, name, key) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (10, 9, 3, '${name}', ${userid}, '', 'Re: Cypress remove ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => {
    ids[`${key}Message`] = message.insertId;
    cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A message with a file.')`);

    return cy.task(
      'queryDB',
      `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES (${message.insertId}, ${userid}, 0, 'cypress-${stamp}', 20, 'media/kunena/attachments/cypress-none', 'text/plain', 'cypress-${key}-${stamp}.txt', 'cypress-${key}-${stamp}.txt', '', 0, 0)`,
    );
  }).then((attachment) => {
    ids[key] = attachment.insertId;
  });

  beforeEach(() => {
    cy.task('queryDB', `SELECT CAST(banned AS CHAR) AS banned FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      oldBan = rows[0].banned;
    });
    addMessage(user.id, 'ci-user', 'own');
    addMessage(797, 'ci-admin', 'other');
  });

  afterEach(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET banned = '${oldBan}' WHERE userid = ${user.id}`));
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE user_id = ${user.id} AND category_id = 3`);
    cy.then(() => {
      cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE mesid IN (${ids.ownMessage}, ${ids.otherMessage})`);
      cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid IN (${ids.ownMessage}, ${ids.otherMessage})`);
      cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id IN (${ids.ownMessage}, ${ids.otherMessage})`);
    });
  });

  it('lets the uploader remove his file, and not the file of another user', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => remove(ids.other));
    cy.then(() => exists(ids.other)).should('eq', true);
    cy.then(() => remove(ids.own));
    cy.then(() => exists(ids.own)).should('eq', false);
  });

  it('lets a visitor remove nothing', () => {
    cy.then(() => remove(ids.own));
    cy.then(() => exists(ids.own)).should('eq', true);
  });

  it('lets a banned uploader remove nothing', () => {
    cy.task('queryDB', `UPDATE #__kunena_users SET banned = DATE_ADD(NOW(), INTERVAL 1 DAY) WHERE userid = ${user.id}`);
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => remove(ids.own));
    cy.then(() => exists(ids.own)).should('eq', true);
  });

  it('lets a moderator of the category remove the file of another user', () => {
    cy.task('queryDB', `INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES (${user.id}, 3, 1, 0, 0, '') ON DUPLICATE KEY UPDATE role = 1`);
    cy.doFrontendLogin(user.name, user.password, false);
    cy.then(() => remove(ids.other));
    cy.then(() => exists(ids.other)).should('eq', false);
  });
});
