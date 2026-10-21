// Screenshots for docs/7.2: the administration of Kunena and its plugins, as the administrator of the test site. Run it with
// cypress.docs.config.js.
import { forum, lightScheme, open } from './helpers.mjs';

describe('Documentation: the administration', () => {
  const base = '/administrator/index.php?option=com_kunena';
  const plugin = (element, folder = 'kunena') => cy.task('queryDB', `SELECT extension_id FROM #__extensions WHERE type = 'plugin' AND folder = '${folder}' AND element = '${element}'`)
    .then((rows) => rows[0].extension_id);

  let badgesBefore;

  before(() => {
    // Two badges: one for the administrators and one for every registered user.
    cy.kunena_setOption('badges', {
      badges0: { title: 'Staff', color: '#dc3545', usergroup: '8', rank: '' },
      badges1: { title: 'Member', color: '#198754', usergroup: '2', rank: '' },
    }).then((old) => {
      badgesBefore = old;
    });
  });

  after(() => {
    cy.kunena_setOption('badges', badgesBefore ?? null);
  });

  beforeEach(() => {
    lightScheme();
    cy.doAdministratorLogin();
  });

  const tab = (name) => cy.contains('joomla-tab button[role="tab"], .nav-tabs a, .nav-tabs button', name).filter(':visible').first().click();

  it('the social websites', () => {
    open(`${base}&view=socials`);
    forum('admin-social-websites', 'main', { maxHeight: 620 });
  });

  it('the badges', () => {
    open(`${base}&view=badges`);
    forum('admin-badges', 'main', { maxHeight: 620 });
  });

  it('a rank with a Joomla user group', () => {
    open(`${base}&view=rank&layout=edit`);
    forum('admin-rank-user-group', 'main');
  });

  it('the form of a category: name, alias and icon image', () => {
    open(`${base}&view=category&layout=edit&catid=3`);
    forum('admin-category-general', 'main');
  });

  it('the form of a category: the settings of the category', () => {
    open(`${base}&view=category&layout=edit&catid=3`);
    tab('Category Settings');
    forum('admin-category-settings', 'main');
  });

  it('the options of the template: features', () => {
    open(`${base}&view=template&layout=edit&name=aurelia`);
    tab('Features');
    forum('admin-template-features', 'main');
  });

  ['reports', 'inbox', 'acl', 'geoip'].forEach((element) => {
    it(`the plugin ${element}`, () => {
      plugin(element).then((id) => {
        open(`/administrator/index.php?option=com_plugins&task=plugin.edit&extension_id=${id}`);
        forum(`admin-plugin-${element}`, 'main', { maxHeight: 900 });
      });
    });
  });

  it('the system plugin with the choice of the content plugins', () => {
    plugin('kunena', 'system').then((id) => {
      open(`/administrator/index.php?option=com_plugins&task=plugin.edit&extension_id=${id}`);
      forum('admin-plugin-system', 'main', { maxHeight: 900 });
    });
  });

  it('the dashboard of the administration', () => {
    open(`${base}&view=cpanel`);
    forum('admin-dashboard', 'main', { maxHeight: 900 });
  });

  it('the menu item of the forum with the categories of this forum', () => {
    open('/administrator/index.php?option=com_menus&task=item.edit&id=117');

    // The option is on one of the tabs of the form.
    cy.contains('label', /Categories of this forum/i).closest('joomla-tab-element').then(($pane) => {
      cy.get(`joomla-tab button[aria-controls="${$pane.attr('id')}"]`).filter(':visible').first().click();
    });
    forum('admin-menu-item-forum-home', 'main');

    // The menu item was checked out by opening it: it is free again.
    cy.task('queryDB', 'UPDATE #__menu SET checked_out = NULL, checked_out_time = NULL WHERE id = 117');
  });

  it('the mail templates of Kunena in the mail templates of Joomla', () => {
    open('/administrator/index.php?option=com_mails&view=templates&filter[extension]=com_kunena');
    forum('admin-mail-templates', 'main', { maxHeight: 700 });
  });
});
