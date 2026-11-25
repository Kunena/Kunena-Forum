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

namespace Kunena\Tests\Unit\Libraries\Kunena\Tools;

use Kunena\Forum\Libraries\Tools\KunenaModerationQueue;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Moderation queue" reads the reports from the moderation log.
 */
class KunenaModerationQueueTest extends UnitTestCase
{
    public function testAReportIsReadFromItsLogEntry(): void
    {
        $this->assertSame(
            ['mesid' => 12, 'reason' => 'Spam', 'text' => 'Links'],
            KunenaModerationQueue::parseReport(['data' => json_encode(['mesid' => '12', 'reason' => 'Spam', 'message' => 'Links'])])
        );
        $this->assertSame(['mesid' => 0, 'reason' => '', 'text' => ''], KunenaModerationQueue::parseReport(['data' => 'not json']));
    }
}
