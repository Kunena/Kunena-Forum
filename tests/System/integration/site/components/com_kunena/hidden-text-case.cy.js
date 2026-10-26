// The tags of the BBCode are not case sensitive: [HIDE]...[/HIDE] hides its text on the page like [hide]. The places which take
// the hidden parts out of a text with a regular expression (the description of the page, the feeds, the previews) must do that in
// the same way, or a tag in capitals shows the text to everybody there.
describe('Kunena hidden text: tags in capitals', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const words = {
    open: `zqxopen${stamp}`, hidden: `zqxhidden${stamp}`, confidential: `zqxconf${stamp}`, nestedHidden: `zqxnesthide${stamp}`, nestedConfidential: `zqxnestconf${stamp}`,
  };
  // A tag in a tag of the same kind: the hidden text after the inner block is hidden on the page too. (The regular expression
  // stopped at the first closing tag and left that text, with the closing tag, in the description and the tooltips.)
  const text = `${words.open} [HIDE]${words.hidden}[/HIDE] [Confidential]${words.confidential}[/Confidential]`
    + ` [hide]a[hide]b[/hide]${words.nestedHidden}[/hide] [confidential]c[confidential]d[/confidential]${words.nestedConfidential}[/confidential]`;
  const ids = {};
  let rssType;

  // The feeds show the text of the messages with this type (with 'topic' they show the number of messages only).
  before(() => {
    cy.kunena_setOption('rssType', 'post').then((old) => {
      rssType = old;
    });
  });

  after(() => {
    cy.kunena_setOption('rssType', rssType ?? 'topic');
  });

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress capitals ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, '${text}', '', 0, UNIX_TIMESTAMP(), 797, '${text}', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, 3, 'ci-admin', 797, '', 'Cypress capitals ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${ids.message}, '${text}')`);
      cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.message}, last_post_id = ${ids.message} WHERE id = ${ids.topic}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
  });

  it('keeps the text of the tags in capitals from a visitor on the page and in the feeds', () => {
    const pages = [
      `${base}&view=topic&catid=3&id=${ids.topic}`,
      `${base}&view=category&catid=3`,
      `${base}&view=category&catid=3&format=feed&layout=feed&type=rss`,
      `${base}&view=topics&format=feed&layout=feed&type=rss`,
    ];

    pages.forEach((url) => {
      cy.request({ url, failOnStatusCode: false }).then((response) => {
        const body = String(response.body);

        expect(response.status, `the status of ${url}`).to.be.lessThan(500);
        expect(body, `the hidden word on ${url}`).to.not.include(words.hidden);
        expect(body, `the confidential word on ${url}`).to.not.include(words.confidential);
        expect(body, `the nested hidden word on ${url}`).to.not.include(words.nestedHidden);
        expect(body, `the nested confidential word on ${url}`).to.not.include(words.nestedConfidential);
        expect(body, `the open word on ${url}`).to.include(words.open);
      });
    });
  });
});
