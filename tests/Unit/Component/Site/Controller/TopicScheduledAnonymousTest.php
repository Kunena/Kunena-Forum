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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * An anonymous reply cannot be scheduled. It has no author, and the task which publishes the scheduled replies checks the author
 * (KunenaMessageSchedule::publishOne()): it found none, took the date away and left the reply waiting for good, and no moderator
 * had been told (a scheduled reply is not for the moderators). (The task needs a whole site: its code is read.)
 */
class TopicScheduledAnonymousTest extends UnitTestCase
{
    public function testAnAnonymousReplyIsNotScheduled(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $this->assertSame(1, preg_match('/public function post\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $refusal = strpos($m[1], "if (\$scheduledTime > 0 && \$fields['anonymous']) {");
        $this->assertNotFalse($refusal);
        $this->assertLessThan(strpos($m[1], '$message->scheduled_time   = $scheduledTime;'), $refusal, 'Refused before the reply is made.');
        $this->assertStringContainsString("Text::_('COM_KUNENA_POST_SCHEDULE_ERROR_ANONYMOUS')", $m[1]);
        $this->assertStringContainsString('COM_KUNENA_POST_SCHEDULE_ERROR_ANONYMOUS = ', Files::read('src/site/language/en-GB/com_kunena.ini'));
    }
}
