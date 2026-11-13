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

namespace Kunena\Tests\Unit\Component\Site\Controller\Application\Misc\Initial;

use Kunena\Forum\Site\Controller\Application\Misc\Initial\MiscDisplay;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The custom content page gets the Open Graph and Twitter Card tags of the recent topics page, with a title and a description
 * which fall back to the board title (issue #7598).
 */
class MiscDisplayTest extends UnitTestCase
{
    private const URL = 'https://example.com/help';

    private const IMAGE = 'https://example.com/media/kunena/email/hero-wide.png';

    protected function setUp(): void
    {
        $this->requireJoomla();
    }

    private function tags(string $pageTitle, string $metaDescription, string $boardTitle = 'Board', string $url = self::URL, string $image = self::IMAGE): array
    {
        return $this->invoke(MiscDisplay::class, 'getSocialMetaData', $pageTitle, $metaDescription, $boardTitle, $url, $image);
    }

    private function values(array $tags): array
    {
        return array_column($tags, 1, 0);
    }

    public function testAllNineTagsInOrder(): void
    {
        $this->assertSame([
            ['og:url', self::URL, 'property'],
            ['og:type', 'website', 'property'],
            ['og:title', 'Help', 'property'],
            ['og:description', 'About the forum', 'property'],
            ['og:image', self::IMAGE, 'property'],
            ['twitter:card', 'summary', 'name'],
            ['twitter:title', 'Help', 'name'],
            ['twitter:description', 'About the forum', 'name'],
            ['twitter:image', self::IMAGE, 'name'],
        ], $this->tags('Help', 'About the forum'));
    }

    public function testTheTitleAndDescriptionFallBackToTheBoardTitle(): void
    {
        foreach (['', "  \n ", '<p> </p>'] as $empty) {
            $values = $this->values($this->tags($empty, $empty));

            $this->assertSame('Board', $values['og:title']);
            $this->assertSame('Board', $values['twitter:title']);
            $this->assertSame('Board', $values['og:description']);
            $this->assertSame('Board', $values['twitter:description']);
        }

        $values = $this->values($this->tags('', 'About the forum'));

        $this->assertSame('Board', $values['og:title']);
        $this->assertSame('About the forum', $values['og:description']);

        $values = $this->values($this->tags('Help', ''));

        $this->assertSame('Help', $values['og:title']);
        $this->assertSame('Board', $values['twitter:description']);
    }

    public function testTheValuesArePlainText(): void
    {
        $values = $this->values($this->tags("<b>Tom &amp; Jerry</b>\n  help", "<p>Q&amp;A\t about &quot;it&quot;</p>"));

        $this->assertSame('Tom & Jerry help', $values['og:title']);
        $this->assertSame('Tom & Jerry help', $values['twitter:title']);
        $this->assertSame('Q&A about "it"', $values['og:description']);
    }

    public function testNoImageTagsWithoutAnImage(): void
    {
        $names = array_column($this->tags('Help', 'About the forum', 'Board', self::URL, ''), 0);

        $this->assertSame(['og:url', 'og:type', 'og:title', 'og:description', 'twitter:card', 'twitter:title', 'twitter:description'], $names);
    }

    public function testNoEmptyTagsWithoutAnyTitle(): void
    {
        $tags = $this->tags('', '', '', '', '');

        $this->assertSame([
            ['og:type', 'website', 'property'],
            ['twitter:card', 'summary', 'name'],
        ], $tags);

        foreach ($this->tags('<p> </p>', ' ', " \n ") as $tag) {
            $this->assertNotSame('', $tag[1]);
            $this->assertNotContains($tag[0], ['og:title', 'twitter:title', 'og:description', 'twitter:description']);
        }
    }

    public function testTheCardAndTypeAreFixed(): void
    {
        foreach ([$this->tags('Help', 'About the forum'), $this->tags('', '', '', '', '')] as $tags) {
            $values = $this->values($tags);

            $this->assertSame('summary', $values['twitter:card']);
            $this->assertSame('website', $values['og:type']);
        }
    }
}
