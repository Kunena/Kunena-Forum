// The order of the user list (view=user&layout=list&filter_order=...) is one of the fields of the list. A word which is no
// field gave an SQL error, and the word stayed in the session: the list was broken for the rest of it. The e-mail address is
// a field of the order only while the list shows the addresses.
describe('Kunena user list: the field of the order', () => {
  const base = '/index.php?option=com_kunena&view=user&layout=list';
  let userlistEmail;

  const ordering = (body) => /name="filter_order" value="([^"]*)"/.exec(body)?.[1];
  const list = (query) => cy.request({ url: `${base}&${query}`, failOnStatusCode: false }).then((response) => {
    expect(response.status, `the status for "${query}"`).to.be.lessThan(500);

    return { status: response.status, body: String(response.body) };
  });

  before(() => {
    cy.kunena_setOption('userlistEmail', '0').then((old) => {
      userlistEmail = old;
    });
  });

  after(() => {
    cy.kunena_setOption('userlistEmail', userlistEmail ?? '0');
  });

  it('takes a word which is no field as the standard order, also for the rest of the session', () => {
    list('filter_order=foo').then(({ status, body }) => {
      expect(status, 'the status').to.eq(200);
      expect(ordering(body), 'the order').to.not.eq('foo');
    });
    list('').then(({ status, body }) => {
      expect(status, 'the status of the list afterwards').to.eq(200);
      expect(body, 'the list afterwards').to.include('ci-admin');
    });
  });

  it('orders by a field of the list', () => {
    list('filter_order=posts&filter_order_Dir=desc').then(({ body }) => {
      expect(ordering(body), 'the order').to.eq('posts');
    });
  });

  it('does not order by the e-mail address while the list does not show it', () => {
    list('filter_order=email').then(({ body }) => {
      expect(ordering(body), 'the order').to.not.eq('email');
    });
  });
});
