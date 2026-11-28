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
 * The edit of a reply by a moderator of the category renames the topic with its new subject, as the post of a reply does: the edit
 * asked for a global moderator (isModerator() without the category), so a moderator of the category could not. (The task needs a
 * whole site: its code is read.)
 */
class TopicEditSubjectModeratorTest extends UnitTestCase
{
    public function testAModeratorOfTheCategoryMayRenameTheTopic(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertStringContainsString('$topic->first_post_userid == $message->userid || $this->me->isModerator($topic->getCategory()))) {', $source);
        $this->assertStringNotContainsString('$topic->first_post_userid == $message->userid || $this->me->isModerator())) {', $source);
    }
}
