// Screenshots for docs/7.2: the forum as the administrator of the test site sees it. Run it with cypress.docs.config.js.
import { forum, lightScheme, open } from './helpers.mjs';

describe('Documentation: the forum as an administrator', () => {
  const base = '/index.php?option=com_kunena';
  const options = ['enableDrafts', 'enableTags', 'enableFollow', 'enableIgnore', 'quickBan', 'logModeration', 'popupNotifications'];
  const before_ = {};

  before(() => {
    options.forEach((name) => cy.kunena_setOption(name, '1').then((old) => {
      before_[name] = old;
    }));
  });

  after(() => {
    options.forEach((name) => cy.kunena_setOption(name, before_[name] ?? '0'));
  });

  beforeEach(() => {
    lightScheme();
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  it('the forum index with the buttons for a new category', () => {
    open(`${base}&view=category&layout=list`);
    forum('forum-index');
  });

  it('the filter bar of the lists of topics', () => {
    open(`${base}&view=topics&layout=default&mode=replies`);
    forum('topic-list-filter-bar');
  });

  it('the moderation log', () => {
    open(`${base}&view=topic&layout=modlog`);
    forum('moderation-log');
  });

  it('the form of a new category', () => {
    open(`${base}&view=category&layout=create&parentid=1`);
    forum('category-create-form');
  });

  it('the new topic with the tags field and the similar topics', () => {
    open(`${base}&view=topic&layout=create&catid=3`);
    cy.get('#postform input[name="subject"]').type('Welcome to the');
    cy.wait(1500);
    forum('new-topic-similar-topics');
  });

  it('the announcements with the star of the default announcement', () => {
    cy.kunena_createAnnouncement({ title: 'Maintenance on Sunday' });
    cy.kunena_createAnnouncement({ title: 'New rules for the forum' });
    open(`${base}&view=announcement&layout=listing`);
    forum('announcements-default-star');
  });

  it('the profile of another user with the buttons to follow and to ignore', () => {
    open(`${base}&view=user&layout=default&userid=798`);
    forum('profile-follow-ignore');
  });

  it('the page of the drafts', () => {
    open(`${base}&view=topic&layout=drafts`);
    forum('drafts-page');
  });
});
