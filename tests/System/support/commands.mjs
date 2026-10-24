/**
 * Imports commands from files. The commands start with the folder name and an underscore as Cypress doesn't support
 * namespaces for commands.
 *
 * https://github.com/cypress-io/cypress/issues/6575
 */

import { registerCommands } from 'joomla-cypress';

import './commands/api.mjs';
import './commands/config.mjs';
import './commands/db.mjs';
import './commands/kunena.mjs';

registerCommands();

// Click Joomla Dialog Confirm, isOkay: true = push "ok" button, false = push "cancel" button
Cypress.Commands.add('clickDialogConfirm', (isOkay) => {
  let selector = '.joomla-dialog-confirm';
  if (isOkay) {
    selector += ' button[data-button-ok]';
  } else {
    selector += ' button[data-button-cancel]';
  }
  return cy.get(selector, { timeout: 1000 }).click();
});

/**
 * Puts the rows and counters of the users of the test site right after a test which wrote through the forms and removes its
 * topics and messages with SQL: the rows of #__kunena_user_topics and #__kunena_user_read of topics which are gone, and the
 * number of posts of every user (his published messages). Without it the next tests start with counters which are off. The
 * notifications of the inbox plugin about the messages which are gone go too (the posts of the tests leave them in the inboxes).
 */
Cypress.Commands.add('kunena_fixUserCounters', () => cy.task('queryDB', 'DELETE ut FROM #__kunena_user_topics AS ut LEFT JOIN #__kunena_topics AS t ON t.id = ut.topic_id WHERE t.id IS NULL')
  .then(() => cy.task('queryDB', 'DELETE ur FROM #__kunena_user_read AS ur LEFT JOIN #__kunena_topics AS t ON t.id = ur.topic_id WHERE t.id IS NULL'))
  .then(() => cy.task('queryDB', 'UPDATE #__kunena_users AS u SET u.posts = (SELECT COUNT(*) FROM #__kunena_messages AS m WHERE m.userid = u.userid AND m.hold = 0)'))
  .then(() => cy.task('queryDB', "SELECT COUNT(*) AS n FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '#__kunena_inbox'")
    .then((rows) => (Number(rows[0].n) ? cy.task(
      'queryDB',
      "DELETE i FROM #__kunena_inbox AS i LEFT JOIN #__kunena_messages AS m ON m.id = i.message_id WHERE i.type <> 'message' AND i.message_id > 0 AND m.id IS NULL",
    ) : null))));
