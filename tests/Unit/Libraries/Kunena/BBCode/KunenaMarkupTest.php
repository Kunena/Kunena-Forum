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

use Kunena\Forum\Libraries\BBCode\KunenaMarkup;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Markdown and html in a message are converted to BBCode before the message is parsed.
 */
class KunenaMarkupTest extends UnitTestCase
{
    public static function markdown(): array
    {
        return [
            'bold and italic' => ['**bold** and *it*', '[b]bold[/b] and [i]it[/i]'],
            'a link'          => ['[x](https://e.com)', '[url=https://e.com]x[/url]'],
            'inline code'     => ['`code`', '[font=monospace]code[/font]'],
            'a heading'       => ['# Title', '[size=6][b]Title[/b][/size]'],
        ];
    }

    #[DataProvider('markdown')]
    public function testMarkdownBecomesBBCode(string $text, string $expected): void
    {
        $this->assertSame($expected, KunenaMarkup::toBBCode($text, true, false));
    }

    public function testAListBecomesBBCode(): void
    {
        $this->assertSame('[ul][li]a' . "\n" . '[/li][li]b' . "\n" . '[/li][/ul]', KunenaMarkup::toBBCode("- a\n- b", true, false));
    }

    public function testTheTextStaysWhenBothConversionsAreOff(): void
    {
        $this->assertSame('**not converted**', KunenaMarkup::toBBCode('**not converted**', false, false));
    }

    public function testAnEmptyTextStaysEmpty(): void
    {
        $this->assertSame('', KunenaMarkup::toBBCode('', true, true));
    }

    public function testQuotesInQuotesStopAfterTenLevels(): void
    {
        $this->assertSame(str_repeat('[quote]', 10) . '> x' . str_repeat('[/quote]', 10), KunenaMarkup::toBBCode(str_repeat('> ', 11) . 'x', true, false));

        // Thousands of quote marks used all the memory of the page.
        $before = memory_get_usage();
        KunenaMarkup::toBBCode(str_repeat('> ', 50000) . 'x', true, false);
        $this->assertLessThan(16 * 1024 * 1024, memory_get_peak_usage() - $before);
    }

    public static function addresses(): array
    {
        return [
            'https'               => ['https://example.com/a?b=1', 'https://example.com/a?b=1'],
            'http'                => ['http://x.org', 'http://x.org'],
            'mail'                => ['mailto:a@b.c', 'mailto:a@b.c'],
            'a path of the site'  => ['/forum/topic/1', '/forum/topic/1'],
            'spaces around'       => ['  https://ok.com  ', 'https://ok.com'],
            'no protocol address' => ['//evil.com', null],
            'javascript'          => ['javascript:alert(1)', null],
            'data'                => ['data:text/html,x', null],
            'ftp'                 => ['ftp://x.com', null],
            'a space inside'      => ['https://a.com/x y', null],
            'a quote inside'      => ['https://a.com/"x', null],
            'empty'               => ['', null],
        ];
    }

    #[DataProvider('addresses')]
    public function testSafeUrl(string $url, ?string $expected): void
    {
        $this->assertSame($expected, KunenaMarkup::safeUrl($url));
    }
}
