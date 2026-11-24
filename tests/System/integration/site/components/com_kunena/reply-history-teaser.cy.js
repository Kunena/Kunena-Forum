// The form to reply (view=topic&layout=reply) shows the last messages of the topic under the form. With "hide the replies from the
// guests" (teaser) and guests who may write, a guest who opened the form read there the replies which the topic hides from him.
describe('Kunena reply form: the replies hidden from the guests in the history', () => {
  const reply = 'I use the Cassiopeia template of Joomla';
  const first = 'I use the Aurelia template with the light colours';
  const old = {};

  before(() => {
    cy.kunena_setOption('teaser', '1').then((value) => {
      old.teaser = value ?? null;
    });
    cy.kunena_setOption('pubWrite', '1').then((value) => {
      old.pubWrite = value ?? null;
    });
    // The guests may reply in category 3 (the groups Guest and Public with those who may reply).
    cy.task('queryDB', 'SELECT params FROM #__kunena_categories WHERE id = 3').then((rows) => {
      old.params = rows[0].params;

      const params = JSON.parse(old.params || '{}');

      params.access_reply = [...new Set([...(params.access_reply || []), '9', '1'])];

      return cy.task('queryDB', `UPDATE #__kunena_categories SET params = '${JSON.stringify(params)}' WHERE id = 3`);
    });
  });

  after(() => {
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_categories SET params = '${old.params}' WHERE id = 3`));
    cy.kunena_setOption('teaser', old.teaser);
    cy.kunena_setOption('pubWrite', old.pubWrite);
  });

  it('shows a guest the first message but not the reply', () => {
    cy.request('/index.php?option=com_kunena&view=topic&layout=reply&catid=3&id=9').then((page) => {
      const body = String(page.body);

      expect(body, 'the history of the topic').to.include(first);
      expect(body, 'the hidden reply').not.to.include(reply);
    });
  });
});
