// The list of the users of a topic (for the @ of the editor): for a user who may read the topic, also when he did not write in it; not
// for a guest; not for a topic which the user may not read, even when he was subscribed to it before the category was closed.
describe('Kunena mentions: the users of a topic', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};

  const list = (topic, token) => cy.request({ url: `${base}&view=user&task=getusersmentions&format=json&topicid=${topic}&${token}=1`, failOnStatusCode: false });
  const tokenOf = () => cy.request(`${base}&view=home`).then((page) => /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1]);
  const usersIn = (body) => {
    const data = typeof body === 'string' ? JSON.parse(body) : body;

    return (Array.isArray(data) ? data : []).map((row) => Number(row.userid));
  };

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress mentions ${stamp}', 'cypress-mentions-${stamp}', '', '', '', '', '', 'joomla.level', 3, 3, 1, 8, 1, 90, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-mentions-${stamp}', 'catid', ${category.insertId}, 0)`);

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Cypress mentions ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      );
    }).then((topic) => {
      ids.restricted = topic.insertId;

      // A subscription from the time when the user could read the category.
      return cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${user.id}, ${topic.insertId}, ${ids.category}, 0, 0, 0, 0, 1, '')`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${ids.restricted}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.restricted}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-mentions-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('gives the users of a topic to a user who may read it, also when he did not write in it', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    tokenOf().then((token) => {
      // Topic 2 is of the user 796; the user of the test never wrote in it.
      list(2, token).then((response) => {
        expect(response.status, 'the status for a topic he did not write in').to.eq(200);
        expect(usersIn(response.body), 'the users of the topic').to.include(796);
      });
      list(9, token).then((response) => {
        expect(response.status).to.eq(200);
        expect(usersIn(response.body)).to.include(user.id);
      });
    });
  });

  it('gives nothing to a guest, and nothing of a topic which the user may not read', () => {
    tokenOf().then((token) => list(9, token).its('status').should('eq', 403));

    cy.doFrontendLogin(user.name, user.password, false);
    tokenOf().then((token) => {
      list(ids.restricted, token).its('status').should('eq', 403);
      list(99999999, token).its('status').should('eq', 403);
    });
  });
});
