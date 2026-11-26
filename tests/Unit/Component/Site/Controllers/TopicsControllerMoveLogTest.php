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

namespace Kunena\Tests\Unit\Component\Site\Controllers;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The moderation log of a move of several topics: one entry for each topic which was moved, with the category it came from.
 * Once one topic was moved, every topic of the request got an entry, also the ones which were refused, and the category of an
 * entry was read after the move: the target, not the source. (The controller needs a request and a session: its code is read.)
 */
class TopicsControllerMoveLogTest extends UnitTestCase
{
    public function testOnlyTheMovedTopicsAreLoggedWithTheCategoryTheyCameFrom(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicsController.php');

        $this->assertSame(1, preg_match('/public function move\(\).*?\n    \}\n/s', $source, $m));

        $read  = strpos($m[0], '$source = $topic->getCategory();');
        $move  = strpos($m[0], 'if ($topic->move($target) === false) {');
        $moved = strpos($m[0], '$moved[] = [$topic, $source];');

        $this->assertIsInt($read, 'The category of the topic is read.');
        $this->assertIsInt($move);
        $this->assertIsInt($moved);
        $this->assertLessThan($move, $read, 'Before the move.');
        $this->assertLessThan($moved, $move, 'A topic counts as moved after its move.');

        $this->assertStringContainsString('foreach ($moved as [$topic, $source]) {', $m[0]);
        $this->assertSame(1, preg_match('/foreach \(\$moved as \[\$topic, \$source\]\) \{\s*KunenaLog::log\((.*?)\);/s', $m[0], $log));
        $this->assertStringContainsString('$source,', $log[1]);
        $this->assertStringNotContainsString('$topic->getCategory()', $log[1]);
    }
}
