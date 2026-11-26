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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Topic;

use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The place of a message in its topic (which page of the topic a link to the message opens) is counted from the messages before
 * it. A short cut compared the id of the message with the number of the unread messages of the topic: the message whose id was
 * that number (the message 5 of a topic with 5 unread messages, on a young forum) opened the page of the first unread message.
 */
class KunenaTopicPostLocationTest extends UnitTestCase
{
    private mixed $savedLocation = null;

    protected function setUp(): void
    {
        $this->requireJoomla();

        $property            = new ReflectionProperty(KunenaMessageHelper::class, '_location');
        $this->savedLocation = $property->getValue();

        // The message 5 has two visible messages before it and seventeen after it.
        $property->setValue(null, [5 => (object) ['hold' => [0 => ['before' => 2, 'after' => 17]]]]);
    }

    protected function tearDown(): void
    {
        (new ReflectionProperty(KunenaMessageHelper::class, '_location'))->setValue(null, $this->savedLocation);
    }

    public function testAMessageWhoseIdIsTheNumberOfUnreadMessagesHasItsOwnPlace(): void
    {
        // A topic of 20 messages (the ids 100 to 200), 5 of them unread. (A shadow topic: its place needs no current user.)
        $topic                = (new ReflectionClass(KunenaTopic::class))->newInstanceWithoutConstructor();
        $topic->moved_id      = 1;
        $topic->posts         = 20;
        $topic->first_post_id = 100;
        $topic->last_post_id  = 200;
        $topic->lastread      = 150;
        $topic->unread        = 5;

        $this->assertSame(2, $topic->getPostLocation(5, 'asc', [0 => 0]));
        $this->assertSame(17, $topic->getPostLocation(5, 'desc', [0 => 0]));

        // The first and the last message keep their short cut.
        $this->assertSame(0, $topic->getPostLocation(100, 'asc', [0 => 0]));
        $this->assertSame(19, $topic->getPostLocation(200, 'asc', [0 => 0]));
    }
}
