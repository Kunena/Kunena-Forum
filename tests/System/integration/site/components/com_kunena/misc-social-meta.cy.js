// The custom content page (view misc) had no Open Graph and Twitter Card tags, unlike the recent topics page (issue #7598). It
// has them now, with a title and a description which are never empty. The image depends on the configuration of the site: both
// image tags or neither.
describe('Kunena custom content page: the Open Graph and Twitter Card tags', () => {
  const meta = (body, attribute, name) => {
    const tag = new RegExp(`<meta ${attribute}="${name}" content="([^"]*)"`).exec(body);

    return tag ? tag[1] : null;
  };

  it('has the social tags in the head of the page', () => {
    cy.request({ url: '/index.php?option=com_kunena&view=misc', followRedirect: true }).then((page) => {
      const { body } = page;

      expect(body).to.contain('<meta property="og:type" content="website">');
      expect(body).to.contain('<meta name="twitter:card" content="summary">');

      expect(meta(body, 'property', 'og:url')).to.be.a('string').and.not.be.empty;
      expect(meta(body, 'property', 'og:title')).to.be.a('string').and.not.be.empty;
      expect(meta(body, 'property', 'og:description')).to.be.a('string').and.not.be.empty;
      expect(meta(body, 'name', 'twitter:title')).to.eq(meta(body, 'property', 'og:title'));
      expect(meta(body, 'name', 'twitter:description')).to.eq(meta(body, 'property', 'og:description'));

      const ogImage = meta(body, 'property', 'og:image');

      expect(meta(body, 'name', 'twitter:image')).to.eq(ogImage);

      if (ogImage !== null) {
        expect(ogImage).to.not.be.empty;
      }
    });
  });
});
