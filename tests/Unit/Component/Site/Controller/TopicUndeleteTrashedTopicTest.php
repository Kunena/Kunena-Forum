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
 * A message of a topic in the trash is not restored (or approved) alone: the trash puts the messages of a topic there with it, and
 * restoring one of them published the topic again with that message only (the others stayed in the trash, and the topic had no
 * button to restore them any more). The restore of one message, the restore and the approval of the selected messages of the lists
 * refuse it, and the message has no button for it, as for the approval of one message. (The tasks need a whole site: their code is
 * read.)
 */
class TopicUndeleteTrashedTopicTest extends UnitTestCase
{
    private static function method(string $file, string $signature): string
    {
        $source = str_replace("\r\n", "\n", Files::read($file));
        $start  = strpos($source, $signature);

        return $start === false ? '' : substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);
    }

    public function testTheTasksRefuseAMessageOfATopicInTheTrash(): void
    {
        $bodies = [
            'undelete'      => self::method('src/site/src/Controllers/TopicController.php', '    public function undelete()'),
            'restore_posts' => self::method('src/site/src/Controllers/TopicsController.php', '    public function restore_posts()'),
            'approve_posts' => self::method('src/site/src/Controllers/TopicsController.php', '    public function approve_posts()'),
        ];

        foreach ($bodies as $task => $body) {
            $this->assertNotSame('', $body, $task);
            $this->assertMatchesRegularExpression('/\(int\) \$(message->getTopic\(\)|topic)->hold >= KunenaForum::DELETED/', $body, $task);
            $this->assertStringContainsString('COM_KUNENA_MODERATE_APPROVE_TOPIC_DELETED', $body, $task);

            // The check comes before the publish.
            $this->assertLessThan(strpos($body, '->publish(KunenaForum::PUBLISHED)'), strpos($body, '>= KunenaForum::DELETED'), $task);
        }
    }

    public function testTheMessageHasNoRestoreButtonInATopicInTheTrash(): void
    {
        $source = Files::read('src/site/src/Controller/Message/Item/Actions/MessageItemActionsDisplay.php');

        $this->assertStringContainsString("\$this->message->isAuthorised('undelete') && (int) \$this->message->getTopic()->hold < 2", $source);
    }
}
