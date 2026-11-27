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
 * A reply which waits for approval in a topic which was put in the trash afterwards: approving it on the site published the topic
 * again with that reply only (KunenaMessage::update() publishes an unpublished topic of a published message), while its other
 * messages stayed in the trash. The approval is refused and the button is not shown. (The task needs a whole site: its code is
 * read.)
 */
class TopicApproveTrashedTopicTest extends UnitTestCase
{
    public function testTheApprovalOfAMessageOfATopicInTheTrashIsRefused(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $this->assertSame(1, preg_match('/public function approve\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $refusal = strpos($m[1], "if (\$target instanceof KunenaMessage && (int) \$topic->hold >= KunenaForum::DELETED) {");
        $publish = strpos($m[1], '$target->publish(KunenaForum::PUBLISHED);');

        $this->assertNotFalse($refusal, 'A message of a topic in the trash is refused.');
        $this->assertLessThan($publish, $refusal, 'It is refused before it is published.');
        $this->assertStringContainsString("Text::_('COM_KUNENA_MODERATE_APPROVE_TOPIC_DELETED')", $m[1]);
        $this->assertStringContainsString('COM_KUNENA_MODERATE_APPROVE_TOPIC_DELETED = ', Files::read('src/site/language/en-GB/com_kunena.ini'));
    }

    public function testTheButtonIsNotShownForAMessageOfATopicInTheTrash(): void
    {
        $source = Files::read('src/site/src/Controller/Message/Item/Actions/MessageItemActionsDisplay.php');

        $this->assertStringContainsString('if ($this->message->hold == 1 && (int) $this->message->getTopic()->hold < 2) {', $source);
    }
}
