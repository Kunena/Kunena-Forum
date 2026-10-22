// The feeds of the forum must be XML which a reader can parse, also when a topic has characters which XML does not like: & < > quotes,
// the end of a CDATA section (]]>), a control character and a character outside the first plane (an emoji).
describe('Kunena feeds: valid XML', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress feed ${stamp} & <b>bold</b> "quoted" 'single' ]]> end \u{1F600}`;
  const message = `Text & <i>html</i> ]]> and a control character \u0008 and more \u{1F600}`;
  const ids = {};

  const hex = (text) => Buffer.from(text, 'utf8').toString('hex');
  const text = (value) => `CONVERT(UNHEX('${hex(value)}') USING utf8mb4)`;

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, ${text(subject)}, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, ${text(message)}, 'ci-admin', 0, UNIX_TIMESTAMP(), 797, ${text(message)}, 'ci-admin', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'ci-admin', 797, '', ${text(subject)}, UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((result) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${result.insertId}, ${text(message)})`).then(() => cy.task(
      'queryDB',
      `UPDATE #__kunena_topics SET first_post_id = ${result.insertId}, last_post_id = ${result.insertId} WHERE id = ${ids.topic}`,
    )));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  const feeds = () => [
    `${base}&view=topics&format=feed&layout=feed&type=rss`,
    `${base}&view=topics&format=feed&layout=feed&type=atom`,
    `${base}&view=category&catid=3&format=feed&layout=feed&type=rss`,
    `${base}&view=category&catid=3&format=feed&layout=feed&type=atom`,
    `${base}&view=topics&mode=replies&format=feed&layout=feed`,
  ];

  it('gives XML which can be parsed', () => {
    feeds().forEach((url) => {
      cy.request({ url, failOnStatusCode: false }).then((response) => {
        expect(response.status, `the status of ${url.replace(base, '')}`).to.be.lessThan(500);

        const doc = new DOMParser().parseFromString(String(response.body), 'application/xml');
        const error = doc.querySelector('parsererror');

        expect(error ? error.textContent.slice(0, 200) : null, `the XML of ${url.replace(base, '')}`).to.eq(null);
      });
    });
  });

  it('has the topic in the feed', () => {
    cy.request(`${base}&view=topics&format=feed&layout=feed&type=rss`).its('body').then((body) => {
      expect(String(body)).to.include(`Cypress feed ${stamp}`);
    });
  });
});
