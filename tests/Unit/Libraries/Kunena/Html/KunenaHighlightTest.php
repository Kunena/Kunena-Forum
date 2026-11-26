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

namespace Kunena\Tests\Unit\Libraries\Kunena\Html;

use Kunena\Forum\Libraries\Html\KunenaHighlight as H;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The words of a search are marked in the text of the HTML only, with the case of the text.
 */
class KunenaHighlightTest extends UnitTestCase
{
    private const MARK = '<mark class="kunena-searchword">';

    public function testAWordInTheText(): void
    {
        $this->assertSame('The ' . self::MARK . 'Forum</mark> of ' . self::MARK . 'forums</mark>', H::html('The Forum of forums', ['forum', 'forums']));
    }

    public function testNotInATagAnAttributeOrAnEntity(): void
    {
        $html = '<a href="https://example.org/amp" title="amp">Tom &amp; Jerry</a>';

        $this->assertSame('<a href="https://example.org/amp" title="amp">Tom &amp; Jerry</a>', H::html($html, ['amp']));
        $this->assertSame('<a href="https://example.org/amp" title="amp">' . self::MARK . 'Tom</mark> &amp; Jerry</a>', H::html($html, ['tom']));
    }

    public function testNotInCode(): void
    {
        $html = '<p>echo</p><pre><code>echo 1;</code></pre>';

        $this->assertSame('<p>' . self::MARK . 'echo</mark></p><pre><code>echo 1;</code></pre>', H::html($html, ['echo']));
    }

    public function testAWordWithTheSignsOfARegularExpression(): void
    {
        $this->assertSame('a ' . self::MARK . 'c++</mark> b', H::html('a c++ b', ['c++']));
    }

    public function testAWordWithHtmlIsFoundAsTextAndStaysEscaped(): void
    {
        // The text "<b>" of a post is &lt;b&gt; in the HTML: a search for <b> marks it, and the mark has no tag of the search in it.
        $this->assertSame('x ' . self::MARK . '&lt;b&gt;</mark> y', H::html('x &lt;b&gt; y', ['<b>']));
        $this->assertSame('<b>bold</b>', H::html('<b>bold</b>', ['<b>']));
    }

    public function testUnicode(): void
    {
        $this->assertSame('Één ' . self::MARK . 'Café</mark>', H::html('Één Café', ['café']));
    }

    public function testNothingToMark(): void
    {
        $this->assertSame('<p>text</p>', H::html('<p>text</p>', []));
        $this->assertSame('<p>text</p>', H::html('<p>text</p>', ['', '  ']));
        $this->assertSame('', H::html('', ['text']));
    }
}
