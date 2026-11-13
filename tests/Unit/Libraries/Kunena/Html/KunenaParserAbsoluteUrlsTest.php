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

use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The root-relative URLs of the text of a feed get the host in front of them: Joomla put the base URL with the folder of
 * the site in front of them, and the folder appeared twice (a protected attachment of a site in a subfolder).
 */
class KunenaParserAbsoluteUrlsTest extends UnitTestCase
{
    public static function texts(): array
    {
        return [
            'an attachment of a site in a folder' => [
                '<img src="/develop/forum/attachment/36?format=raw" />',
                '<img src="http://kunena.test/develop/forum/attachment/36?format=raw" />',
            ],
            'a link of a site in the root' => [
                '<a href="/forum/topic/1">x</a>',
                '<a href="http://kunena.test/forum/topic/1">x</a>',
            ],
            'the name of the attribute in capitals' => [
                '<IMG SRC="/forum/attachment/36" />',
                '<IMG SRC="http://kunena.test/forum/attachment/36" />',
            ],
            'several attributes, &amp; kept' => [
                '<a href="/x?a=1&amp;b=2"><img src="/y" alt="" /></a>',
                '<a href="http://kunena.test/x?a=1&amp;b=2"><img src="http://kunena.test/y" alt="" /></a>',
            ],
            'an absolute URL'             => ['<img src="http://other.test/a.png" />', '<img src="http://other.test/a.png" />'],
            'an absolute URL of the site' => ['<img src="https://kunena.test/develop/a.png" />', '<img src="https://kunena.test/develop/a.png" />'],
            'a protocol-relative URL'     => ['<img src="//cdn.test/a.png" />', '<img src="//cdn.test/a.png" />'],
            'a mailto link'               => ['<a href="mailto:a@b.c">a</a>', '<a href="mailto:a@b.c">a</a>'],
            'a data URL'                  => ['<img src="data:image/png;base64,AA" />', '<img src="data:image/png;base64,AA" />'],
            'an anchor'                   => ['<a href="#post1">a</a>', '<a href="#post1">a</a>'],
            'a relative path'             => ['<img src="images/a.png" />', '<img src="images/a.png" />'],
            'a path outside an attribute' => ['see /develop/forum outside an attribute', 'see /develop/forum outside an attribute'],
        ];
    }

    #[DataProvider('texts')]
    public function testTheRootRelativeUrlsGetTheHost(string $html, string $expected): void
    {
        $this->assertSame($expected, KunenaParser::absoluteUrls($html, 'http://kunena.test'));
    }
}
