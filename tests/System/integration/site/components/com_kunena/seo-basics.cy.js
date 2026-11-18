// The basics of SEO of the pages of the forum, as a guest sees them: the description of the credits (a text of the code with mixed
// quotes), one h1 on the index and on the page of a category (the index had none in Agora, one per section in Aurelia), the canonical
// of a category on its first page (and of a section, which pointed to the home of the site), the number of the page in the title of
// a long topic, and one permanent redirect from an old address (it was a 301 to /component/kunena/... and a 303). And the smaller ones:
// the replies of the page in the structured data of a topic, its own image (the first one, the regex took everything up to the last
// [/img]), descriptions which are no copy of the title, no index for a profile without a post, one h1 on a profile, and no
// structured breadcrumb on a missing page.
describe('Kunena SEO: titles, headings, canonicals and redirects', () => {
  const site = '/index.php';
  const plain = `${site}?option=com_kunena`;
  const stamp = Date.now();
  const subject = `Cypress SEO ${stamp}`;
  const ids = {};
  const old = {};

  const canonical = (body) => (/<link href="([^"]*)" rel="canonical"/.exec(body) || [])[1] || null;
  const h1s = (body) => (body.match(/<h1[\s>]/g) || []).length;
  const meta = (body, name) => (new RegExp(`<meta (?:name|property)="${name}" content="([^"]*)"`).exec(body) || [])[1] ?? null;
  const posting = (body) => [...body.matchAll(/<script type="application\/ld\+json">([\s\S]*?)<\/script>/g)]
    .map((match) => JSON.parse(match[1]))
    .find((data) => data['@type'] === 'DiscussionForumPosting');
  const images = '[img]https://example.com/first.png[/img] and [img]https://example.com/second.png[/img]';

  before(() => {
    cy.kunena_setOption('messagesPerPage', '2').then((value) => {
      old.messagesPerPage = value;
    });
  });

  after(() => {
    cy.then(() => cy.kunena_setOption('messagesPerPage', old.messagesPerPage ?? null));
  });

  // The rows of queryDB are removed after every test: the topic is made for each test.
  beforeEach(() => {
    // A topic of three messages: two pages of two messages. The times are in the past (a message of the future is a scheduled reply,
    // which nobody sees yet).
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, '${subject}', 0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'Message 1 ${images}', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      [1, 2, 3].forEach((n) => {
        cy.task(
          'queryDB',
          `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, 3, 'ci-admin', 797, '', '${subject}', UNIX_TIMESTAMP() - ${10 - n}, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
        ).then((message) => {
          cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'Message ${n}${n === 1 ? ` ${images}` : ''}')`);

          if (n === 1) {
            cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${message.insertId}, last_post_id = ${message.insertId} WHERE id = ${ids.topic}`);
          }
        });
      });
    });
  });

  afterEach(() => {
    cy.then(() => {
      if (ids.topic) {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
      }
    });
  });

  it('gives the credits a description of one line', () => {
    cy.request(`${plain}&view=credits`).its('body').then((body) => {
      const description = (/<meta name="description" content="([^"]*)"/.exec(body) || [])[1];

      expect(description, 'the description').to.match(/^Kunena is the ideal forum extension for Joomla\..*www\.kunena\.org\.$/);
      expect(description, 'no code in the description').to.not.match(/[\t\n]|&quot;|" \./);
    });
  });

  it('has one h1 on the index and on the page of a category', () => {
    cy.request(`${plain}&view=category&layout=list`).its('body').then((body) => {
      expect(h1s(body), 'the h1 of the index').to.eq(1);
    });
    cy.request(`${plain}&view=category&catid=3`).its('body').then((body) => {
      expect(h1s(body), 'the h1 of the category').to.eq(1);
      expect(body, 'the h1 is the category').to.match(/<h1[^>]*id="category-header3"/);
    });
  });

  it('gives each page of a category its own canonical, the first one too', () => {
    cy.request(`${plain}&view=category&catid=3`).then((response) => {
      const own = canonical(response.body);

      expect(own, 'the canonical of the category').to.match(/\/forum\/sub1$/);
      cy.request(`${response.redirects ? response.redirects.at(-1).split(': ')[1] : ''}?start=0&utm_source=cypress`).its('body').then((body) => {
        expect(canonical(body), 'the same page with parameters').to.eq(own);
      });
    });
    // A section: its own address, not the home of the site.
    cy.request(`${plain}&view=category&catid=1`).its('body').then((body) => {
      expect(canonical(body), 'the canonical of a section').to.match(/\/forum\/[a-z0-9-]+$/);
    });
  });

  it('puts the number of the page in the title of a long topic', () => {
    cy.request(`${plain}&view=topic&catid=3&id=${ids.topic}`).its('body').then((body) => {
      expect(/<title>([^<]*)<\/title>/.exec(body)[1], 'the first page').to.not.contain(' - Page');
    });
    cy.request(`${plain}&view=topic&catid=3&id=${ids.topic}&limitstart=2`).its('body').then((body) => {
      expect(/<title>([^<]*)<\/title>/.exec(body)[1], 'the second page').to.contain(`${subject} - Page 2`);
    });
  });

  it('redirects an old address of a topic once, permanently, to its address', () => {
    cy.request({ url: `${plain}&view=topic&catid=3&id=${ids.topic}`, followRedirect: false }).then((response) => {
      expect(response.status, 'the redirect').to.eq(301);
      expect(response.headers.location, 'the address of the topic').to.match(/\/forum\/sub1\/\d+-cypress-seo-\d+$/);
    });
    // A list goes to the address of its menu item: every step is permanent.
    cy.request(`${plain}&view=category&layout=list`).then((response) => {
      (response.redirects || []).forEach((step) => {
        expect(step, 'a step of the redirect').to.match(/^301: /);
      });
    });
  });

  it('describes the topic with the replies of its page and its own image', () => {
    cy.request(`${plain}&view=topic&catid=3&id=${ids.topic}`).its('body').then((body) => {
      const data = posting(body);

      expect(data.image, 'the image of the topic').to.eq('https://example.com/first.png');
      expect(meta(body, 'og:image'), 'the image of Open Graph').to.eq('https://example.com/first.png');
      // The first page: the first message (the posting itself) and the first reply.
      expect(data.comment, 'the replies of the page').to.have.length(1);
      expect(data.comment[0]['@type']).to.eq('Comment');
      expect(data.comment[0].text, 'the text of the reply').to.eq('Message 2');
      expect(data.comment[0].author.name, 'its author').to.eq('ci-admin');
      expect(data.comment[0].datePublished, 'its date').to.match(/^\d{4}-\d\d-\d\dT/);
    });
    cy.request(`${plain}&view=topic&catid=3&id=${ids.topic}&limitstart=2`).its('body').then((body) => {
      expect(posting(body).comment.map((comment) => comment.text), 'the replies of the second page').to.deep.eq(['Message 3']);
    });
  });

  it('describes the lists with a sentence, not with their title', () => {
    [`${plain}&view=category&layout=list`, `${plain}&view=user&layout=list`, `${plain}&view=topics&mode=replies`].forEach((url) => {
      cy.request(url).its('body').then((body) => {
        const title       = /<title>([^<]*)<\/title>/.exec(body)[1];
        const description = meta(body, 'description');

        expect(description, `the description of ${url}`).to.match(/^.{40,}\.( - .*)?$/);
        // It was the title and the name of the forum ("Recent Topics: Kunena").
        expect(description.length, `more than the title "${title}"`).to.be.greaterThan(title.length + 30);
        expect(meta(body, 'og:title'), 'the title of Open Graph').to.not.be.oneOf([null, '']);
      });
    });
  });

  it('keeps a profile without a post out of the index, with one h1 on a profile', () => {
    cy.request(`${plain}&view=user&userid=797`).its('body').then((body) => {
      expect(meta(body, 'robots'), 'a profile with posts').to.eq('index, follow');
      expect(h1s(body), 'the h1 of the profile').to.eq(1);
    });
    cy.task(
      'queryDB',
      `INSERT INTO #__users (name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset, authProvider) SELECT 'CI Silent', 'ci-silent-${stamp}', 'ci-silent-${stamp}@example.com', password, 0, 0, NOW(), NULL, '', '{}', NULL, 0, '', '', 0, '' FROM #__users WHERE id = 798`,
    ).then((user) => {
      cy.request(`${plain}&view=user&userid=${user.insertId}`).its('body').then((body) => {
        expect(meta(body, 'robots'), 'a profile without a post').to.eq('noindex, follow');
      });
      // The forum made the profile of the user when it showed it.
      cy.task('queryDB', `DELETE FROM #__kunena_users WHERE userid = ${user.insertId}`);
    });
  });

  it('shows the breadcrumb of a missing page without structured data', () => {
    cy.request({ url: `${plain}&view=topic&catid=3&id=99999999`, failOnStatusCode: false }).then((response) => {
      expect(response.status, 'a missing topic').to.eq(404);
      expect(response.body, 'the breadcrumb').to.contain('mod-kunena-breadcrumbs');
      expect(response.body, 'its structured data').to.not.contain('schema.org/BreadcrumbList');
    });
    cy.request(`${plain}&view=category&catid=3`).its('body').should('contain', 'schema.org/BreadcrumbList');
  });
});
