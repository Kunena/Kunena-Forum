// The drop-down of the avatar gallery on the profile edit page asked a raw view which does not exist (404): it never showed the
// pictures of a gallery. The list is now the JSON task galleryimages, for members, and the save accepts the values it gives and
// no other path.
describe('Kunena avatars: the gallery lists and saves its pictures', () => {
  const base = '/index.php?option=com_kunena';
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const gallery = 'media/kunena/avatars/gallery';
  const old = {};

  const avatar = () => cy.task('queryDB', `SELECT avatar FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => rows[0].avatar);
  const tokenOf = (url) => cy.request(url).then((page) => /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1]);
  const list = (token, name) => cy.request({
    url: `${base}&view=user&task=galleryimages&format=json&${token}=1`,
    qs: { gallery_name: name },
    failOnStatusCode: false,
  });
  const save = (value) => tokenOf(`${base}&view=user&layout=edit`).then((token) => cy.request({
    method: 'POST',
    url: `${base}&view=user&task=save`,
    form: true,
    body: { userid: user.id, avatar_gallery: value, [token]: '1' },
    failOnStatusCode: false,
    followRedirect: false,
  }));

  beforeEach(() => {
    cy.task('copyRelativeFile', { source: 'media/kunena/avatars/nophoto.png', destination: `${gallery}/ci-root.png` });
    cy.task('writeRelativeFile', { path: `${gallery}/ci-gallery-a/index.html`, content: '<html></html>', mode: 0o644 });
    cy.task('copyRelativeFile', { source: 'media/kunena/avatars/nophoto.png', destination: `${gallery}/ci-gallery-a/a1.png` });
    cy.task('writeRelativeFile', { path: `${gallery}/ci-gallery-b/index.html`, content: '<html></html>', mode: 0o644 });
    cy.task('copyRelativeFile', { source: 'media/kunena/avatars/nophoto.png', destination: `${gallery}/ci-gallery-b/b1.png` });
    cy.kunena_setOption('allowAvatarGallery', '1').then((value) => {
      old.setting = value;
    });
    avatar().then((value) => {
      old.avatar = value;
    });
  });

  afterEach(() => {
    cy.task('deleteRelativePath', `${gallery}/ci-root.png`);
    cy.task('deleteRelativePath', `${gallery}/ci-gallery-a`);
    cy.task('deleteRelativePath', `${gallery}/ci-gallery-b`);
    cy.then(() => cy.kunena_setOption('allowAvatarGallery', old.setting ?? null));
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET avatar = ${old.avatar === null ? 'NULL' : `'${old.avatar}'`} WHERE userid = ${user.id}`));
  });

  it('gives a guest no pictures, and answers 403 without a token', () => {
    tokenOf('/index.php?option=com_users&view=login').then((token) => {
      list(token, 'ci-gallery-a').then((response) => {
        expect(response.status).to.eq(200);
        expect(response.body).to.deep.eq([]);
      });
    });
    list('x', 'ci-gallery-a').its('status').should('eq', 403);
  });

  it('lists the pictures of a gallery for a member', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    tokenOf(`${base}&view=user&layout=edit`).then((token) => {
      list(token, 'ci-gallery-a').then((response) => {
        expect(response.body).to.have.length(1);
        expect(response.body[0].value).to.eq('gallery/ci-gallery-a/a1.png');
        expect(response.body[0].url).to.match(/\/media\/kunena\/avatars\/gallery\/ci-gallery-a\/a1\.png$/);
        expect(JSON.stringify(response.body), 'index.html').to.not.include('index.html');
      });
      list(token, '').then((response) => {
        expect(response.body.map((item) => item.value)).to.include('gallery/ci-root.png');
      });
      list(token, '..').then((response) => {
        expect(response.body).to.deep.eq([]);
      });
    });
  });

  it('shows the pictures of the chosen gallery on the avatar tab and saves the chosen one', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.visit(`${base}&view=user&layout=edit`);
    cy.get('a[href="#editavatar"]').click();

    cy.get('#avatar_gallery_select').select('ci-gallery-b');
    cy.get('#gallery_list > li.col-md-2 input[type="radio"]').should('have.length', 1)
      .and('have.value', 'gallery/ci-gallery-b/b1.png');
    cy.get('li.span2').should('not.exist');

    cy.get('#avatar_gallery_select').select('Default Gallery');
    cy.get('#gallery_list > li.col-md-2 input[value="gallery/ci-root.png"]').should('exist');

    cy.get('#avatar_gallery_select').select('ci-gallery-b');
    cy.get('#gallery_list input[value="gallery/ci-gallery-b/b1.png"]').check({ force: true });
    cy.intercept({ method: 'POST' }).as('save');
    cy.get('#kuserform button[type="submit"]').first().click();
    cy.wait('@save');
    avatar().should('eq', 'gallery/ci-gallery-b/b1.png');
  });

  it('does not save a path which is no picture of a gallery', () => {
    cy.task('queryDB', `UPDATE #__kunena_users SET avatar = '' WHERE userid = ${user.id}`);
    cy.doFrontendLogin(user.name, user.password, false);
    save('gallery//ci-root.png');
    avatar().should('eq', '');
    save('gallery/../ci-root.png');
    avatar().should('eq', '');
    save('gallery/ci-root.png');
    avatar().should('eq', 'gallery/ci-root.png');
  });
});
