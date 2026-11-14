// The date fields of the forum (the search at a date, the birthdate of the profile) write the date in the format of the option
// "Date format for datepicker". A format like dd/mm/yyyy is no format of the date parser of PHP: the search showed the error of
// the parser and the profile was not saved. A text which is no date gives no error page.
describe('Kunena date fields: the format of the date picker', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const user = { name: 'ci-user', password: 'ci-user-17082005-x', id: 798 };
  const word = `zqxdate${stamp}`;
  const old = {};

  const search = (date) => cy.request({ url: `${base}&view=search&task=results&query=${word}&searchatdate=${encodeURIComponent(date)}`, failOnStatusCode: false })
    .then((response) => String(response.body));

  before(() => {
    cy.kunena_setOption('datePickerFormat', 'dd/mm/yyyy').then((value) => {
      old.format = value;
    });
    // As text: a date of the database is shifted by the time zone of the browser in a Date of JavaScript.
    cy.task('queryDB', `SELECT CAST(birthdate AS CHAR) AS birthdate FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      old.birthdate = rows[0].birthdate || '1000-01-01';
    });
  });

  after(() => {
    cy.kunena_setOption('datePickerFormat', old.format ?? '');
    cy.then(() => cy.task('queryDB', `UPDATE #__kunena_users SET birthdate = '${old.birthdate}' WHERE userid = ${user.id}`));
  });

  beforeEach(() => {
    // A message of the 15th of March 2025 (noon, UTC) with a word of its own.
    cy.task(
      'queryDB',
      `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (10, 9, 3, 'ci-user', ${user.id}, '', 'Re: Which template do you use?', 1742040000, '127.0.0.1', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
    ).then((message) => {
      old.message = message.insertId;
      cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${message.insertId}, '${word} of a day')`);
    });
  });

  afterEach(() => {
    cy.task('queryDB', `DELETE FROM #__kunena_messages_text WHERE mesid = ${old.message}`);
    cy.task('queryDB', `DELETE FROM #__kunena_messages WHERE id = ${old.message}`);
  });

  it('finds the message of a day with that day in the format of the date picker, and not with the next day', () => {
    search('15/03/2025').then((body) => {
      expect(body, 'the error of the date parser').to.not.include('Failed to parse');
      expect(body, 'the message of that day').to.include(`${word} of a day`);
    });
    search('16/03/2025').then((body) => expect(body, 'the message on the next day').to.not.include(`${word} of a day`));
  });

  it('gives no error page for a text which is no date', () => {
    search('xyz').then((body) => expect(body, 'the error of the date parser').to.not.include('Failed to parse'));
  });

  it('saves the birthdate of the profile in the format of the date picker', () => {
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=user&layout=edit`).then((page) => {
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      return cy.request({
        method: 'POST',
        url: `${base}&view=user&task=save`,
        form: true,
        body: { userid: user.id, birthdate: '23/10/1990', [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.task('queryDB', `SELECT CAST(birthdate AS CHAR) AS birthdate FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      expect(rows[0].birthdate, 'the birthdate').to.eq('1990-10-23');
    });
  });

  it('shows the birthdate in the format of the date picker, and keeps it with a save without a change', () => {
    // A day which is also a month (5 June): written as month/day it would come back as 6 May.
    cy.task('queryDB', `UPDATE #__kunena_users SET birthdate = '1990-06-05' WHERE userid = ${user.id}`);
    cy.doFrontendLogin(user.name, user.password, false);
    cy.request(`${base}&view=user&layout=edit`).then((page) => {
      const field = /<input[^>]*name="birthdate"[^>]*>/.exec(page.body)?.[0] ?? '';
      const value = /value="([^"]*)"/.exec(field)?.[1];
      const token = /name="([0-9a-f]{32})" value="1"/.exec(page.body)[1];

      expect(value, 'the birthdate in the form').to.eq('05/06/1990');

      return cy.request({
        method: 'POST',
        url: `${base}&view=user&task=save`,
        form: true,
        body: { userid: user.id, birthdate: value, [token]: '1' },
        failOnStatusCode: false,
        followRedirect: false,
      });
    });
    cy.task('queryDB', `SELECT CAST(birthdate AS CHAR) AS birthdate FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      expect(rows[0].birthdate, 'the birthdate after the save').to.eq('1990-06-05');
    });
  });

  it('shows the birthdate beside a post in the date format of the forum', () => {
    cy.kunena_setOption('datePickerFormat', 'dd.mm.yyyy');
    cy.task('queryDB', `UPDATE #__kunena_users SET birthdate = '1990-06-05' WHERE userid = ${user.id}`);
    cy.then(() => cy.request(`${base}&view=topic&catid=3&id=9&mesid=${old.message}`)).then((page) => {
      expect(page.body, 'the post of the user').to.contain(`${word} of a day`);
      // COM_KUNENA_LIB_DATE_FMT of en-GB: d M Y.
      expect(page.body, 'the birthdate in the tooltip').to.contain('title="Birthdate: 05 Jun 1990"');
      expect(page.body, 'the birthdate of the database').not.to.contain('Birthdate: 1990-06-05');
    });
  });

  // The calendar writes the date in the format of the option, and in yyyy-mm-dd without the option.
  [
    { format: 'dd.mm.yyyy', shown: '05.06.1990', picked: '12.06.1990' },
    { format: '', shown: '1990-06-05', picked: '1990-06-12' },
  ].forEach(({ format, shown, picked }) => {
    it(`picks the birthdate in the calendar in the format ${format || 'yyyy-mm-dd'}`, () => {
      cy.kunena_setOption('datePickerFormat', format);
      cy.task('queryDB', `UPDATE #__kunena_users SET birthdate = '1990-06-05' WHERE userid = ${user.id}`);
      cy.doFrontendLogin(user.name, user.password, false);
      // The tooltip of Bootstrap moves the title out of the field: it is read in the page as sent.
      cy.request(`${base}&view=user&layout=edit`).then((page) => {
        const field = /<input[^>]*name="birthdate"[^>]*>/.exec(page.body)?.[0] ?? '';
        const title = /title="([^"]*)"/.exec(field)?.[1];

        expect(title, 'the hint of the field').to.contain(format || 'yyyy-mm-dd');
        expect(title, 'the hint of the field').not.to.contain('Year (YYYY) / Month (MM) / Day (DD)');
      });
      cy.visit(`${base}&view=user&layout=edit`);
      cy.get('a[href="#editprofile"]').click();
      cy.get('input[name="birthdate"]').should('be.visible').and('have.value', shown);
      cy.get('#birthdate .input-group.date .btn').click();
      cy.get('.datepicker-days td.day.active').filter(':visible').should('have.length', 1).and('have.text', '5');
      cy.get('.datepicker-days th.datepicker-switch').filter(':visible').should('contain', '1990');
      cy.get('input[name="birthdate"]').should('have.value', shown);
      cy.get('.datepicker-days td.day:not(.old):not(.new)').filter(':visible').filter((index, day) => day.textContent.trim() === '12').click();
      cy.get('input[name="birthdate"]').should('have.value', picked);
    });
  });

  it('keeps the birthdate with a save without a change of the user form of the backend', () => {
    // The tests of the calendar leave another format.
    cy.kunena_setOption('datePickerFormat', 'dd/mm/yyyy');
    cy.task('queryDB', `UPDATE #__kunena_users SET birthdate = '1990-06-05' WHERE userid = ${user.id}`);
    // The cookies of the logins of the site go first: with them the backend lost its session after the save.
    cy.clearAllCookies();
    cy.doAdministratorLogin();
    cy.visit(`/administrator/index.php?option=com_kunena&view=user&layout=edit&userid=${user.id}`);
    cy.get('input[name="birthdate"]').should('have.value', '05/06/1990');
    cy.get('#toolbar-apply button', { timeout: 15000 }).should('be.visible');
    // A new query for the click: the page of the configuration can draw its toolbar again after it is shown.
    cy.get('#toolbar-apply button').click();
    cy.get('joomla-alert, #system-message-container', { timeout: 15000 }).should('contain.text', 'ed');
    cy.task('queryDB', `SELECT CAST(birthdate AS CHAR) AS birthdate FROM #__kunena_users WHERE userid = ${user.id}`).then((rows) => {
      expect(rows[0].birthdate, 'the birthdate after the save of the backend').to.eq('1990-06-05');
    });
  });
});

