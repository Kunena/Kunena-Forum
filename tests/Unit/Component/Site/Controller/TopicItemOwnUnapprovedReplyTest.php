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
 * A member sees his own messages which wait for approval on the page of the topic, as the rights of a message allow (a reply held
 * for review, a scheduled reply with its notice): only the author of a topic which waits for approval saw them, so the author of a
 * reply in a published topic was sent to the topic after posting and did not find his reply, nor its button to edit it. (The page
 * needs a whole site: its code is read.)
 */
class TopicItemOwnUnapprovedReplyTest extends UnitTestCase
{
    public function testAMemberSeesHisOwnUnapprovedReplies(): void
    {
        $source = Files::read('src/site/src/Controller/Topic/Item/TopicItemDisplay.php');

        $this->assertStringContainsString('if ($this->me->exists() && (int) $this->me->userid > 0 && !$this->me->isModerator($topicCategory)) {', $source);
        $this->assertStringNotContainsString('if ($this->topic->isUnapprovedOf($this->me) && !$this->me->isModerator($topicCategory)) {', $source);
    }
}
