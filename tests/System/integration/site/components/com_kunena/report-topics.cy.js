// The reports plugin: a report of a post becomes a topic in the category of the reports (with the report and a quote of the post),
// and a second report of the same post, by another user, is a reply in that topic and counts the reporters.
describe('Kunena reports: the topic of a report', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const reported = { topic: 2, message: 2, category: 4 };
  const ids = {};
  const old = {};

  const report = (reason) => cy.request(`${base}&view=topic&layout=report&catid=${reported.category}&id=${reported.topic}&mesid=${reported.message}`).then((page) => {
    const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

    return cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=report`,
      form: true,
      body: { catid: reported.category, id: reported.topic, mesid: reported.message, reason, text: `The text of the report ${stamp}`, [token]: '1' },
      failOnStatusCode: false,
      followRedirect: false,
    });
  });

  before(() => {
    cy.kunena_setOption('reportMsg', '1').then((value) => {
      old.reportMsg = value;
    });
    cy.task('queryDB', "SELECT params FROM #__extensions WHERE folder = 'kunena' AND element = 'reports'").then((rows) => {
      old.params = rows[0].params;
    });
  });

  after(() => {
    cy.kunena_setOption('reportMsg', old.reportMsg ?? '1');
    cy.then(() => cy.task('queryDB', `UPDATE #__extensions SET params = '${old.params}' WHERE folder = 'kunena' AND element = 'reports'`));
  });

  beforeEach(() => {
    // A category for the reports, which only the moderators may read.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_categories (parentid, name, alias, icon, class_sfx, description, headerdesc, topictemplate, accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse, ordering, published, params, iconset) VALUES (1, 'Cypress reports ${stamp}', 'cypress-reports-${stamp}', '', '', '', '', '', 'joomla.level', 3, 3, 1, 8, 1, 90, 1, '{}', 'default')`,
    ).then((category) => {
      ids.category = category.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ('cypress-reports-${stamp}', 'catid', ${category.insertId}, 0)`);
      cy.task('queryDB', `UPDATE #__extensions SET params = '{"category":"${category.insertId}","holdThreshold":"0"}' WHERE folder = 'kunena' AND element = 'reports'`);
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
    cy.task('queryDB', `DELETE FROM #__kunena_reports WHERE message_id = ${reported.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_aliases WHERE alias = 'cypress-reports-${stamp}'`);
    cy.task('queryDB', `DELETE FROM #__kunena_categories WHERE id = ${ids.category}`);
    cy.kunena_fixUserCounters();
  });

  it('makes a topic of the first report, and a reply of the report of a second user', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    report('Spam').its('status').should('be.lessThan', 500);

    cy.task('queryDB', `SELECT topic_id, reports FROM #__kunena_reports WHERE message_id = ${reported.message}`).then((rows) => {
      expect(rows, 'the report').to.have.length(1);
      ids.reportTopic = Number(rows[0].topic_id);
    });
    cy.then(() => cy.task('queryDB', `SELECT category_id, posts FROM #__kunena_topics WHERE id = ${ids.reportTopic}`)).then((rows) => {
      expect(Number(rows[0].category_id), 'the category of the report').to.eq(ids.category);
      expect(Number(rows[0].posts), 'the posts of the report topic').to.eq(1);
    });
    cy.then(() => cy.task('queryDB', `SELECT x.message FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS x ON x.mesid = m.id WHERE m.thread = ${ids.reportTopic} ORDER BY m.id LIMIT 1`)).then((rows) => {
      expect(rows[0].message, 'the text of the report').to.include(`The text of the report ${stamp}`);
      expect(rows[0].message, 'the quote of the reported post').to.include(`post=${reported.message}`);
    });

    cy.clearAllCookies();
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    report('Off topic').its('status').should('be.lessThan', 500);

    cy.task('queryDB', `SELECT topic_id, reports, reporters FROM #__kunena_reports WHERE message_id = ${reported.message}`).then((rows) => {
      expect(Number(rows[0].topic_id), 'the same topic').to.eq(ids.reportTopic);
      expect(JSON.parse(rows[0].reporters).map(Number).sort(), 'the reporters').to.deep.eq([797, 798]);
    });
    cy.then(() => cy.task('queryDB', `SELECT COUNT(*) AS total FROM #__kunena_messages WHERE thread = ${ids.reportTopic}`)).then((rows) => {
      expect(Number(rows[0].total), 'the report and the reply').to.eq(2);
    });
  });
});
