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
 * A message of the tree of the threaded view links to the permalink of the message, which says which message the page shows: the
 * link of the topic had only #id, so the page showed the first message of its page, not the one which was clicked (Agora uses the
 * row of Aurelia). (The layout needs a whole site: its code is read.)
 */
class ThreadedRowPermalinkTest extends UnitTestCase
{
    public function testTheRowLinksToThePermalinkOfTheMessage(): void
    {
        $row = Files::read('src/site/template/aurelia/layouts/topic/item/message/row.php');

        $this->assertStringContainsString('$this->message->getPermaUrl()', $row);
        $this->assertStringNotContainsString('$this->getTopicLink(', $row);
    }
}
