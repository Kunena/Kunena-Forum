/**
 * Commands for the options of Kunena (the parameters of com_kunena). They read and write the database of the test site, so a
 * test does not need the administrator to change an option. Setting an option clears the cache of the options in the folder
 * cache of the site, so Cypress must know the folder of the site: KUNENA_CMS_PATH (see cypress.config.js).
 */

const paramsQuery = "SELECT params FROM #__extensions WHERE element = 'com_kunena' AND type = 'component'";

// The value for a string of the database: the backslashes and the quotes of the JSON are escaped.
const sqlString = (value) => `'${value.replaceAll('\\', '\\\\').replaceAll("'", "\\'")}'`;

/**
 * Gives the value of an option of Kunena.
 *
 * @param {string} name The name of the option, for example enableDrafts
 *
 * @returns string|undefined
 */
Cypress.Commands.add('kunena_getOption', (name) => cy.task('queryDB', paramsQuery).then((rows) => {
  const params = JSON.parse(rows[0].params || '{}');

  return params[name];
}));

/**
 * Sets an option of Kunena and gives the value it had (so a test can put it back).
 *
 * @param {string} name  The name of the option, for example enableDrafts
 * @param {string|Object|null} value The new value (the options are strings, '1' or '0' for a switch; null removes the option)
 *
 * @returns string|Object|undefined The old value
 */
Cypress.Commands.add('kunena_setOption', (name, value) => cy.task('queryDB', paramsQuery).then((rows) => {
  const params = JSON.parse(rows[0].params || '{}');
  const old = params[name];

  if (value === null) {
    delete params[name];
  } else {
    // The options are strings ('1', '0'); a few (the badges) are a list of rows.
    params[name] = typeof value === 'object' ? value : String(value);
  }

  return cy.task(
    'queryDB',
    `UPDATE #__extensions SET params = ${sqlString(JSON.stringify(params))} WHERE element = 'com_kunena' AND type = 'component'`,
  )
    // Kunena keeps its options in a cache of its own, which never expires: it is made again from the database.
    .then(() => cy.task('deleteRelativePath', 'cache/com_kunena_configuration'))
    .then(() => cy.task('deleteRelativePath', 'administrator/cache/com_kunena_configuration'))
    .then(() => old);
}));

/**
 * Makes a published announcement, for a test which needs one on the list. The queryDB task removes it again after the test.
 *
 * @param {Object} data title, sdescription, description (all optional)
 *
 * @returns integer The id of the announcement
 */
Cypress.Commands.add('kunena_createAnnouncement', (data = {}) => {
  const announcement = {
    title: 'Cypress announcement',
    sdescription: 'The short text of the announcement',
    description: 'The text of the announcement',
    ...data,
  };
  const quote = (value) => sqlString(String(value));

  return cy.task(
    'queryDB',
    `INSERT INTO #__kunena_announcement (title, sdescription, description, created, published) VALUES (${quote(announcement.title)}, ${quote(announcement.sdescription)}, ${quote(announcement.description)}, NOW(), 1)`,
  ).then((info) => info.insertId);
});
