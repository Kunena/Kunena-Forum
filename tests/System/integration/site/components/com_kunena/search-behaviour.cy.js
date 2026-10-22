// The search of the forum: a word finds the messages with the word, a - before a word leaves them out, a % or an _ is that character
// (not a wildcard), the search can look at the subject only, and strange words give no error.
describe('Kunena search: what is found', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const word = `zqx${stamp}`;
  const texts = {
    plain: `${word} alpha plain`,
    underscore: `${word} beta under_score`,
    percent: `${word} gamma 100% sure`,
    other: `${word} delta underXscore other words`,
  };

  const addMessage = (name, text) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress search ${name} ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', 'Cypress search ${name} ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text.replace(/'/g, "\\'")}')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
  ))));

  // The names of the topics which the search shows.
  const found = (query, extra = '') => cy.request({ url: `${base}&view=search&task=results&query=${encodeURIComponent(query)}&searchdate=all${extra}`, failOnStatusCode: false }).then((response) => {
    expect(response.status, `the status for "${query}"`).to.be.lessThan(500);

    return ['plain', 'underscore', 'percent', 'other'].filter((name) => String(response.body).includes(`Cypress search ${name} ${stamp}`));
  });

  beforeEach(() => {
    Object.entries(texts).forEach(([name, text]) => addMessage(name, text));
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE 'Cypress search % ${stamp}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
  });

  it('finds the messages with a word', () => {
    found(word).should('deep.eq', ['plain', 'underscore', 'percent', 'other']);
    found('alpha').should('deep.eq', ['plain']);
    found(`${word} gamma`).should('include', 'percent');
  });

  it('leaves out the messages with a word which has a - before it', () => {
    found(`${word} -alpha`).should('deep.eq', ['underscore', 'percent', 'other']);
  });

  it('takes a % and an _ as the characters', () => {
    found('100%').should('deep.eq', ['percent']);
    found('under_score').should('deep.eq', ['underscore']);
    // An _ would match any character (the X of underXscore).
    // A % alone is not every message.
    found('%').should('not.include', 'plain');
  });

  it('can look at the subject only', () => {
    found('alpha', '&titleonly=1').should('deep.eq', []);
    found(`${word} delta`, '&titleonly=1').should('deep.eq', []);
    found('Cypress', '&titleonly=1').should('include', 'plain');
  });

  it('does not break on strange words', () => {
    ["'", '"', '\\', '`', '--', ';', '<script>', '0x', '()', '[]', 'ünïcode ☃', 'a'.repeat(300), '   ', ''].forEach((query) => found(query));
  });
});
