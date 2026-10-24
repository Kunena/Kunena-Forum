// The list of the topics of the users whom a user follows (view=topics&layout=user&mode=following) shows whom he follows: it is for
// the user himself (and the moderators), like his favourites and subscriptions. A visitor or another user gets no list.
describe('Kunena follow: the topics of the followed users are private', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const ids = {};
  const list = () => cy.request({ url: `${base}&view=topics&layout=user&mode=following&userid=${user.id}&sel=-1`, failOnStatusCode: false });

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress follow ${stamp}', 'cypress-follow-${stamp}', '', '', '', '', '', 'joomla.level', 1, 1, 1, 8, 1, 97, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-follow-${stamp}', 'catid', ${category.insertId}, 0)`);

      // A topic of the administrator, whom ci-user follows.
      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${category.insertId}, 'Followed ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      );
    });
    cy.task('queryDB', `INSERT INTO #__kunena_user_follow (user_id, followed_id, created_time) VALUES (${user.id}, 797, UNIX_TIMESTAMP())`);
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_user_follow WHERE user_id = ${user.id} AND followed_id = 797`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_read WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_user_categories WHERE category_id = ${ids.category}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-follow-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
  });

  it('shows the user the topics of the users whom he follows', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    list().then((response) => {
      expect(response.status).to.eq(200);
      expect(String(response.body)).to.include(`Followed ${stamp}`);
    });
  });

  it('shows a visitor nothing of it', () => {
    list().then((response) => {
      expect(response.status, 'the status').to.be.oneOf([401, 403]);
      expect(String(response.body)).to.not.include(`Followed ${stamp}`);
    });
  });

  it('shows the administrator (a moderator) the list', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    list().its('body').should('include', `Followed ${stamp}`);
  });
});
