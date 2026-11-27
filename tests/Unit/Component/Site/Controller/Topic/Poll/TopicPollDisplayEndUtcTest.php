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

namespace Kunena\Tests\Unit\Component\Site\Controller\Topic\Poll;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The end of a poll is stored in UTC (as the vote reads it): the page read it in the time zone of PHP, and showed the poll as ended
 * (or open) hours before or after the votes were refused. (The display needs a whole topic: its code is read.)
 */
class TopicPollDisplayEndUtcTest extends UnitTestCase
{
    public function testThePageReadsTheEndInUtc(): void
    {
        $source = Files::read('src/site/src/Controller/Topic/Poll/TopicPollDisplay.php');

        $this->assertStringContainsString('new DateTimeImmutable($this->poll->polltimetolive, new DateTimeZone(\'UTC\'))', $source);
    }
}
