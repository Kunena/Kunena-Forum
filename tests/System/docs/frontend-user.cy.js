// Screenshots for docs/7.2: the forum as a registered user (the user ci-user, which the specs of the tests make). Run it with
// cypress.docs.config.js. What a test needs in the database is made by the test and removed again after it.
import { addMessage, forum, hideToolbar, lightScheme, open } from './helpers.mjs';

describe('Documentation: the forum as a user', () => {
  const base = '/index.php?option=com_kunena';
  const options = ['enableDrafts', 'enableTags', 'enableFollow', 'enableIgnore', 'popupNotifications'];
  const old = {};
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const admin = 797;
  const tabs = '.nav-tabs a, .nav-tabs button, joomla-tab button';

  before(() => {
    options.forEach((name) => cy.kunena_setOption(name, '1').then((value) => {
      old[name] = value;
    }));
    cy.kunena_setOption('badges', {
      badges0: { title: 'Staff', color: '#dc3545', usergroup: '8', rank: '' },
      badges1: { title: 'Member', color: '#198754', usergroup: '2', rank: '' },
    }).then((value) => {
      old.badges = value;
    });
  });

  after(() => {
    options.forEach((name) => cy.kunena_setOption(name, old[name] ?? '0'));
    cy.kunena_setOption('badges', old.badges ?? null);
  });

  beforeEach(() => {
    lightScheme();
    cy.doFrontendLogin(user.name, user.password);
  });

  // The tables of the follows, the ignores and the tags have no id: the task of the database can not remove their rows.
  afterEach(() => {
    cy.task('queryDB', 'DELETE FROM #__kunena_user_follow');
    cy.task('queryDB', 'DELETE FROM #__kunena_user_ignore');
    cy.task('queryDB', "DELETE FROM #__contentitem_tag_map WHERE type_alias = 'com_kunena.topic'");
  });

  const follow = () => cy.task('queryDB', `INSERT INTO #__kunena_user_follow (user_id, followed_id, created_time) VALUES (${user.id}, ${admin}, UNIX_TIMESTAMP())`);
  const ignore = () => cy.task('queryDB', `INSERT INTO #__kunena_user_ignore (user_id, ignored_id, created_time) VALUES (${user.id}, ${admin}, UNIX_TIMESTAMP())`);
  const draft = (subject, text, age) => cy.task('queryDB', `INSERT INTO #__kunena_drafts (user_id, catid, subject, message, fields, created_time, modified_time) VALUES (${user.id}, 3, '${subject}', '${text}', '{}', UNIX_TIMESTAMP() - ${age}, UNIX_TIMESTAMP() - ${age})`);

  it('the buttons of the editor with Save draft', () => {
    open(`${base}&view=topic&layout=create&catid=3`);
    cy.get('#form_submit_button').parent().invoke('attr', 'id', 'doc-editor-buttons');
    forum('drafts-save-button', '#doc-editor-buttons');
  });

  it('the page with the drafts of the user', () => {
    draft('An idea for the next meetup', 'Maybe we can meet in a park when the weather is good.', 3600);
    draft('Which camera do you use?', 'I am looking for a small camera for travelling.', 86400);
    open(`${base}&view=topic&layout=drafts`);
    forum('drafts-page', '#kunena');
  });

  it('a popup for a user who is mentioned', () => {
    addMessage(4, admin, 'ci-admin', 'Welcome to the team, [mention userid=798]ci-user[/mention]!', 'Re: Meet our new moderators');
    open(`${base}&view=category&layout=list`);
    // The first request of the script gives the cursor to the browser: it is set to the start after that.
    cy.wait(2000);
    cy.window().then((win) => {
      win.localStorage.setItem(`kunena.notify.${win.Joomla.getOptions('com_kunena.notifications').userid}`, '1');
    });
    cy.reload();
    hideToolbar();
    cy.get('.kunena-notify-toast').should('be.visible');
    forum('popup-notification', '.kunena-notify-toast', { padding: 12, wait: 1500 });
  });

  it('the profile of a user who follows another user', () => {
    follow();
    open(`${base}&view=user&layout=default`);
    cy.contains(tabs, /^Following/).filter(':visible').first().click();
    forum('follow-tab', '#kunena');
  });

  it('the profile with the tab of the ignored users', () => {
    ignore();
    open(`${base}&view=user&layout=default`);
    cy.contains(tabs, /Ignored users/).filter(':visible').first().click();
    forum('ignored-users-tab', '#kunena');
  });

  it('the messages of an ignored user are collapsed in a topic', () => {
    ignore();
    addMessage(9, admin, 'ci-admin', 'I use the light colours too, they are easy on the eyes.', 'Re: Which template do you use?');
    open(`${base}&view=topic&layout=default&catid=3&id=9`);
    forum('ignored-message-collapsed', '.topic-item-messages');
  });

  it('the marker of a followed user in the list of topics', () => {
    follow();
    open(`${base}&view=topics&layout=default&mode=replies`);
    forum('follow-marker-in-lists', '#kunena', { maxHeight: 1135 });
  });

  it('tags on the topics', () => {
    [[5, 3], [7, 4], [4, 2]].forEach(([topic, tag]) => {
      cy.task('queryDB', `INSERT INTO #__contentitem_tag_map (type_alias, core_content_id, content_item_id, tag_id, tag_date, type_id) VALUES ('com_kunena.topic', 0, ${topic}, ${tag}, NOW(), 0)`);
    });
    open(`${base}&view=topics&layout=default&mode=replies`);
    forum('tags-in-lists', '#kunena', { maxHeight: 1135 });
  });

  it('badges next to the name of a user', () => {
    open(`${base}&view=topic&layout=default&catid=3&id=4`);
    forum('badges-in-posts', '.topic-item-messages');
  });

  it('the profile of the user with the request to delete the account', () => {
    open(`${base}&view=user&layout=edit`);
    cy.contains(tabs, /Forum Settings/i).filter(':visible').first().click();
    forum('request-account-deletion', '#kunena');
  });

  it('the list of users who can be mentioned while the name is typed', () => {
    open(`${base}&view=topic&layout=create&catid=3`);
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty')
      .then(($body) => cy.wrap($body).click().type('Hello @ci', { parseSpecialCharSequences: false }));
    cy.wait(2000);
    forum('mention-suggestions', ['#postform .sceditor-container', '.kmention-menu'], { wait: 500 });
  });

  it('the inbox with private messages and notifications', () => {
    cy.task('queryDB', `INSERT INTO #__kunena_inbox (user_id, sender_id, type, conversation_id, subject, body, topic_id, message_id, created_time) VALUES (${user.id}, ${admin}, 'message', 1, 'Welcome to the forum', 'Hello! Tell me if you have a question about the forum.', 0, 0, UNIX_TIMESTAMP() - 1800)`);
    cy.task('queryDB', `INSERT INTO #__kunena_inbox (user_id, sender_id, type, conversation_id, subject, body, topic_id, message_id, created_time) VALUES (${user.id}, ${admin}, 'mention', 0, 'Meet our new moderators', 'ci-admin mentioned you in a message.', 4, 0, UNIX_TIMESTAMP() - 600)`);
    open(`${base}&view=user&layout=default`);
    cy.contains(tabs, /Inbox/i).filter(':visible').first().click();
    forum('inbox-tab', '#kunena');
  });
});
