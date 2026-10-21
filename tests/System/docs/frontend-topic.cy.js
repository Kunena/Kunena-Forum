// Screenshots for docs/7.2: a topic, its messages and the tools of the moderators. Run it with cypress.docs.config.js.
import { addMessage, forum, lightScheme, open } from './helpers.mjs';

describe('Documentation: topics and messages', () => {
  const base = '/index.php?option=com_kunena&view=topic&layout=default&catid=3';
  const options = ['quickBan', 'enableMarkdown', 'enableHtml', 'linkPreview', 'autoEmbedSpotify', 'autoEmbedDeezer', 'enableTags'];
  const old = {};

  before(() => {
    options.forEach((name) => cy.kunena_setOption(name, '1').then((value) => {
      old[name] = value;
    }));
  });

  after(() => {
    options.forEach((name) => cy.kunena_setOption(name, old[name] ?? '0'));
  });

  beforeEach(() => {
    lightScheme();
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  it('the box to ban a user from a message', () => {
    open(`${base}&id=4`);

    // The message of the user (not of an administrator): the entry of the Moderate menu.
    cy.contains('.dropdown-menu a, .dropdown-menu button', /Ban user/i).closest('.btn-group').find('button.dropdown-toggle').click();
    cy.contains('.dropdown-menu.show a, .dropdown-menu.show button', /Ban user/i).click();
    cy.get('.modal.show').should('be.visible');
    forum('ban-box', '.modal.show .modal-content', { wait: 1200 });
  });

  it('the menu of a topic with the action Mark as solved', () => {
    open(`${base}&id=4`);

    cy.get('#topic-actions-toolbar .btn-group').first().find('a.dropdown-toggle').click();
    cy.get('#topic-actions-toolbar .dropdown-menu.show').should('be.visible');
    forum('topic-mark-as-solved', ['#topic-actions-toolbar', '#topic-actions-toolbar .dropdown-menu.show'], { padding: 10 });
  });

  it('a link in a message becomes a card', () => {
    addMessage(7, 797, 'ci-admin', 'Have a look at this page: https://en.wikipedia.org/wiki/Joomla', 'Re: Next community meetup');
    open(`${base}&id=7`);
    cy.wait(4000);
    forum('link-card', '.topic-item-messages');
  });

  it('a Spotify and a Deezer link become a player', () => {
    addMessage(5, 797, 'ci-admin', 'My favourite song: https://open.spotify.com/track/4cOdK2wGLETKBW3PvgPWqT\n\nAnd one on Deezer: https://www.deezer.com/track/3135556', 'Re: Share your favourite music');
    open(`${base}&id=5`);
    cy.wait(4000);
    forum('spotify-deezer-players', '.topic-item-messages');
  });

  it('a map with OpenStreetMap', () => {
    addMessage(7, 797, 'ci-admin', 'The meetup is here: [map]Amsterdam, Netherlands[/map]', 'Re: Next community meetup');
    open(`${base}&id=7`);
    cy.wait(4000);
    forum('map-openstreetmap', '.topic-item-messages');
  });

  it('a mention of a user', () => {
    addMessage(4, 797, 'ci-admin', 'Welcome to the team, [mention userid=798]ci-user[/mention]!', 'Re: Meet our new moderators');
    open(`${base}&id=4`);
    forum('mention-in-message', '.topic-item-messages');
  });

  it('a message in Markdown', () => {
    addMessage(8, 797, 'ci-admin', '**Idea:** a *dark mode*.\n\n- one list item\n- another list item\n\n> A quote from somebody\n\n`inline code`', 'Re: Feature ideas for the next version');
    open(`${base}&id=8`);
    forum('markdown-message', '.topic-item-messages');
  });
});
