// The data tables of Aurelia (statistics, user list, attachments of a user, ban history, ban manager, announcements and
// drafts) tell a screen reader what they hold: a hidden caption as their first child, scope="col" on every header cell, and a
// text (visible or hidden) in every header cell. The pages are read as the administrator.
describe('Kunena data tables: captions and column headers', () => {
  const base = '/index.php?option=com_kunena';
  const admin = { id: 797 };
  const user = { id: 798 };
  // The template Aurelia (the site may run another one), the drafts, and the top lists of the statistics (posters, profiles and
  // topics), which are only shown when their options are on.
  const options = { template: 'aurelia', enableDrafts: '1', showPopUserStats: '1', showPopSubjectStats: '1' };
  const old = {};

  const page = (query) => cy.request(`${base}&${query}`).then((response) => {
    expect(response.status, `the status of ${query}`).to.eq(200);

    return Cypress.$(Cypress.$.parseHTML(String(response.body)));
  });

  const find = ($page, selector) => $page.find(selector).add($page.filter(selector));

  const checkTable = (table, name) => {
    const $table = Cypress.$(table);
    const $first = $table.children().first();

    expect($first.prop('tagName'), `the first child of the ${name} table`).to.eq('CAPTION');
    expect($first.hasClass('visually-hidden'), `the caption of the ${name} table is hidden`).to.eq(true);
    expect($first.text().trim(), `the text of the caption of the ${name} table`).to.not.eq('');

    const $headers = $table.find('thead th');

    expect($headers.length, `the header cells of the ${name} table`).to.be.greaterThan(0);
    $headers.each((index, th) => {
      // A cell with only an icon link (the first cell of the user list) is named by the aria-label of that link.
      const label = Cypress.$(th).text().trim() || Cypress.$(th).find('[aria-label]').attr('aria-label') || '';

      expect(th.getAttribute('scope'), `the scope of header cell ${index + 1} of the ${name} table`).to.eq('col');
      expect(label.trim(), `the text of header cell ${index + 1} of the ${name} table`).to.not.eq('');
    });

    return $first.text().trim();
  };

  const checkOne = (query, selector, name) => page(query).then(($page) => {
    const $tables = find($page, selector);

    expect($tables.length, `the ${name} table`).to.eq(1);
    checkTable($tables[0], name);
  });

  before(() => {
    Object.entries(options).forEach(([name, value]) => {
      cy.kunena_setOption(name, value).then((before) => {
        old[name] = before;
      });
    });
  });

  beforeEach(() => {
    // A ban of the test user for an hour (a ban of the forum only), for the ban manager and the ban history.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_users_banned (userid, ip, blocked, expiration, created_by, created_time, reason_private, reason_public, modified_by, modified_time, comments, params) VALUES (${user.id}, '', 0, DATE_ADD(NOW(), INTERVAL 1 HOUR), ${admin.id}, NOW(), 'private reason', 'public reason', 0, '1000-01-01 00:00:00', '', '')`,
    );
    // A draft of the administrator, for the list of the drafts.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_drafts (user_id, catid, parent_id, topic_id, message_id, subject, message, fields, created_time, modified_time) VALUES (${admin.id}, 3, 0, 0, 0, 'A draft of the data tables test', 'The text of the draft.', '', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())`,
    );
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  });

  afterEach(() => {
    cy.task('queryDB', `UPDATE #__kunena_users SET banned = '1000-01-01 00:00:00' WHERE userid = ${user.id}`);
  });

  after(() => {
    Object.keys(options).forEach((name) => {
      cy.then(() => cy.kunena_setOption(name, old[name] ?? null));
    });
    cy.task('queryDB', `DELETE FROM #__kunena_drafts WHERE user_id = ${admin.id} AND subject = 'A draft of the data tables test'`);
  });

  it('gives every top list of the statistics its own caption', () => {
    page('view=statistics').then(($page) => {
      const $tables = find($page, '.statistics > table');

      expect($tables.length, 'the tables of the statistics').to.be.greaterThan(0);

      const captions = $tables.toArray().map((table, index) => checkTable(table, `statistics ${index + 1}`));

      expect(new Set(captions).size, 'no two tables share a caption').to.eq(captions.length);
    });
  });

  it('labels the table of the user list', () => {
    checkOne('view=user&layout=list', '#kuserlist-form table', 'user list');
  });

  it('labels the table of the attachments of a user', () => {
    checkOne(`view=user&layout=attachments&userid=${admin.id}`, 'table:has(input[name="checkall-toggle"])', 'attachments');
  });

  it('labels the table of the ban history in a profile', () => {
    checkOne(`view=user&userid=${user.id}`, '#banhistory table', 'ban history');
  });

  it('labels the table of the ban manager', () => {
    checkOne('view=user&layout=banmanager', 'h3 + table', 'ban manager');
  });

  it('labels the table of the announcements', () => {
    checkOne('view=announcement&layout=listing', 'table:has(input[name="toggle"])', 'announcements');
  });

  it('labels the table of the drafts', () => {
    checkOne('view=topic&layout=drafts', 'h1 + table', 'drafts');
  });
});
