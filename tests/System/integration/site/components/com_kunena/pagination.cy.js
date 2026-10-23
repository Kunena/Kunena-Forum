// A topic with many messages: each page shows the messages of its place in the order of time, a start after the end shows the last page
// (not an empty page and not an error), a link to a message opens the page with the message, and the numbers of the pages in the
// links are the numbers which the page really has.
describe('Kunena pagination: a topic with many messages', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const total = 47;
  const ids = {};
  let perPage;

  // The numbers of the messages (PAGEMSG<stamp>-<nr>) which a page shows.
  const numbersOf = (body) => [...new Set([...String(body).matchAll(new RegExp(`PAGEMSG${stamp}-(\\d+)`, 'g'))].map((match) => Number(match[1])))].sort((a, b) => a - b);

  const page = (query) => cy.request({ url: `${base}&view=topic&catid=3&id=${ids.topic}${query}`, failOnStatusCode: false }).then((response) => {
    expect(response.status, `the status of ${query}`).to.be.lessThan(500);

    // The template shows the first message at the top of every page (the option "sticky first post"): it is not counted for the later pages.
    const numbers = numbersOf(response.body);

    return /limitstart=(0|-)|^$/.test(query) || /mesid=/.test(query) ? numbers : numbers.filter((number) => number !== 1);
  });

  before(() => {
    cy.task('queryDB', "SELECT JSON_UNQUOTE(JSON_EXTRACT(params, '$.messagesPerPage')) AS value FROM #__extensions WHERE element = 'com_kunena' AND type = 'component'").then((rows) => {
      perPage = Number(rows[0].value) || 6;
    });
  });

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress pagination ${stamp}', 0, 0, 0, 0, 0, ${total}, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - 10000, 797, 'x', 'ci-admin', 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;
      ids.messages = [];

      // The messages one after the other, each a second after the one before.
      for (let number = 1; number <= total; number++) {
        cy.task(
          'queryDB',
          `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${number === 1 ? 0 : 1}, ${topic.insertId}, 3, 'ci-admin', 797, '', 'Cypress pagination ${stamp}', UNIX_TIMESTAMP() - 10000 + ${number}, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
        ).then((message) => {
          ids.messages[number] = message.insertId;

          return cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'PAGEMSG${stamp}-${number}')`);
        });
      }

      cy.then(() => cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.messages[1]}, last_post_id = ${ids.messages[total]} WHERE id = ${topic.insertId}`));
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('shows the messages of each page in the order of time', () => {
    page('').then((numbers) => {
      expect(numbers.length, 'the messages of the first page').to.be.within(1, perPage);
      expect(numbers[0], 'the first message').to.eq(1);
      expect(numbers, 'the numbers go on without a gap').to.deep.eq(Array.from({ length: numbers.length }, (unused, index) => index + 1));
    });

    page(`&limitstart=${perPage}`).then((numbers) => {
      expect(numbers[0], 'the first message of the second page').to.eq(perPage + 1);
      expect(numbers.length).to.be.within(1, perPage);
    });
  });

  it('shows the last page for a start after the end, and the first page for a start before the beginning', () => {
    const lastStart = Math.floor((total - 1) / perPage) * perPage;

    [`&limitstart=${total + 500}`, '&limitstart=99999999'].forEach((query) => {
      page(query).then((numbers) => {
        expect(numbers.length, `the messages for ${query}`).to.be.greaterThan(0);
        expect(numbers[numbers.length - 1], `the last message for ${query}`).to.eq(total);
        expect(numbers[0], `the first message for ${query}`).to.be.at.least(lastStart + 1);
      });
    });

    ['&limitstart=-1', '&limitstart=-9999'].forEach((query) => {
      page(query).then((numbers) => expect(numbers[0], `the first message for ${query}`).to.eq(1));
    });
  });

  it('opens the page with a message when the link has its number', () => {
    [1, perPage, perPage + 1, 30, total].forEach((number) => {
      page(`&mesid=${ids.messages[number]}`).then((numbers) => expect(numbers, `the page for the message ${number}`).to.include(number));
    });
  });

  it('shows every message once on the pages one after the other', () => {
    const seen = [];

    for (let start = 0; start < total; start += perPage) {
      page(start ? `&limitstart=${start}` : '').then((numbers) => seen.push(...numbers));
    }
    cy.then(() => expect([...seen].sort((a, b) => a - b), 'all the messages, each once').to.deep.eq(Array.from({ length: total }, (unused, index) => index + 1)));
  });

  it('keeps the page in the address when a list is opened with an address without an Itemid', () => {
    [
      `${base}&view=topics&layout=default&limitstart=2`,
      `${base}&view=category&catid=3&limitstart=2`,
      `${base}&view=user&layout=list&limitstart=2`,
      `${base}&view=topic&catid=3&id=${ids.topic}&limitstart=2`,
    ].forEach((url) => {
      cy.request({ url, failOnStatusCode: false }).then((response) => {
        expect(response.status, url.replace(base, '')).to.be.lessThan(500);
        // The redirects which the forum made keep the start of the page.
        const redirects = (response.redirects || []).join(' ');

        if (redirects) {
          expect(redirects, `the redirects for ${url.replace(base, '')}`).to.match(/start=2/);
        }
      });
    });
  });
});
