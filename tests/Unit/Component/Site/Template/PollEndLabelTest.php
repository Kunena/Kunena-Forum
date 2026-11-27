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

namespace Kunena\Tests\Unit\Component\Site\Template;

use DateTimeImmutable;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The title of a poll says "runs until" or "was ended": the layouts compared the whole days left with 1, so on its last day a
 * poll was "ended" while it still took votes. The interval is inverted only once the end has passed. (Agora uses the layouts of
 * Aurelia.)
 */
class PollEndLabelTest extends UnitTestCase
{
    public function testTheLastDayOfAPollIsNoEnd(): void
    {
        $now = new DateTimeImmutable('2026-06-15 12:00:00');

        // Ten hours left: 0 whole days, but not ended.
        $this->assertSame('+0', $now->diff(new DateTimeImmutable('2026-06-15 22:00:00'))->format('%R%a'));
        $this->assertSame(0, $now->diff(new DateTimeImmutable('2026-06-15 22:00:00'))->invert);
        $this->assertSame(1, $now->diff(new DateTimeImmutable('2026-06-15 02:00:00'))->invert);

        foreach (['results', 'vote'] as $layout) {
            $source = Files::read("src/site/template/aurelia/layouts/topic/poll/{$layout}/default.php");

            $this->assertStringContainsString('if (!$this->intervalTimeToLive->invert) {', $source, $layout);
            $this->assertStringNotContainsString("format('%R%a') >= 1", $source, $layout);
        }
    }
}
