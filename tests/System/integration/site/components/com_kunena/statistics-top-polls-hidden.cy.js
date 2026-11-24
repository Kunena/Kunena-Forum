// The most voted polls of the statistics page were cut to their number (popPollsCount) before the topics which the visitor may
// not read were left out: a poll in a category hidden from him took a place, and the list showed fewer polls, or none. The polls
// which he may read are counted now.
describe('Kunena statistics: the most voted polls of the categories hidden from the visitor', () => {
  const stamp = Date.now();
  const ids = { topics: [], polls: [] };
  const old = {};

  const setOption = (name, value) => cy.kunena_setOption(name, value).then((previous) => {
    if (!(name in old)) {
      old[name] = previous;
    }
  });

  // A topic of ci-admin with a poll of one option with these votes.
  const addPoll = (catid, subject, votes) => cy.task(
    'queryDB',
    `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (${catid}, '${subject}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
  ).then((topic) => {
    ids.topics.push(topic.insertId);

    return cy.task('queryDB', `INSERT INTO #__kunena_polls (title, threadid) VALUES ('Poll ${stamp}', ${topic.insertId})`);
  }).then((poll) => {
    ids.polls.push(poll.insertId);
    cy.task('queryDB', `UPDATE #__kunena_topics SET poll_id = ${poll.insertId} WHERE id = ${ids.topics[ids.topics.length - 1]}`);
    cy.task('queryDB', `INSERT INTO #__kunena_polls_options (pollid, text, votes) VALUES (${poll.insertId}, 'Yes', ${votes})`);
  });

  beforeEach(() => {
    setOption('showStats', '1');
    setOption('statsLinkAllowed', '1');
    setOption('showPopPollStats', '1');
    setOption('popPollsCount', '1');
    // A category for the Special access level only (not for guests).
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress hidden polls ${stamp}', 'cypress-hidden-polls-${stamp}', '', '', '', '', '', 'joomla.level', 3, 1, 1, 8, 1, 97, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      addPoll(category.insertId, `Cypress hidden poll ${stamp}`, 100);
      addPoll(3, `Cypress open poll ${stamp}`, 1);
    });
  });

  afterEach(() => {
    Object.entries(old).forEach(([name, value]) => cy.kunena_setOption(name, value === undefined ? null : value));
    Object.keys(old).forEach((name) => delete old[name]);
    cy.then(() => {
      ids.polls.forEach((id) => {
        cy.task('queryDB', `DELETE FROM #__kunena_polls_options WHERE pollid = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_polls WHERE id = ${id}`);
      });
      ids.topics.forEach((id) => cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`));
      ids.polls = [];
      ids.topics = [];
      cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    });
  });

  it('shows a guest the most voted poll which he may read', () => {
    cy.request('/index.php?option=com_kunena&view=statistics').its('body').then((body) => {
      const text = String(body);
      const start = text.indexOf('Most Popular Polls');
      // The block of the polls ends with the next block of the page (or the page).
      const end = text.indexOf('card-header', start + 1);
      const polls = start < 0 ? '' : text.slice(start, end < 0 ? undefined : end);

      expect(text, 'the poll of the hidden category').not.to.contain(`Cypress hidden poll ${stamp}`);
      expect(start, 'the block of the most voted polls').to.be.greaterThan(-1);
      expect(polls, 'the open poll in it').to.contain(`Cypress open poll ${stamp}`);
    });
  });
});
