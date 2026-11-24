// The list of the files of a member (view=user&layout=attachments) took any number of the address as the length of its page:
// limit=0 or limit=100000 loaded every file of the member, with its message and topic, for any visitor. The length is bounded
// as in the other lists now: another value is the default length (30).
describe('Kunena profile: the length of a page of the files of a member', () => {
  const stamp = Date.now();
  const name = `cyatt${stamp}`;
  const user = 798;
  const old = {};

  before(() => {
    cy.kunena_setOption('showImgFilesManageProfile', '1').then((value) => {
      old.files = value ?? null;
    });
    cy.kunena_setOption('pubProfile', '1').then((value) => {
      old.pubProfile = value ?? null;
    });
  });

  after(() => {
    cy.kunena_setOption('showImgFilesManageProfile', old.files);
    cy.kunena_setOption('pubProfile', old.pubProfile);
  });

  beforeEach(() => {
    // 31 files of ci-user in the first message of topic 9 (General Discussion, readable by everybody).
    cy.task('queryDB', 'SELECT first_post_id AS id FROM #__kunena_topics WHERE id = 9').then((topic) => {
      const rows = Array.from({ length: 31 }, (_, i) => `(${topic[0].id}, ${user}, 0, '', 1, 'media/kunena/attachments/${name}', 'text/plain', '${name}-${i}.txt', '${name}-${i}.txt', '', 0, 1)`).join(', ');

      cy.task('queryDB', `INSERT INTO #__kunena_attachments (mesid, userid, protected, hash, size, folder, filetype, filename, filename_real, caption, inline, status) VALUES ${rows}`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_attachments WHERE folder = 'media/kunena/attachments/${name}'`);
  });

  [0, 100000].forEach((limit) => {
    it(`shows one page of 30 files for limit=${limit}`, () => {
      cy.request(`/index.php?option=com_kunena&view=user&layout=attachments&userid=${user}&limit=${limit}`).its('body').then((body) => {
        const shown = new Set(String(body).match(new RegExp(`${name}-\\d+\\.txt`, 'g')) ?? []);

        expect(shown.size, 'the files on the page').to.eq(30);
      });
    });
  });
});
