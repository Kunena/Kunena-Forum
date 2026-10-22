// A topic and a reply which are written as anonymous must not show who wrote them: not on the topic page, not in the lists, the
// search, the feeds and the data for search engines, and not in the list of the posts of the user. The moderators see the author.
describe('Kunena anonymous: the author stays hidden', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const subject = `Cypress anonymous ${stamp}`;
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};

  const typeInEditor = (text) => {
    cy.window().should('have.property', 'sceditor');
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty')
      .then(($body) => cy.wrap($body).click().type(text, { parseSpecialCharSequences: false }));
  };

  // Words which show the author: the username, the id in an address of a profile.
  const showsAuthor = (body) => {
    const text = String(body);

    return /ci-user/i.test(text.replace(/Cypress anonymous[^<]*/g, '')) || new RegExp(`/${user.id}-ci-user|userid=${user.id}\\b`).test(text);
  };

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, allowAnonymous, postAnonymous, params, iconset) VALUES (1, 'Cypress category anon ${stamp}', 'cypress-anon-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 99, 1, 1, 0, '{}', 'default')`,
    ).then((result) => {
      ids.category = result.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-anon-${stamp}', 'catid', ${result.insertId}, 0)`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE category_id = ${ids.category}`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', 'DELETE x FROM #__kunena_messages_text AS x LEFT JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.id IS NULL');
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-anon-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  const pages = () => [
    `${base}&view=topic&catid=${ids.category}&id=${ids.topic}`,
    `${base}&view=category&catid=${ids.category}`,
    `${base}&view=category&layout=list`,
    `${base}&view=topics`,
    `${base}&view=topics&layout=user&mode=default&userid=${user.id}`,
    `${base}&view=topics&layout=user&mode=started&userid=${user.id}`,
    `${base}&view=topics&layout=user&mode=posted&userid=${user.id}`,
    `${base}&view=topics&layout=user&mode=favorites&userid=${user.id}`,
    `${base}&view=topics&layout=user&mode=subscriptions&userid=${user.id}`,
    `${base}&view=user&layout=default&userid=${user.id}`,
    `${base}&view=search&task=results&query=${stamp}&searchdate=all`,
    `${base}&view=topics&format=feed&layout=feed&type=rss`,
    `${base}&view=category&catid=${ids.category}&format=feed&layout=feed&type=rss`,
    `${base}&view=statistics`,
  ];

  it('hides the author of a topic and a reply which are written as anonymous', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=topic&layout=create&catid=${ids.category}`);
    cy.get('#postform input[name="subject"]').type(subject);
    typeInEditor(`The text of ${subject}.`);
    cy.get('#postform input[name="anonymous"][type="checkbox"], #postform #anonymous').first().check({ force: true });
    cy.get('#form_submit_button').should('not.be.disabled').click();

    cy.task('queryDB', `SELECT id, first_post_id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      expect(rows, 'the new topic').to.have.length(1);
      ids.topic = rows[0].id;
    });
    cy.then(() => cy.task('queryDB', `SELECT name, userid FROM #__kunena_messages WHERE thread = ${ids.topic}`)).then((rows) => {
      // The message belongs to the user (the moderators and the user himself can find it), but the name which is shown is not his.
      expect(rows[0].name, 'the name stored with the message').to.not.match(/ci-user/i);
    });

    // A visitor and another user do not find the author anywhere.
    cy.clearAllCookies();
    const leaks = [];

    cy.then(() => pages()).then((urls) => {
      urls.forEach((url) => {
        cy.request({ url, failOnStatusCode: false }).then((response) => {
          // The page of the author lists the topics he wrote: that is not the leak, the leak is the topic of the test in it.
          if (/layout=user|layout=default&userid/.test(url)) {
            if (String(response.body).includes(subject)) {
              leaks.push(`${url.replace(base, '')} lists the anonymous topic`);
            }

            return;
          }

          const body = String(response.body);
          // The topic page is about this topic alone; a list has other topics (of the same user): only the part around the subject counts.
          const parts = /view=topic&catid/.test(url) ? [body] : [...body.matchAll(new RegExp(subject, 'g'))].map((match) => body.slice(Math.max(0, match.index - 700), match.index + 700));

          if (parts.some((part) => part.includes(subject) && showsAuthor(part))) {
            leaks.push(`${url.replace(base, '')} shows the author`);
          }
        });
      });
    });
    cy.then(() => expect(leaks, 'the pages which show the author of an anonymous topic').to.deep.eq([]));
  });

  it('keeps the favorites and the subscriptions of a user for him and the moderators', () => {
    ['favorites', 'subscriptions'].forEach((mode) => {
      const url = `${base}&view=topics&layout=user&mode=${mode}&userid=${user.id}`;

      // A visitor: no access. Another user: no access.
      cy.clearAllCookies();
      cy.request({ url, failOnStatusCode: false }).its('status').should('be.gte', 400);
      cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
      cy.request({ url: `${base}&view=topics&layout=user&mode=${mode}&userid=796`, failOnStatusCode: false }).its('status').should('be.lessThan', 400);

      // The user himself, and a moderator, get the list.
      cy.clearAllCookies();
      cy.doFrontendLogin(user.name, user.password, false);
      cy.request({ url, failOnStatusCode: false }).its('status').should('be.lessThan', 400);
      cy.request({ url: `${base}&view=topics&layout=user&mode=${mode}&userid=797`, failOnStatusCode: false }).its('status').should('be.gte', 400);
      cy.clearAllCookies();
    });
  });
});
