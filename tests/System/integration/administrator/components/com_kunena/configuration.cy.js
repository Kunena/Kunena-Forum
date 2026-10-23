// The options of the features of 7.2 in the configuration of the forum, and the mail templates of Joomla. It needs the login of
// the administrator of the test site (cy.doAdministratorLogin).
describe('Kunena configuration', () => {
  const options = {
    quickBan: '1',
    mapProvider: 'auto',
    categoryAliasId: '0',
    useMailTemplates: '0',
    popupNotifications: '1',
    popupNotificationsInterval: '30',
    linkPreview: '1',
    linkPreviewMax: '3',
    autoEmbedSpotify: '1',
    autoEmbedDeezer: '1',
  };

  beforeEach(() => {
    cy.doAdministratorLogin();
  });

  it('has the options of the new features with their start values', () => {
    cy.visit('/administrator/index.php?option=com_config&view=component&component=com_kunena');

    Object.entries(options).forEach(([name, value]) => {
      cy.get(`[name="jform[${name}]"]`).should('exist').then(($fields) => {
        const field = $fields.toArray().find((element) => (element.type === 'radio' ? element.checked : true));

        expect(field.value, `the start value of ${name}`).to.eq(value);
      });
    });
  });

  it('saves a changed option', () => {
    cy.visit('/administrator/index.php?option=com_config&view=component&component=com_kunena');

    cy.get('label[for="jform_categoryAliasId1"]').click({ force: true });
    cy.get('#toolbar-apply button', { timeout: 15000 }).should('be.visible');
    // A new query for the click: the page of the configuration can draw its toolbar again after it is shown.
    cy.get('#toolbar-apply button').click();

    cy.get('joomla-alert').should('contain.text', 'Configuration saved');
    cy.get('#jform_categoryAliasId1').should('be.checked');

    // The start value is back for the other tests.
    cy.get('label[for="jform_categoryAliasId0"]').click({ force: true });
    cy.get('#toolbar-apply button', { timeout: 15000 }).should('be.visible');
    // A new query for the click: the page of the configuration can draw its toolbar again after it is shown.
    cy.get('#toolbar-apply button').click();
    cy.get('joomla-alert').should('contain.text', 'Configuration saved');
  });

  it('adds the mail templates of the forum to the mail templates of Joomla', () => {
    cy.task('queryDB', "SELECT template_id FROM #__mail_templates WHERE extension = 'com_kunena' ORDER BY template_id").then((rows) => {
      const ids = rows.map((row) => row.template_id);

      ['com_kunena.mention', 'com_kunena.moderator', 'com_kunena.reportmessage', 'com_kunena.subscription'].forEach((id) => {
        expect(ids, `the mail template ${id}`).to.include(id);
      });
    });

    cy.visit('/administrator/index.php?option=com_mails&view=templates');
    cy.contains('Kunena').should('exist');
  });
});
