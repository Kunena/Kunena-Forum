// Data which belongs to the moderators and to the user himself must not be in the pages for a visitor or another user: the address of
// the computer (IP) of a message, the e-mail address of a guest who wrote a message, and the e-mail addresses of the users.
describe('Kunena privacy: addresses in the pages', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const ip = '203.0.113.77';
  const guestMail = `guest${stamp}@secret-example.test`;
  const ids = {};

  beforeEach(() => {
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_topics (category_id, subject, icon_id, label_id, locked, hold, ordering, posts, hits, attachments, poll_id, moved_id, first_post_id, first_post_time, first_post_userid, first_post_message, first_post_guest_name, last_post_id, last_post_time, last_post_userid, last_post_message, last_post_guest_name, rating, params) VALUES (3, 'Cypress private ${stamp}', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, UNIX_TIMESTAMP(), 0, 'x', 'A guest', 0, UNIX_TIMESTAMP(), 0, 'x', 'A guest', 0, '')`,
    ).then((topic) => {
      ids.topic = topic.insertId;

      return cy.task(
        'queryDB',
        `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (0, ${topic.insertId}, 3, 'A guest', 0, '${guestMail}', 'Cypress private ${stamp}', UNIX_TIMESTAMP(), '${ip}', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
      );
    }).then((message) => {
      ids.message = message.insertId;

      return cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, 'A message of a guest.')`);
    }).then(() => cy.task('queryDB', `UPDATE #__kunena_topics SET first_post_id = ${ids.message}, last_post_id = ${ids.message} WHERE id = ${ids.topic}`));
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${ids.topic}`);
    cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${ids.topic}`);
  });

  const pages = () => [
    `${base}&view=topic&catid=3&id=${ids.topic}`,
    `${base}&view=topic&catid=3&id=${ids.topic}&mesid=${ids.message}`,
    `${base}&view=category&catid=3`,
    `${base}&view=topics`,
    `${base}&view=search&task=results&query=Cypress+private&searchdate=all`,
    `${base}&view=topics&format=feed&layout=feed&type=rss`,
    `${base}&view=user&layout=list`,
    `${base}&view=user&layout=default&userid=798`,
    `${base}&view=user&layout=default&userid=797`,
    `${base}&view=statistics`,
  ];

  const emailsOfUsers = ['ci-user@example.com', 'schumacher1986@gmail.com'];

  const sweep = (who, mayKnowIp, ownMail = '') => {
    const leaks = [];

    pages().forEach((url) => {
      cy.request({ url, failOnStatusCode: false }).then((response) => {
        const body = String(response.body);
        const found = [];

        if (!mayKnowIp && body.includes(ip)) {
          found.push('the IP');
        }

        if (body.includes(guestMail)) {
          found.push('the e-mail of the guest');
        }

        emailsOfUsers.filter((mail) => mail !== ownMail).forEach((mail) => {
          if (body.includes(mail)) {
            found.push(`the e-mail ${mail}`);
          }
        });

        if (found.length) {
          leaks.push(`${url.replace(base, '')}: ${found.join(', ')}`);
        }
      });
    });
    cy.then(() => expect(leaks, `${who}: what the pages show`).to.deep.eq([]));
  };

  it('shows no address to a visitor', () => {
    sweep('a visitor', false);
  });

  it('shows no address to another user', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    // His own e-mail address is his: it may be in a page for him.
    sweep('a user', false, 'ci-user@example.com');
  });

  it('shows the IP of a message to the moderator', () => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
    cy.request(`${base}&view=topic&catid=3&id=${ids.topic}`).its('body').should('include', ip);
  });
});
