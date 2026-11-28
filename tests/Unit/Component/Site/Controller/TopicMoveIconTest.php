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
 * The icon which the form of the moderation gives a moved topic is one which the forms offer, as for a post (isTopicIconOfForm): any
 * number was taken, an icon of the system (500-505) or a number larger than the column (a database error). (The task needs a whole
 * site: its code is read.)
 */
class TopicMoveIconTest extends UnitTestCase
{
    public function testTheIconOfTheMoveIsChecked(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));
        $start  = strpos($source, '    public function move()');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);
        $check  = strpos($body, '$this->isTopicIconOfForm($target instanceof KunenaTopic ? $target->getCategory() : $target, $topic_emoticon)');

        $this->assertNotFalse($check);
        $this->assertLessThan(strpos($body, '$topic->move($target, $ids, $shadow, $subject, $changesubject, $topic_emoticon, $keep_poll)'), $check);
    }
}
