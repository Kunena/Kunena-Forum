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

namespace Kunena\Tests\Unit\Libraries\Kunena\Tools;

use Kunena\Forum\Libraries\Tools\KunenaTopicIcons as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Topic icons and labels" reads the icon sets and the labels and counts their use.
 */
class KunenaTopicIconsTest extends UnitTestCase
{
    public function testTheNameOfAnIconSet(): void
    {
        $this->assertSame('default', T::setName(null));
        $this->assertSame('default', T::setName('/default'));
        $this->assertSame('my-set', T::setName('my-set/'));
        $this->assertSame('default', T::setName('../../etc'));
    }

    public function testTheIconsOfASet(): void
    {
        $xml = '<kunena-topicicons><icons type="user"><icon id="0" name="default" published="1"/><icon id="1" name="question" published="0"/></icons>'
            . '<icons type="system"><icon id="9" name="moved" published="1"/></icons></kunena-topicicons>';

        $this->assertSame([0 => ['name' => 'default', 'published' => true], 1 => ['name' => 'question', 'published' => false]], T::icons($xml));
        $this->assertNull(T::icons('not xml'));
    }

    public function testTheLabels(): void
    {
        $xml = '<kunena-topiclabels><labels type="qa"><label id="0" name="COM_KUNENA_LABELS_QUESTION"/><label id="4" name="COM_KUNENA_LABELS_SOLVED"/></labels></kunena-topiclabels>';

        $this->assertSame([0 => 'COM_KUNENA_LABELS_QUESTION', 4 => 'COM_KUNENA_LABELS_SOLVED'], T::labels($xml));
        $this->assertSame([], T::labels(''));
    }

    public function testTheUseOfTheIcons(): void
    {
        $usage = T::usage(
            [0 => ['name' => 'default', 'published' => true], 1 => ['name' => 'question', 'published' => false], 2 => ['name' => 'idea', 'published' => true]],
            [0 => 10, 1 => 2, 7 => 3]
        );

        $this->assertSame([10, 2, 0], array_column($usage['icons'], 'topics'));
        $this->assertSame([1 => 2, 7 => 3], $usage['unknown']);
    }
}
