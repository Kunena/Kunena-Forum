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

namespace Kunena\Tests\Unit\Libraries\Kunena\Route;

use Kunena\Forum\Libraries\Route\KunenaFeedLink;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Two options of the feeds which nothing read: "RSS Specification" (the format of the feed: with Atom 1.0 the links of the forum
 * ask an Atom feed) and "FeedBurner" (the address of the feed of the forum at FeedBurner, which the link of the forum then
 * names). Every feed link was "type=rss" of the forum itself, whatever the two options said.
 */
class KunenaFeedLinkTest extends UnitTestCase
{
    public static function specifications(): array
    {
        return [
            'RSS 2.0'  => ['rss2.0', 'rss', 'application/rss+xml'],
            'RSS 0.91' => ['rss0.91', 'rss', 'application/rss+xml'],
            'RSS 1.0'  => ['rss1.0', 'rss', 'application/rss+xml'],
            'Atom 1.0' => ['atom1.0', 'atom', 'application/atom+xml'],
            'capitals' => [' Atom1.0 ', 'atom', 'application/atom+xml'],
            'nothing'  => ['', 'rss', 'application/rss+xml'],
            'unknown'  => ['json', 'rss', 'application/rss+xml'],
        ];
    }

    #[DataProvider('specifications')]
    public function testTheFormatOfTheOptionIsTheTypeOfTheLink(string $specification, string $type, string $mime): void
    {
        $this->assertSame($type, KunenaFeedLink::type($specification));
        $this->assertSame($mime, KunenaFeedLink::mime($specification));
    }

    public static function burners(): array
    {
        return [
            'a web address'       => ['https://feeds.feedburner.com/myforum', 'https://feeds.feedburner.com/myforum'],
            'with spaces around'  => ['  http://feeds.example.org/forum ', 'http://feeds.example.org/forum'],
            'empty'               => ['', null],
            'no web address'      => ['feeds.feedburner.com/myforum', null],
            'a script'            => ['javascript:alert(1)', null],
            'markup in it'        => ['https://example.org/"><script>', null],
            'a line break in it'  => ["https://example.org/feed\nX: y", null],
        ];
    }

    #[DataProvider('burners')]
    public function testTheAddressOfFeedBurnerIsAWebAddress(string $option, ?string $expected): void
    {
        $this->assertSame($expected, KunenaFeedLink::burner($option));
    }

    public function testNoLinkOfAFeedHasAFixedType(): void
    {
        foreach (
            [
                'src/site/src/Layout/Widget/WidgetFooter.php', 'src/site/src/Layout/Category/CategoryIndex.php',
                'src/libraries/kunena/src/Forum/Category/KunenaCategory.php', 'src/site/src/View/Common/HtmlView.php',
            ] as $file
        ) {
            $source = Files::read($file);

            $this->assertStringNotContainsString('type=rss', $source, $file);
            $this->assertStringContainsString('KunenaFeedLink::type()', $source, $file);
        }

        $this->assertStringContainsString('KunenaFeedLink::burner()', Files::read('src/site/src/Layout/Widget/WidgetFooter.php'));
    }
}
