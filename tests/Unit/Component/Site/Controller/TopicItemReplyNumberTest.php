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
 * "Show Position in Topic" gives a message its place in the topic in both orders: with the newest first the page counted up from
 * its start, so the newest reply was #1, the first post was #N and, pinned on top of the page, #1 too. (The page needs a whole
 * site: its code is read.)
 */
class TopicItemReplyNumberTest extends UnitTestCase
{
    public function testNewestFirstCountsDown(): void
    {
        $source = Files::read('src/site/src/Controller/Topic/Item/TopicItemDisplay.php');

        $this->assertStringContainsString("\$descending     = \$this->me->getMessageOrdering() != 'asc';", $source);
        $this->assertStringContainsString('$message->replynum = $descending ? $total - $location++ : ++$location;', $source);
    }
}
