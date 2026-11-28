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

namespace Kunena\Tests\Unit\Component\Site\Template\Aurelia;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The box of the accepted answer hides the text of the answer from a guest when the category of the topic hides the replies: it
 * followed the category of the address, which may show the topic as a channel and not hide them, so a guest read the answer there.
 * (The layout needs a whole site: its code is read.)
 */
class TopicAnswerTeaserCategoryTest extends UnitTestCase
{
    public function testTheCategoryOfTheTopicDecides(): void
    {
        $layout = Files::read('src/site/template/aurelia/layouts/topic/item/answer/default.php');

        $this->assertStringContainsString('!$this->topic->getCategory()->hidesRepliesForGuests()', $layout);
        $this->assertStringNotContainsString('$this->category->hidesRepliesForGuests()', $layout);
    }
}
