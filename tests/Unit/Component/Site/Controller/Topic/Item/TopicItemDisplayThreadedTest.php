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

namespace Kunena\Tests\Unit\Component\Site\Controller\Topic\Item;

use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Site\Controller\Topic\Item\TopicItemDisplay;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The threaded and indented topic layouts (issue #10246): the messages of the page in the order of the tree of the replies, each
 * with the edges which the tree of the threaded layout draws and from which the indented layout takes the depth.
 */
class TopicItemDisplayThreadedTest extends UnitTestCase
{
    /**
     * The topic: 1 is the first message, 2 and 4 answer 1, 3 answers 2. The page has them in the order of time: 1, 2, 4, 3.
     */
    private function display(): TopicItemDisplay
    {
        $this->requireJoomla();

        $messages = [];

        foreach ([1 => 0, 2 => 1, 4 => 1, 3 => 2] as $id => $parent) {
            $messages[$id] = self::bare(KunenaMessage::class, ['id' => $id, 'parent' => $parent, 'thread' => 1]);
        }

        $topic = $this->createStub(KunenaTopic::class);
        $topic->method('getTotal')->willReturn(4);
        $topic->first_post_id = 1;

        // As prepareMessages() collects them for these layouts: the replies under their parent, the others under 0.
        return self::bare(TopicItemDisplay::class, [
            'messages'   => $messages,
            'topic'      => $topic,
            'pagination' => (object) ['limitstart' => 0, 'limit' => 20],
            'threaded'   => [0 => [1], 1 => [2, 4], 2 => [3]],
        ]);
    }

    public function testTheRepliesFollowTheMessageTheyAnswer(): void
    {
        $this->assertSame([1, 2, 3, 4], array_keys($this->invoke($this->display(), 'getThreadedOrdering')));
    }

    public function testEachMessageHasTheEdgesOfItsPlaceInTheTree(): void
    {
        $list = $this->invoke($this->display(), 'getThreadedOrdering');

        $this->assertSame(['lastedge', 'root'], $list[1]->indent);
        $this->assertSame(['empty', 'crossedge', 'node'], $list[2]->indent);
        $this->assertSame(['empty', 'edge', 'lastedge', 'leaf'], $list[3]->indent);
        $this->assertSame(['empty', 'lastedge', 'leaf'], $list[4]->indent);
    }

    public function testAPageWithoutMessagesHasAnEmptyTree(): void
    {
        $display           = $this->display();
        $display->messages = [];
        $display->threaded = [];

        $this->assertSame([], $this->invoke($display, 'getThreadedOrdering'));
        $this->assertSame([], $this->invoke($display, 'getThreadedOrdering', 0, ['edge']));
    }

    public function testTheDepthOfTheIndentedLayoutIsTheNumberOfEdgesWithoutTheFirstTwo(): void
    {
        $list  = $this->invoke($this->display(), 'getThreadedOrdering');
        $depth = array_map(static fn ($message) => \count($message->indent) - 2, $list);

        $this->assertSame([1 => 0, 2 => 1, 3 => 2, 4 => 1], $depth);
    }
}
