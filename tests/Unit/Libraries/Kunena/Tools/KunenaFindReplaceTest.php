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

use Kunena\Forum\Libraries\Tools\KunenaFindReplace as F;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Find and replace" replaces plain text (never a pattern), with or without the case, and refuses a search too
 * short or a replacement which changes nothing.
 */
class KunenaFindReplaceTest extends UnitTestCase
{
    public function testReplaceWithTheCase(): void
    {
        $this->assertSame(['[img]https://a.example/x.png[/img] HTTP://a.example', 1], F::replace('[img]http://a.example/x.png[/img] HTTP://a.example', 'http://', 'https://', true));
    }

    public function testReplaceWithoutTheCase(): void
    {
        $this->assertSame(['https://a https://b', 2], F::replace('http://a HTTP://b', 'http://', 'https://', false));
    }

    public function testTheTextIsNoPattern(): void
    {
        $this->assertSame(['a.b x', 0], F::replace('a.b x', 'a?b', 'z', false));
        $this->assertSame(['a z', 1], F::replace('a.b z', '.b', '', false));
        // A replacement with $1 or \1 is kept as it is, not read as a group of a pattern.
        $this->assertSame(['$1 and yy', 1], F::replace('xx and yy', 'xx', '$1', false));
        $this->assertSame(['\\1 and yy', 1], F::replace('xx and yy', 'XX', '\\1', false));
    }

    public function testUnicodeWithoutTheCase(): void
    {
        $this->assertSame(['x x', 2], F::replace('Été été', 'été', 'x', false));
        $this->assertSame(['x ÄBC', 1], F::replace('äbc ÄBC', 'äbc', 'x', true));
    }

    public function testCheckRefusesAShortSearchAndNoChange(): void
    {
        $this->assertSame('COM_KUNENA_TOOLS_REPLACE_ERROR_SHORT', F::check(F::options(['search' => ' ab ', 'replace' => 'x'])));
        $this->assertSame('COM_KUNENA_TOOLS_REPLACE_ERROR_SAME', F::check(F::options(['search' => 'abc', 'replace' => 'abc'])));
        $this->assertNull(F::check(F::options(['search' => 'abc', 'replace' => ''])));
    }

    public function testTheSnippetShowsTheMatchWithItsSurroundings(): void
    {
        $text = str_repeat('a', 100) . 'NEEDLE' . str_repeat('b', 100);

        $this->assertSame('…' . str_repeat('a', 10) . 'NEEDLE' . str_repeat('b', 10) . '…', F::snippet($text, 'needle', false, 10));
        $this->assertSame(str_repeat('a', 20), F::snippet($text, 'needle', true, 10));
    }
}
