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
 * A post is ignored as a duplicate (a double click) only when its poster also made the last post of the topic: only the text was
 * compared, so another member who replied the same ("Thanks!") within the minute lost his reply. For guests, the name is compared
 * too. (The task needs a whole site: its code is read.)
 */
class TopicDuplicatePostPosterTest extends UnitTestCase
{
    public function testTheDuplicateIsOfTheSamePoster(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $this->assertSame(1, preg_match('/\$lastTopic->last_post_message == \$message->message\n(.*?)\) \{\n\s*\$this->app->enqueueMessage\(Text::_\(\'COM_KUNENA_POST_DUPLICATE_IGNORED\'\)/s', $source, $m));
        $this->assertStringContainsString('(int) $lastTopic->last_post_userid === (int) $this->me->userid', $m[1]);
        $this->assertStringContainsString('(string) $lastTopic->last_post_guest_name === (string) $message->name', $m[1]);
    }
}
