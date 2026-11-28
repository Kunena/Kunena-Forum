<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Tests
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Tests\Unit\Plugin\Task\Kunena;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The task which sends the queue of the notifications goes past a full batch of which no mail could be sent (addresses which the server
 * refuses). It ended the run after every full batch ("will resume"), and the next run began with the same rows: the queue never went
 * past them, and the later notifications were never sent. A batch with a sent mail still ends the run. (The task sends mails: its code
 * is read.)
 */
class MailQueueFailedBatchTest extends UnitTestCase
{
    public function testABatchWithoutAnySentMailDoesNotEndTheRun(): void
    {
        $source = Files::read('src/plugins/plg_task_kunena/src/Extension/Kunena.php');

        $this->assertStringContainsString('if (\count($mailsToSend) >= $batchSize && !empty($processedIds)) {', $source);

        // The failed rows are left out of the next batches of the run.
        $this->assertStringContainsString("\$query->where(\$db->quoteName('id') . ' NOT IN (' . implode(',', array_map('intval', \$failedIds)) . ')');", $source);
    }
}
