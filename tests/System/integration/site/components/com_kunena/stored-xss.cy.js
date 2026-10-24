// Texts with HTML and script in every field which the forum shows to other people (written to the database as they would be by
// an import, an old version or a plugin, so only the output can protect). No page may run the script or carry the markup unescaped.
// The pages are opened in the browser as a guest, a user and the administrator.
describe('Kunena output: stored texts with markup', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const payload = '"\'><img src=x onerror=window.__xss=1><script>window.__xss=1</script>';
  // The same markup for the fields which are short.
  const mid = '"><img src=x onerror=__xss=1>';
  const short = '"onfocus=__xss=1 x="';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};
  let profile;
  // The real name of the user and the option "username": the tests change them, they are put back after each test (also
  // after a failure, else the other specs see the forum with the real names and a name which is a piece of markup).
  let realName;
  let usernameOption;

  const q = (value) => `'${String(value).replaceAll('\\', '\\\\').replaceAll("'", "\\'")}'`;

  beforeEach(() => {
    usernameOption = undefined;
    cy.task('queryDB', `SELECT name FROM #__users WHERE id = ${user.id}`).then((rows) => {
      realName = rows[0].name;
    });
    cy.task('queryDB', `SELECT signature, personalText, location, websitename, websiteurl, status_text, \`rank\` FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      profile = JSON.parse(JSON.stringify(rows[0]));
    });
    cy.task(
      'queryDB',
      `UPDATE #__kunena_users SET signature = ${q(payload)}, personalText = ${q(payload)}, location = ${q(mid)}, websitename = ${q(mid)}, websiteurl = ${q(`http://a.b/${mid}`)}, status_text = ${q(payload)}, \`rank\` = 1 WHERE userid = ${user.id}`,
    );
    // The title of a rank (a table without an id: the task which removes rows does not know it; it is put back after the test).
    cy.task('queryDB', `UPDATE #__kunena_ranks SET rankTitle = ${q(payload)} WHERE rankId = 1`);
    cy.task('queryDB', `INSERT INTO #__kunena_announcement (title, sdescription, description, created, published, showdate, publish_up, publish_down, ordering, created_by) VALUES (${q(payload)}, ${q(payload)}, ${q(payload)}, NOW(), 1, 1, '1000-01-01 00:00:00', '1000-01-01 00:00:00', 0, 797)`);

    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, ${q(payload)}, 'cypress-xss-${stamp}', ${q(mid)}, ${q(short)}, ${q(payload)}, ${q(payload)}, '', 'joomla.level', 1, 1, 1, 8, 1, 93, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-xss-${stamp}', 'catid', ${ids.category}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${ids.category}, ${q(payload)}, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 0, ${q(payload)}, ${q(payload)}, 0, UNIX_TIMESTAMP(), 0, ${q(payload)}, ${q(payload)}, 0, '')`,
      );
    }).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${ids.topic}, ${ids.category}, ${q(payload)}, 0, ${q(`a${payload}@example.com`)}, ${q(payload)}, UNIX_TIMESTAMP(), ${q(payload)}, 0, 0, 0, 0, 0, 0, ${user.id}, UNIX_TIMESTAMP(), ${q(payload)})`,
      );
    }).then((message) => {
      ids.message = message.insertId;

      return cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${ids.message}, ${q(`${payload} [url=${payload}]link[/url] [img]${payload}[/img] [quote="${payload}"]quoted[/quote]`)})`);
    }).then(() => cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.message}, last_post_id = ${ids.message} WHERE id = ${ids.topic}`))
      .then(() => cy.task('queryDB', `INSERT INTO #__kunena_polls (title, threadid, polltimetolive) VALUES (${q(payload)}, ${ids.topic}, '1000-01-01 00:00:00')`))
      .then((poll) => {
        ids.poll = poll.insertId;
        cy.task('queryDB', `INSERT INTO #__kunena_polls_options (pollid, text, votes) VALUES (${ids.poll}, ${q(payload)}, 0), (${ids.poll}, ${q(payload)}, 0)`);

        return cy.task('queryDB', `UPDATE #__kunena_topics SET poll_id = ${ids.poll} WHERE id = ${ids.topic}`);
      });
  });

  afterEach(() => {
    cy.task('queryDB', `UPDATE #__users SET name = ${q(realName)} WHERE id = ${user.id}`);

    if (usernameOption !== undefined) {
      cy.kunena_setOption('username', usernameOption);
    }

    if (!ids.topic) {
      return;
    }

    cy.task('queryDB', `DELETE FROM #__kunena_polls_options WHERE pollid = ${ids.poll}`);
    cy.task('queryDB', `DELETE FROM #__kunena_polls WHERE id = ${ids.poll}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-xss-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.task('queryDB', "UPDATE #__kunena_ranks SET rankTitle = 'COM_KUNENA_SAMPLEDATA_RANK1' WHERE rankId = 1");
    cy.task('queryDB', `DELETE FROM #__kunena_announcement WHERE title = ${q(payload)}`);
    cy.then(() => cy.task(
      'queryDB',
      `UPDATE #__kunena_users SET signature = ${profile.signature === null ? 'NULL' : q(profile.signature)}, personalText = ${profile.personalText === null ? 'NULL' : q(profile.personalText)}, location = ${profile.location === null ? 'NULL' : q(profile.location)}, websitename = ${profile.websitename === null ? 'NULL' : q(profile.websitename)}, websiteurl = ${profile.websiteurl === null ? 'NULL' : q(profile.websiteurl)}, status_text = ${profile.status_text === null ? 'NULL' : q(profile.status_text)}, \`rank\` = ${Number(profile.rank)} WHERE userid = ${user.id}`,
    ));
  });

  const pages = () => [
    `${base}&view=home`,
    `${base}&view=category&layout=list`,
    `${base}&view=category&catid=${ids.category}`,
    `${base}&view=topic&catid=${ids.category}&id=${ids.topic}`,
    `${base}&view=topic&catid=${ids.category}&id=${ids.topic}&mesid=${ids.message}`,
    `${base}&view=topics`,
    `${base}&view=topics&mode=replies`,
    `${base}&view=topics&layout=user&mode=topics&userid=${user.id}`,
    `${base}&view=user&layout=default&userid=${user.id}`,
    `${base}&view=user&layout=list`,
    `${base}&view=user&layout=list&search=cypress`,
    `${base}&view=announcement&layout=listing`,
    `${base}&view=statistics`,
    `${base}&view=search&task=results&query=img`,
    `${base}&view=search&task=results&query=${encodeURIComponent(payload)}`,
    `${base}&view=misc`,
  ];

  const adminPages = () => [
    `${base}&view=topic&layout=reply&catid=${ids.category}&id=${ids.topic}`,
    `${base}&view=topic&layout=edit&catid=${ids.category}&id=${ids.topic}&mesid=${ids.message}`,
    `${base}&view=topic&layout=moderate&catid=${ids.category}&id=${ids.topic}`,
    `${base}&view=category&layout=manage&catid=${ids.category}`,
    `${base}&view=user&layout=edit&userid=${user.id}`,
  ];

  const check = (urls) => {
    urls.forEach((url) => {
      cy.visit({ url, failOnStatusCode: false });
      cy.wait(250);
      cy.window().then((win) => expect(win.__xss, `the script of the text ran on ${url.replace(base, '')}`).to.be.undefined);
      cy.document().then((doc) => {
        const html = doc.documentElement.outerHTML;

        const handlers = [...doc.querySelectorAll('*')].filter((element) => [...element.attributes].some((attribute) => ['onerror', 'onfocus'].includes(attribute.name) && /^(window\.)?__xss=1$/.test(attribute.value)));

        expect(handlers.length, `an element with a handler of the text on ${url.replace(base, '')}`).to.eq(0);
        expect(html.includes('<script>window.__xss=1</script>'), `the script of the text in the page ${url.replace(base, '')}`).to.eq(false);
      });
    });
  };

  it('is escaped for a guest', () => {
    check(pages());
  });

  it('is escaped for a user', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    check(pages());
  });

  it('is escaped in the list of users of the search form', () => {
    const name = '<img src=x onerror=__xss=1>';

    cy.task('queryDB', `UPDATE #__users SET name = ${q(name)} WHERE id = ${user.id}`);
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.visit(`${base}&view=search`);
    cy.get('#kusersearch').type('@<img', { delay: 100 });
    cy.wait(1000);
    cy.window().then((win) => expect(win.__xss, 'the script of a name ran').to.be.undefined);
    cy.document().then((doc) => expect(doc.querySelectorAll('img[src="x"]').length, 'an element made of a name').to.eq(0));
  });

  it('is escaped when the real name of a user is shown, not the username', () => {
    const name = '"><img src=x onerror=__xss=1>';

    cy.kunena_setOption('username', '0').then((oldOption) => {
      usernameOption = oldOption ?? '1';
    });
    cy.task('queryDB', `UPDATE #__users SET name = ${q(name)} WHERE id = ${user.id}`);
    check(pages());
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    check([...pages(), ...adminPages()]);
  });

  it('is escaped for the administrator, also in the forms', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    check([...pages(), ...adminPages()]);
  });
});
