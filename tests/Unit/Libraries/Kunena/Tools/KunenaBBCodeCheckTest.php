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

use Kunena\Forum\Libraries\BBCode\KunenaMarkup;
use Kunena\Forum\Libraries\Tools\KunenaBBCodeCheck as C;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use Nbbc\BBCode;

/**
 * The tool "BBCode check" finds the tags left open, the closing tags alone and the unknown tags, with the rules of the
 * parser of Kunena: a tag without an end tag is fine, the content of a verbatim tag is not read.
 */
class KunenaBBCodeCheckTest extends UnitTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        spl_autoload_register(static function (string $class): void {
            if (str_starts_with($class, 'Nbbc\\')) {
                $file = Files::path('src/libraries/kunena/External/Nbbc/src/' . substr($class, 5) . '.php');

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });
    }

    private static function tags(): array
    {
        return C::tags([
            'b'       => [],
            'quote'   => [],
            'url'     => [],
            'code'    => ['content' => BBCode::BBCODE_VERBATIM],
            'br'      => ['end_tag' => BBCode::BBCODE_PROHIBIT],
            '*'       => ['end_tag' => BBCode::BBCODE_OPTIONAL],
            'list'    => [],
            'private' => ['content' => BBCode::BBCODE_REQUIRED],
        ]);
    }

    public function testATagWhichKunenaWritesAloneIsFine(): void
    {
        $this->assertSame([], C::check('[PRIVATE=461753479935]', self::tags()));
    }

    public function testGoodBBCodeHasNoProblem(): void
    {
        $this->assertSame([], C::check('[b]bold[/b] [quote=jane]hi [url=https://a.example]a[/url][/quote] line[br]next', self::tags()));
        $this->assertSame([], C::check('[list][*]one[*]two[/list]', self::tags()));
        $this->assertSame([], C::check('[B]case[/b]', self::tags()));
    }

    public function testATagLeftOpen(): void
    {
        $this->assertSame([[C::UNCLOSED, 'quote']], C::check('[quote]never closed', self::tags()));
        $this->assertSame([[C::UNCLOSED, 'b']], C::check('[quote][b]bold[/quote]', self::tags()));
    }

    public function testAClosingTagAlone(): void
    {
        $this->assertSame([[C::STRAY, 'b']], C::check('text[/b]', self::tags()));
    }

    public function testTheContentOfAVerbatimTagIsNotRead(): void
    {
        $this->assertSame([], C::check('[code][b]not a tag[/quote][/code]', self::tags()));
        $this->assertSame([], C::check('[CODE]x[/code] [b]y[/b]', self::tags()));
    }

    /**
     * The parser shows a [code] without its [/code] as text and reads the rest as usual: "the [code] tag" in a sentence
     * is fine, a [b] after it is still read.
     */
    public function testAVerbatimTagWithoutItsClosingTagIsText(): void
    {
        $this->assertSame([], C::check('Code highlighting in [code] blocks', self::tags()));
        $this->assertSame([[C::UNCLOSED, 'b']], C::check('[code][b]', self::tags()));
        $this->assertSame([], C::check('in [code] blocks, then [code]x[/code]', self::tags()));
    }

    /**
     * The site checks the text as the parser gets it, Markdown converted: the image of Markdown "![img](...)" is no [img]
     * tag which is not closed.
     */
    public function testAMarkdownImageIsNoTag(): void
    {
        $tags = self::tags() + C::tags(['img' => ['content' => BBCode::BBCODE_VERBATIM]]);
        $text = 'A picture ![img](https://www.example.org/a.png) and [b]bold[/b]';

        $this->assertSame([], C::check(KunenaMarkup::toBBCode($text, true, false), $tags));
    }

    /**
     * As the parser: text in square brackets which starts with the name of a tag is no tag; a tag with parameters is one.
     */
    public function testBracketsWithWordsAfterATagNameAreText(): void
    {
        $this->assertSame([], C::check('[Quote of the day] and [Hide the replies for guests](x.md)', self::tags()));
        $this->assertSame([[C::UNCLOSED, 'quote']], C::check('[quote name=jane]hi', self::tags()));
        $this->assertSame([[C::UNCLOSED, 'quote']], C::check('[quote="jane"]hi', self::tags()));
        $this->assertSame([], C::check('[quote = "jane" ]hi[/quote]', self::tags()));
    }

    public function testAnUnknownTagCountsOnlyWithItsClosingTag(): void
    {
        $this->assertSame([], C::check('a footnote [1] and [sic] in text', self::tags()));
        $this->assertSame([[C::UNKNOWN, 'marquee']], C::check('[marquee]old[/marquee]', self::tags()));
    }

    public function testTheTagsOfKunenaAreRead(): void
    {
        $tags = self::tags();

        $this->assertTrue($tags['br']['void']);
        $this->assertTrue($tags['*']['void']);
        $this->assertTrue($tags['code']['verbatim']);
        $this->assertFalse($tags['b']['void']);
    }
}
