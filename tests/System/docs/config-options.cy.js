// Screenshots for docs/7.2: the new options of the configuration of Kunena, one picture of the row of each option. It needs the login
// of the administrator of the test site. Run it with cypress.docs.config.js (see the comment of that file).
import { hideToolbar, lightScheme } from './helpers.mjs';

describe('Documentation: the options of the configuration', () => {
  const config = '/administrator/index.php?option=com_config&view=component&component=com_kunena';

  const options = [
    'categoryAliasId',
    'reportMail',
    'useMailTemplates',
    'mentionEmail',
    'popupNotifications',
    'popupNotificationsInterval',
    'emailForumHeaders',
    'emailBatchSize',
    'enableDrafts',
    'enableIgnore',
    'enableFollow',
    'enableTags',
    'redirectDeletedTopics',
    'inactiveLockDays',
    'displayNameField',
    'nameUserChoice',
    'quickBan',
    'quickBanLevel',
    'quickBanDays',
    'quickBanDeletePosts',
    'enableMarkdown',
    'enableHtml',
    'autoEmbedSpotify',
    'autoEmbedDeezer',
    'linkPreview',
    'linkPreviewMax',
    'linkPreviewBlock',
    'mapProvider',
    'geocoderUrl',
  ];

  beforeEach(() => {
    lightScheme();
    cy.doAdministratorLogin();
  });

  options.forEach((name) => {
    it(`shows the option ${name}`, () => {
      cy.visit(config);

      cy.get(`[name="jform[${name}]"], [id="jform_${name}"], [id^="jform_${name}"]`)
        .first()
        .closest('.control-group')
        .as('group');

      // The option is on a tab of the form: the tab is opened first.
      cy.get('@group').then(($group) => {
        const pane = $group.closest('joomla-tab-element');

        if (pane.length) {
          cy.get(`joomla-tab button[aria-controls="${pane.attr('id')}"]`).filter(':visible').first().click();
        }
      });

      hideToolbar();
      cy.get('@group').scrollIntoView().should('be.visible').screenshot(`option-${name}`, { overwrite: true, padding: 6 });
    });
  });
});
