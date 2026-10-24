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
// The counters and the last post of the categories, as the recount of Kunena makes them: a spec which deletes a topic with SQL
// leaves the category pointing at the topic, and the index shows "No topics" until the next recount.
Cypress.Commands.add('kunena_fixCategoryCounters', () => cy.task(
  'queryDB',
  'UPDATE #__kunena_categories AS c LEFT JOIN (SELECT category_id, COUNT(*) AS n, SUM(posts) AS p FROM #__kunena_topics WHERE hold = 0 AND moved_id = 0 GROUP BY category_id) AS s ON s.category_id = c.id SET c.numTopics = COALESCE(s.n, 0), c.numPosts = COALESCE(s.p, 0)',
).then(() => cy.task(
  'queryDB',
  'UPDATE #__kunena_categories AS c LEFT JOIN #__kunena_topics AS t ON t.id = (SELECT t2.id FROM #__kunena_topics AS t2 WHERE t2.category_id = c.id AND t2.hold = 0 AND t2.moved_id = 0 ORDER BY t2.last_post_time DESC, t2.last_post_id DESC LIMIT 1) SET c.last_topic_id = COALESCE(t.id, 0), c.last_post_id = COALESCE(t.last_post_id, 0), c.last_post_time = COALESCE(t.last_post_time, 0)',
)));

Cypress.Commands.add('kunena_fixUserCounters', () => cy.task('queryDB', 'DELETE ut FROM #__kunena_user_topics AS ut LEFT JOIN #__kunena_topics AS t ON t.id = ut.topic_id WHERE t.id IS NULL')
  .then(() => cy.task('queryDB', 'DELETE ur FROM #__kunena_user_read AS ur LEFT JOIN #__kunena_topics AS t ON t.id = ur.topic_id WHERE t.id IS NULL'))
  .then(() => cy.task('queryDB', 'UPDATE #__kunena_users AS u SET u.posts = (SELECT COUNT(*) FROM #__kunena_messages AS m WHERE m.userid = u.userid AND m.hold = 0)'))
  .then(() => cy.task('queryDB', "SELECT COUNT(*) AS n FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '#__kunena_inbox'")
    .then((rows) => (Number(rows[0].n) ? cy.task(
      'queryDB',
      "DELETE i FROM #__kunena_inbox AS i LEFT JOIN #__kunena_messages AS m ON m.id = i.message_id WHERE i.type <> 'message' AND i.message_id > 0 AND m.id IS NULL",
    ) : null))));
