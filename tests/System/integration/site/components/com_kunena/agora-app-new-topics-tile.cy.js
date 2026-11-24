// The layout "App" of Agora starts the index with a tile "New topics in the last 24 hours". It counted every topic with a post of
// the last 24 hours: an old topic which got a reply was counted as new. It counts the topics started in the last 24 hours now.
describe('Kunena Agora layout App: the tile of the new topics', () => {
  const stamp = Date.now();
  const params = () => `${Cypress.expose('cmsPath')}/components/com_kunena/template/agora/config/params.ini`;
  const ids = {};
  const old = {};

  const tile = () => cy.request('/index.php?option=com_kunena&view=category&layout=list').its('body').then((body) => {
    const match = /agora-home-tile-main[^>]*>\s*<span class="agora-home-number">(\d+)<\/span>/.exec(String(body));

    expect(match, 'the tile').not.to.equal(null);

    return Number(match[1]);
  });

  before(() => {
    cy.kunena_setOption('template', 'agora').then((value) => {
      old.template = value ?? null;
    });
    cy.readFile(params()).then((text) => {
      old.params = text;

      return cy.writeFile(params(), /^layoutSet=.*$/m.test(text) ? text.replace(/^layoutSet=.*$/m, 'layoutSet="app"') : `${text.trimEnd()}\nlayoutSet="app"\n`);
    });
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  after(() => {
    cy.then(() => cy.writeFile(params(), old.params));
    cy.kunena_setOption('template', old.template);
    cy.task('deleteRelativePath', 'cache/com_kunena_template');
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  it('does not count an old topic with a new reply', () => {
    tile().then((before) => {
      // A topic of 30 days ago in General Discussion, with its last post of now.
      cy.task(
        'queryDB',
        `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress old topic ${stamp}', 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, UNIX_TIMESTAMP() - 30 * 86400, 797, 'x', '', 0, UNIX_TIMESTAMP(), 797, 'x', '', 0, '')`,
      ).then((topic) => {
        ids.topic = topic.insertId;
      });
      tile().should('eq', before);
    });
  });
});
