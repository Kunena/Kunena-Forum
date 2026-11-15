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

namespace Kunena\Tests\Unit\Libraries\Kunena\Icons;

use Kunena\Forum\Libraries\Icons\KunenaSvgIcons;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * An icon is read and parsed once in a request (Kunena 8.0): every button of every message asked for its icon again, and each time
 * the SVG file was read from the disk and parsed (a third of the time of a topic page).
 */
class KunenaSvgIconsTest extends UnitTestCase
{
    private ReflectionProperty $cache;

    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->cache = new ReflectionProperty(KunenaSvgIcons::class, 'loaded');
        $this->saved = $this->cache->getValue();
    }

    protected function tearDown(): void
    {
        $this->cache->setValue(null, $this->saved);
    }

    public function testAnIconWhichWasLoadedIsNotReadAgain(): void
    {
        // A value which no file has: it can only come from the cache.
        $this->cache->setValue(null, ['default|default|kunena-test-icon' => '<svg id="from-the-cache"></svg>']);

        $this->assertSame('<svg id="from-the-cache"></svg>', KunenaSvgIcons::loadsvg('kunena-test-icon'));
        $this->assertSame('<svg id="from-the-cache"></svg>', KunenaSvgIcons::loadsvg('kunena-test-icon'));
    }

    public function testTheGroupAndTheIconSetArePartOfTheKey(): void
    {
        $this->cache->setValue(null, [
            'default|default|star'           => '<svg id="default"></svg>',
            'systemtopicIcons|aurelia|star'  => '<svg id="system-aurelia"></svg>',
        ]);

        $this->assertSame('<svg id="default"></svg>', KunenaSvgIcons::loadsvg('star'));
        $this->assertSame('<svg id="system-aurelia"></svg>', KunenaSvgIcons::loadsvg('star', 'systemtopicIcons', 'aurelia'));
    }

    public function testAMissingIconIsEmptyAndKept(): void
    {
        $this->cache->setValue(null, []);

        $this->assertSame('', @KunenaSvgIcons::loadsvg('kunena-no-such-icon-' . uniqid()));
        $this->assertCount(1, $this->cache->getValue(), 'The empty result is kept: the missing file is not looked for again.');
        $this->assertSame('', KunenaSvgIcons::loadsvg(''));
    }
}
