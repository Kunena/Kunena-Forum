// The search must not find words which the reader may not see: the text in [hide] is for the logged-in users, the text in
// [confidential] for the author and the moderators. A hit on such a word tells the reader that the word is in the message.
describe('Kunena search: the hidden parts of a message', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const words = { open: `zqxopen${stamp}`, hidden: `zqxhidden${stamp}`, confidential: `zqxconf${stamp}`, own: `zqxown${stamp}`, nested: `zqxnest${stamp}`,
    spacedHidden: `zqxsphide${stamp}`, spacedConfidential: `zqxspconf${stamp}`, priv: `zqxpriv${stamp}`,
  };
  const messages = {
    hidden: { author: admin, text: `${words.open} and [hide]${words.hidden}[/hide]` },
    confidential: { author: admin, text: `[confidential]${words.confidential}[/confidential] of the administrator` },
    own: { author: user.id, text: `[confidential]${words.own}[/confidential] of the user` },
    // A block in a block of the same tag: the word after the inner block is hidden too (the pattern stopped at the first
    // closing tag and the search found the word).
    nested: { author: admin, text: `[confidential]a[confidential]b[/confidential]${words.nested}[/confidential] nested` },
    // A closing tag with a space ([/hide ]) closes the block for the parser too: the message was not looked at for hidden parts
    // (only the ones with [/hide] or [/confidential] were), and the search found the words.
    spaced: { author: admin, text: `[hide]${words.spacedHidden}[/hide ] and [confidential]${words.spacedConfidential}[/confidential ]` },
    // The text in [private] is shown to nobody (the tag shows a note in its place): the search found its words.
    priv: { author: admin, text: `[private]${words.priv}[/private] of the administrator` },
  };

  const addMessage = (name, { author, text }) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress hidden ${name} ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), ${author}, 'x', '', 0, UNIX_TIMESTAMP(), ${author}, 'x', '', 0, '')`,
  ).then((topic) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'author', ${author}, '', 'Cypress hidden ${name} ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
  ).then((message) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${text}')`).then(() => cy.task(
    'queryDB',
    `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${topic.insertId}`,
  ))));

  // The names of the messages which the search shows for a word.
  const found = (word) => cy.request({ url: `${base}&view=search&task=results&query=${word}&searchdate=all`, failOnStatusCode: false }).then((response) => {
    expect(response.status, `the status for "${word}"`).to.be.lessThan(500);

    return Object.keys(messages).filter((name) => String(response.body).includes(`Cypress hidden ${name} ${stamp}`));
  });

  beforeEach(() => {
    Object.entries(messages).forEach(([name, message]) => addMessage(name, message));
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE 'Cypress hidden % ${stamp}'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
  });

  it('finds for a visitor the open words, and not the hidden and confidential ones', () => {
    found(words.open).should('deep.eq', ['hidden']);
    found(words.hidden).should('deep.eq', []);
    found(words.confidential).should('deep.eq', []);
    found(words.nested).should('deep.eq', []);
    found(words.spacedHidden).should('deep.eq', []);
    found(words.spacedConfidential).should('deep.eq', []);
    found(words.priv).should('deep.eq', []);
  });

  it('finds for a user the hidden words and his own confidential ones, and not those of another', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    found(words.hidden).should('deep.eq', ['hidden']);
    found(words.own).should('deep.eq', ['own']);
    found(words.confidential).should('deep.eq', []);
    found(words.nested).should('deep.eq', []);
    found(words.spacedHidden).should('deep.eq', ['spaced']);
    found(words.spacedConfidential).should('deep.eq', []);
    found(words.priv).should('deep.eq', []);
  });

  it('finds for a moderator everything', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    found(words.hidden).should('deep.eq', ['hidden']);
    found(words.confidential).should('deep.eq', ['confidential']);
    found(words.own).should('deep.eq', ['own']);
  });
});
