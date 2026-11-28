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
 * The page of a topic orders the messages of the same second by their ids, in the direction of the page (as their places are
 * counted): the database chose their order for each page, so at the end of a page one of them was shown twice and the other on no
 * page. (The page needs a whole site: its code is read.)
 */
class TopicItemSameTimeOrderTest extends UnitTestCase
{
    public function testTheMessagesOfTheSameSecondAreInTheOrderOfTheirIds(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controller/Topic/Item/TopicItemDisplay.php'));

        $this->assertStringContainsString("->order('time', \$direction)\n            ->order('id', \$direction)", $source);
    }
}
