// A quote with a name and nothing else ([quote="Tom & Jerry"], as the forum wrote them in K5.1/K5.2 and as a user may type it)
// showed the name escaped twice: "Tom &amp; Jerry wrote:".
describe('Kunena BBCode: the name of a quote', () => {
  const stamp = Date.now();
  const ids = {};

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (10, 9, 3, 'ci-admin', 797, '', 'Re: quote ${stamp}', UNIX_TIMESTAMP(), '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      ids.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '[quote="Tom & Jerry ${stamp}"]A quoted text ${stamp}[/quote]')`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${ids.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${ids.message}`);
  });

  it('shows the name once escaped', () => {
    cy.then(() => cy.request(`/index.php?option=com_kunena&view=topic&catid=3&id=9&mesid=${ids.message}`)).then((page) => {
      const body = String(page.body);

      expect(body, 'the quoted text').to.include(`A quoted text ${stamp}`);
      expect(body, 'the name of the quote').to.include(`Tom &amp; Jerry ${stamp}`);
      expect(body, 'a name escaped twice').not.to.include(`Tom &amp;amp; Jerry ${stamp}`);
    });
  });
});
