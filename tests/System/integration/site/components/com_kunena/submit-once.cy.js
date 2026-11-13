// A posting form is sent only once (issue #6618): a second submit of a new topic or of a quick reply is ignored by
// assets/js/submitonce.js, the draft button still sends savedraft=1, and a submit which the browser's validation blocks leaves the
// form usable. The answer is checked in the database.
describe('Kunena writing: a posting form is sent only once', () => {
  const base = '/index.php?option=com_kunena';
  const prefix = `Cypress submit once ${Date.now()}`;
  const old = {};
  let topicId;
  let firstMessage;

  const typeInEditor = (text) => {
    cy.window().should('have.property', 'sceditor');
    cy.get('#postform .sceditor-container iframe').first().its('0.contentDocument.body').should('not.be.empty')
      .then(($body) => cy.wrap($body).click().type(text, { parseSpecialCharSequences: false }));
  };
  // The rows of a query, asked again until there is one (the page which posts may still be loading).
  const rowsOf = (query, tries = 20) => cy.task('queryDB', query)
    .then((rows) => (rows.length || tries <= 0 ? rows : cy.wait(500).then(() => rowsOf(query, tries - 1))));
  // Submits the form twice in the same tick, with the same button, and gives which of the two submits were cancelled.
  const submitTwice = (form, button) => {
    const cancelled = [];

    form.addEventListener('submit', (event) => cancelled.push(event.defaultPrevented));
    form.requestSubmit(button);
    expect(form.getAttribute('aria-busy'), 'the form is busy').to.eq('true');
    expect(button.classList.contains('disabled'), 'the button looks busy').to.eq(true);
    expect(button.hasAttribute('disabled'), 'the button keeps no disabled attribute').to.eq(false);
    form.requestSubmit(button);

    return cancelled;
  };
  // The bodies of the POST requests of the page, as text.
  const bodies = [];
  const recordPosts = () => {
    bodies.length = 0;
    cy.intercept('POST', '**', (req) => {
      bodies.push(typeof req.body === 'string' ? req.body : new TextDecoder().decode(req.body));
    });
  };

  before(() => {
    [['enableDrafts', '1'], ['quickReply', '1']].forEach(([name, value]) => cy.kunena_setOption(name, value).then((before) => {
      old[name] = before;
    }));
  });

  after(() => {
    cy.kunena_setOption('enableDrafts', old.enableDrafts ?? null);
    cy.kunena_setOption('quickReply', old.quickReply ?? null);
    cy.task('queryDB', `SELECT id FROM #__kunena_topics WHERE subject LIKE '${prefix}%'`).then((rows) => {
      rows.forEach(({ id }) => {
        cy.task('queryDB', `DELETE x FROM #__kunena_messages_text AS x INNER JOIN #__kunena_messages AS m ON m.id = x.mesid WHERE m.thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE thread = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_user_topics WHERE topic_id = ${id}`);
        cy.task('queryDB', `DELETE FROM #__kunena_topics WHERE id = ${id}`);
      });
    });
    cy.task('queryDB', `DELETE FROM #__kunena_drafts WHERE subject LIKE '${prefix}%'`);
    cy.kunena_fixUserCounters();
  });

  beforeEach(() => {
    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'));
  });

  it('makes one topic when the form of a new topic is submitted twice', () => {
    const subject = `${prefix} topic`;

    recordPosts();
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    cy.get('#postform').should('have.attr', 'data-kunena-submit-once');
    cy.get('#postform input[name="subject"]').type(subject);
    typeInEditor('A topic which is submitted twice.');
    cy.get('#form_submit_button').should('not.be.disabled').then(($button) => {
      const cancelled = submitTwice($button[0].form, $button[0]);

      expect(cancelled, 'the second submit is cancelled').to.deep.eq([false, true]);
    });

    rowsOf(`SELECT id, first_post_id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      topicId = rows[0].id;
      firstMessage = rows[0].first_post_id;
    });
    cy.wait(1000);
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      expect(Number(rows[0].n), 'one topic').to.eq(1);
    });
    cy.wrap(bodies).then((posted) => {
      expect(posted.filter((body) => body.includes(subject)), 'one request with the topic').to.have.length(1);
    });
  });

  it('makes one reply when a quick reply is submitted twice', () => {
    const text = `${prefix} quick reply`;

    recordPosts();
    cy.visit(`${base}&view=topic&catid=3&id=${topicId}`);
    cy.get(`[data-bs-target="#kreply${firstMessage}_form"]`).first().click({ force: true });
    cy.get(`#postform-${firstMessage}`).should('have.attr', 'data-kunena-submit-once');
    cy.get(`#postform-${firstMessage} textarea[name="message"]`).invoke('val', text);
    cy.get(`#postform-${firstMessage} input.kreply-submit`).then(($button) => {
      const cancelled = submitTwice($button[0].form, $button[0]);

      expect(cancelled, 'the second submit is cancelled').to.deep.eq([false, true]);
    });

    rowsOf(`SELECT mesid FROM #__kunena_messages_text WHERE message = '${text}'`);
    cy.wait(1000);
    cy.task('queryDB', `SELECT COUNT(*) AS n FROM #__kunena_messages_text WHERE message = '${text}'`).then((rows) => {
      expect(Number(rows[0].n), 'one reply').to.eq(1);
    });
    cy.wrap(bodies).then((posted) => {
      expect(posted.filter((body) => body.includes(text)), 'one request with the reply').to.have.length(1);
    });
  });

  it('still sends savedraft=1 with the draft button', () => {
    const subject = `${prefix} draft`;

    recordPosts();
    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    cy.get('#postform input[name="subject"]').type(subject);
    typeInEditor('The text of the draft.');
    cy.get('#form_draft_button').click();

    rowsOf(`SELECT subject, message FROM #__kunena_drafts WHERE subject = '${subject}'`).then((rows) => {
      expect(rows, 'the draft').to.have.length(1);
      expect(rows[0].message).to.include('The text of the draft.');
    });
    cy.wrap(bodies).then((posted) => {
      const sent = posted.filter((body) => body.includes(subject));

      expect(sent, 'one request with the draft').to.have.length(1);
      // The form is multipart: the button is a part named savedraft with the value 1.
      expect(sent[0]).to.match(/savedraft=1|name="savedraft"\r?\n\r?\n1\r?\n/);
    });
  });

  it('leaves the form usable when the validation of the browser blocks the submit', () => {
    const subject = `${prefix} validation`;

    cy.visit(`${base}&view=topic&layout=create&catid=3`);
    typeInEditor('A topic without a subject at first.');
    cy.get('#postform input[name="subject"]').should('have.value', '');
    // The page of the form, as the site shows it (with SEF URLs it is not the address of the visit).
    cy.location('href').then((href) => {
      cy.get('#form_submit_button').should('not.be.disabled').click();
      cy.location('href').should('eq', href);
    });
    cy.get('#postform').should('not.have.attr', 'aria-busy');
    cy.get('#postform button[type="submit"], #postform input[type="submit"]').each(($button) => {
      expect($button.attr('aria-disabled'), 'not busy').to.not.eq('true');
    });
    cy.get('#form_submit_button').should('not.have.class', 'disabled');

    cy.get('#postform input[name="subject"]').type(subject);
    cy.get('#form_submit_button').click();

    rowsOf(`SELECT id FROM #__kunena_topics WHERE subject = '${subject}'`).then((rows) => {
      expect(rows, 'the topic').to.have.length(1);
    });
  });
});
