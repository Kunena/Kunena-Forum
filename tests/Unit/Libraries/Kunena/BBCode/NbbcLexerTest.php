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

namespace Kunena\Tests\Unit\Libraries\Kunena\BBCode;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use Nbbc\BBCode;
use Nbbc\BBCodeException;
use Nbbc\BBCodeLexer;

/**
 * The lexer of the parser Nbbc (Kunena 7.1): an unclosed [ followed by many quotes made the pattern of a tag backtrack until PCRE
 * failed (#10341), and the lexer then threw BBCodeException, a class which did not exist (#10332).
 */
class NbbcLexerTest extends UnitTestCase
{
    protected function setUp(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        spl_autoload_register(static function ($class) {
            if (strpos($class, 'Nbbc\\') === 0) {
                $file = Files::path('src/libraries/kunena/External/Nbbc/src/' . substr($class, 5) . '.php');

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });
    }

    public function testTheExceptionOfTheLexerExists(): void
    {
        $this->assertTrue(class_exists(BBCodeException::class));
        $this->assertTrue(is_subclass_of(BBCodeException::class, \Exception::class));
    }

    public function testAnUnclosedTagWithManyQuotesIsText(): void
    {
        $text  = 'Hello [b ' . str_repeat('"a" ', 20000) . 'world';
        $lexer = new BBCodeLexer($text);
        $out   = '';

        while ($lexer->nextToken() !== BBCode::BBCODE_EOI) {
            $out .= $lexer->text;
        }

        $this->assertSame($text, $out);
        $this->assertSame(PREG_NO_ERROR, preg_last_error());
    }

    private static function html(string $text): string
    {
        return trim((string) preg_replace('/\s+/', ' ', (new BBCode())->parse($text)));
    }

    /**
     * Text in square brackets which starts with the name of a tag is text: "[Quote of the day]" became a quote, and the
     * Markdown link "[Hide the replies for guests](...)" the [hide] of Kunena. Words after the name of a tag without "="
     * are no parameters of Kunena: its tags take "name=value" or "=value".
     */
    public function testBracketsWithWordsAfterATagNameAreText(): void
    {
        $this->assertSame('[Quote of the day] text', self::html('[Quote of the day] text'));
        $this->assertSame('[Hide the replies for guests, per category](features/hide.md)', self::html('[Hide the replies for guests, per category](features/hide.md)'));

        $lexer = new BBCodeLexer('[quote of the day]');
        $this->assertSame(BBCode::BBCODE_TEXT, $lexer->nextToken());
        $this->assertSame('[quote of the day]', $lexer->text);
    }

    public function testTagsWithParametersAreStillTags(): void
    {
        $this->assertStringContainsString('bbcode_quote', self::html('[quote]hi[/quote]'));
        $this->assertStringContainsString('jane wrote:', self::html('[quote="jane"]hi[/quote]'));
        $this->assertStringContainsString('jane wrote:', self::html('[quote name=jane]hi[/quote]'));
        $this->assertStringContainsString('jane wrote:', self::html('[quote = "jane" ]hi[/quote]'));
        $this->assertStringContainsString('href="https://a.example"', self::html('[url=https://a.example]a[/url]'));
        $this->assertStringContainsString('<b>x</b>', self::html('[b ]x[/b ]'));
        // A default value of several words, as [font=Times New Roman size=4] of Nbbc.
        $this->assertStringContainsString('Times New Roman', self::html('[font=Times New Roman size=4]x[/font]'));
        // A word after a parameter stays a part of the tag: only a word right after the name makes it text.
        $this->assertStringContainsString('bbcode_quote', self::html('[quote name=jane extra]hi[/quote]'));
    }
}
