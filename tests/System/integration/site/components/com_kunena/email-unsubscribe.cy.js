// The link to unsubscribe in a notification e-mail has a signature made with the secret of the site. Only a link with the right
// signature, for the right user and the right topic, may end a subscription, and only with a POST (a program which reads the mails
// may open the link with a GET).
describe('Kunena e-mail: the link to unsubscribe', () => {
  const base = '/index.php?option=com_kunena';
  const user = 798;
  let topic;
  let secret;

  const sign = (type, id, uid) => {
    const encoder = new TextEncoder();

    return crypto.subtle.importKey('raw', encoder.encode(secret), { name: 'HMAC', hash: 'SHA-256' }, false, ['sign'])
      .then((key) => crypto.subtle.sign('HMAC', key, encoder.encode(`kunena-unsubscribe|${type}|${id}|${uid}`)))
      .then((buffer) => [...new Uint8Array(buffer)].map((byte) => byte.toString(16).padStart(2, '0')).join(''));
  };

  const link = (params) => `${base}&view=topic&task=unsubscribemail&${new URLSearchParams(params).toString()}`;
  const subscribed = () => cy.task('queryDB', `SELECT subscribed FROM #__kunena_user_topics WHERE user_id = ${user} AND topic_id = ${topic}`).then((rows) => Number(rows[0].subscribed));
  const post = (params) => cy.request({ method: 'POST', url: link(params), failOnStatusCode: false, followRedirect: false });

  before(() => {
    cy.readFile('E:/www/apache/htdocs/tests/configuration.php').then((text) => {
      secret = /public \$secret = '([^']+)'/.exec(text)[1];
    });
  });

  // A topic of its own: the subscriptions of the topics of the test site are not touched.
  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress unsubscribe', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, UNIX_TIMESTAMP(), 797, 'x', 'ci-admin', 0, '')`,
    ).then((result) => {
      topic = result.insertId;

      return cy.task('queryDB', `INSERT INTO #__kunena_user_topics (user_id, topic_id, category_id, posts, last_post_id, owner, favorite, subscribed, params) VALUES (${user}, ${topic}, 3, 0, 0, 0, 0, 1, '')`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE user_id = ${user} AND topic_id = ${topic}`);
  });

  it('shows the confirmation for a GET and changes nothing', () => {
    cy.wrap(sign('topic', topic, user)).then((sig) => {
      cy.request({ url: link({ type: 'topic', id: topic, uid: user, sig }), failOnStatusCode: false, followRedirect: false }).its('status').should('be.lessThan', 400);
    });
    subscribed().should('eq', 1);
  });

  it('ends the subscription with a POST and the right signature', () => {
    cy.wrap(sign('topic', topic, user)).then((sig) => {
      post({ type: 'topic', id: topic, uid: user, sig }).its('status').should('be.lessThan', 400);
    });
    subscribed().should('eq', 0);
  });

  it('does nothing with a signature which is not right', () => {
    cy.wrap(sign('topic', topic, user)).then((sig) => {
      const wrong = [
        { type: 'topic', id: topic, uid: user, sig: sig.replace(/.$/, sig.endsWith('0') ? '1' : '0') },
        { type: 'topic', id: topic, uid: user, sig: '' },
        { type: 'topic', id: topic, uid: 797, sig },
        { type: 'topic', id: topic + 1, uid: user, sig },
        { type: 'category', id: topic, uid: user, sig },
        { type: 'topic', id: topic, uid: user, sig: sig.toUpperCase() },
      ];

      wrong.forEach((params) => post(params).its('status').should('be.lessThan', 500));
    });
    subscribed().should('eq', 1);
  });
});
