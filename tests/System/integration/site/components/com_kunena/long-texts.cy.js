// The subject of a message and the reason of an edit are TINYTEXT columns: 255 bytes. A subject or a reason which is longer (a
// long one, or a short one in letters of more bytes, like Cyrillic) must not lose the post: it is saved, shortened.
describe('Kunena posting: texts longer than their column', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const long = 'ж'.repeat(200);

  const formToken = (url) => cy.request(url).then((page) => /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1]);

  before(() => {
    cy.kunena_setOption('floodProtection', '0');
  });

  afterEach(() => {
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE 'Cypress long ${stamp}%'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.kunena_fixUserCounters();
    cy.task('queryDB', "UPDATE #__kunena_categories c SET numTopics = (SELECT COUNT(*) FROM #__kunena_topics t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0), numPosts = (SELECT COALESCE(SUM(t.posts), 0) FROM #__kunena_topics t WHERE t.category_id = c.id AND t.hold = 0 AND t.moved_id = 0) WHERE c.id = 3");
  });

  it('saves a topic with a subject which is longer than its column, shortened', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    formToken(`${base}&view=topic&layout=create&catid=3`).then((token) => cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=post`,
      form: true,
      body: { catid: 3, subject: `Cypress long ${stamp} ${long}`, message: 'A topic with a long subject.', [token]: '1' },
      failOnStatusCode: false,
      followRedirect: false,
    })).its('status').should('be.lessThan', 500);

    cy.task('queryDB', `SELECT t.subject AS topic, m.subject AS message FROM #__kunena_topics AS t INNER JOIN #__kunena_messages AS m ON m.id = t.first_post_id WHERE t.subject LIKE 'Cypress long ${stamp}%'`).then((rows) => {
      expect(rows, 'the topic').to.have.length(1);
      expect(new TextEncoder().encode(rows[0].topic).length, 'the bytes of the subject of the topic').to.be.at.most(255);
      expect(new TextEncoder().encode(rows[0].message).length, 'the bytes of the subject of the message').to.be.at.most(255);
    });
  });

  it('saves an edit with a reason which is longer than its column, shortened', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    formToken(`${base}&view=topic&layout=create&catid=3`).then((token) => cy.request({
      method: 'POST',
      url: `${base}&view=topic&task=post`,
      form: true,
      body: { catid: 3, subject: `Cypress long ${stamp} edit`, message: 'A topic to edit.', [token]: '1' },
      followRedirect: false,
    }));

    cy.task('queryDB', `SELECT t.id, t.first_post_id FROM #__kunena_topics AS t WHERE t.subject = 'Cypress long ${stamp} edit'`).then((rows) => {
      const topic = rows[0];

      return formToken(`${base}&view=topic&layout=edit&catid=3&id=${topic.id}&mesid=${topic.first_post_id}`).then((token) => cy.request({
        method: 'POST',
        url: `${base}&view=topic&task=edit`,
        form: true,
        body: { catid: 3, id: topic.id, mesid: topic.first_post_id, subject: `Cypress long ${stamp} edit`, message: 'The text after the edit.', modified_reason: long, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      })).then((response) => {
        expect(response.status, 'the status of the edit').to.be.lessThan(500);

        return cy.task('queryDB', `SELECT m.modified_reason, x.message FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS x ON x.mesid = m.id WHERE m.id = ${topic.first_post_id}`);
      });
    }).then((rows) => {
      expect(rows[0].message, 'the text of the edit').to.eq('The text after the edit.');
      expect(new TextEncoder().encode(rows[0].modified_reason).length, 'the bytes of the reason').to.be.at.most(255);
    });
  });
});
