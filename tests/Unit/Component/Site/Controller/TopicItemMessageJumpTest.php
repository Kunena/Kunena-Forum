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
 * The page of a topic opened for one of its messages (mesid):
 * - the page starts at the page of the message: the place of the message was the start, so with 10 posts a page a link to the
 *   13th post showed the posts 13 to 22, the posts 11 and 12 were seen nowhere, and the pagination called it another page;
 * - a message split into another topic since: that topic was shown under the address of the old one, and the links of its pages
 *   went to the old topic; the page goes to the address of the message;
 * - the description of a page whose first post has no text is the subject, escaped once (by Joomla): it was escaped twice more.
 * (The page needs a whole site: its code is read.)
 */
class TopicItemMessageJumpTest extends UnitTestCase
{
    private function source(): string
    {
        return str_replace("\r\n", "\n", Files::read('src/site/src/Controller/Topic/Item/TopicItemDisplay.php'));
    }

    public function testThePageOfAMessageStartsAtItsPage(): void
    {
        $source = $this->source();

        $this->assertStringContainsString('$start = $limit > 0 ? intdiv($this->topic->getPostLocation($mesid), $limit) * $limit : 0;', $source);
        $this->assertStringNotContainsString('$start            = $mesid ? $this->topic->getPostLocation($mesid) : $start;', $source);

        // 10 a page: the 13th post (place 12) is on the page which starts at 10.
        $this->assertSame(10, intdiv(12, 10) * 10);
    }
}
