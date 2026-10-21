// Helpers of the screenshots for docs/7.2.

// A light page (the administrator template of Joomla follows the colour scheme of the browser).
export const lightScheme = () => cy.then(() => Cypress.automation('remote:debugger:protocol', {
  command: 'Emulation.setEmulatedMedia',
  params: { features: [{ name: 'prefers-color-scheme', value: 'light' }] },
}));

// The toolbar of the administrator sticks to the top of the page and covers the elements which are scrolled to, and the debug bar of
// Joomla (the test site runs with the debug mode on) hangs at the bottom: neither belongs in a picture.
export const hideToolbar = () => cy.document().then((doc) => {
  const style = doc.createElement('style');

  style.textContent = '#subhead-container, .subhead, .phpdebugbar, .phpdebugbar-openhandler { display: none !important; }';
  doc.head.appendChild(style);
});

// Opens a page without the parts which do not belong in a picture.
export const open = (url) => {
  cy.visit(url);

  return hideToolbar();
};

export const shot = (name, options = {}) => cy.screenshot(name, { overwrite: true, ...options });

// The picture of the forum (the content of the page, not the template of the site around it). The page is shown in a tall window
// and the picture is cut from it: the scrolling and joining of a screenshot of an element repeats the parts which stick to the top.
export const forum = (name, selector = '#kunena', options = {}) => {
  cy.viewport(1200, 2600);
  cy.window().then((win) => win.scrollTo(0, 0));
  cy.wait(options.wait ?? 700);

  // One element, or the area around several (a button and the menu which opened from it).
  const selectors = Array.isArray(selector) ? selector : [selector];

  return cy.get('body').then(($body) => {
    const rects = selectors.flatMap((one) => $body.find(one).first().toArray()).map((element) => ({
      rect: element.getBoundingClientRect(),
      height: Math.max(element.getBoundingClientRect().height, element.scrollHeight),
    }));

    expect(rects, `the elements ${selectors.join(', ')}`).to.have.length(selectors.length);

    const pad = options.padding ?? 4;
    const left = Math.min(...rects.map(({ rect }) => rect.x));
    const top = Math.min(...rects.map(({ rect }) => rect.y));
    const right = Math.max(...rects.map(({ rect }) => rect.x + rect.width));
    const bottom = Math.max(...rects.map(({ rect, height }) => rect.y + height));

    cy.screenshot(name, {
      capture: 'viewport',
      overwrite: true,
      clip: {
        x: Math.max(0, Math.floor(left) - pad),
        y: Math.max(0, Math.floor(top) - pad),
        width: Math.min(1200, Math.ceil(right - left) + 2 * pad),
        height: Math.min(options.maxHeight ?? 10000, Math.ceil(bottom - top)) + 2 * pad,
      },
    });
  });
};

// A text for a SQL statement: the backslashes and the quotes are escaped.
const sqlString = (value) => `'${value.replaceAll('\\', '\\\\').replaceAll("'", "\\'")}'`;

// A message in a topic, written by a user of the test site (the task of the database removes it again after the test).
export const addMessage = (thread, userid, name, text, subject = 'Re: the topic', ip = '127.0.0.1') => cy.task(
  'queryDB',
  `INSERT INTO #__kunena_messages (parent, thread, catid, name, userid, email, subject, time, ip, topic_emoticon, locked, hold, ordering, hits, moved, modified_by, modified_time, modified_reason) VALUES (${thread}, ${thread}, 3, '${name}', ${userid}, '', ${sqlString(subject)}, UNIX_TIMESTAMP(), '${ip}', 0, 0, 0, 0, 0, 0, 0, 0, '')`,
).then((info) => cy.task('queryDB', `INSERT INTO #__kunena_messages_text (mesid, message) VALUES (${info.insertId}, ${sqlString(text)})`).then(() => info.insertId));

// Options of the Aurelia template (a file of the template). Kunena keeps the options in a cache of its own, which is cleared.
export const templateOptions = (updates) => cy.task('kunenaTemplateParams', updates)
  .then(() => cy.task('deleteRelativePath', 'cache/com_kunena_template'))
  .then(() => cy.task('deleteRelativePath', 'administrator/cache/com_kunena_template'));

export const restoreTemplateOptions = (text) => cy.task('kunenaTemplateParamsRestore', text)
  .then(() => cy.task('deleteRelativePath', 'cache/com_kunena_template'))
  .then(() => cy.task('deleteRelativePath', 'administrator/cache/com_kunena_template'));
