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

namespace Kunena\Tests\Unit\Libraries\Kunena\Layout;

use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The excerpt of a post for the tooltip of a link (Kunena 8.0): a topic list made it about four times a row, and of the first post
 * also for a link to the last post. It is made once in a request for each text.
 */
class KunenaLayoutExcerptTest extends UnitTestCase
{
    private ReflectionProperty $cache;

    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->cache = new ReflectionProperty(KunenaLayout::class, 'excerpts');
        $this->saved = $this->cache->getValue();
    }

    protected function tearDown(): void
    {
        $this->cache->setValue(null, $this->saved);
    }

    public function testAnExcerptWhichWasMadeIsNotMadeAgain(): void
    {
        // A value which stripBBCode() never gives: it can only come from the cache.
        $this->cache->setValue(null, [md5('[b]Hello[/b] world') => 'from the cache']);

        $this->assertSame('from the cache', KunenaLayout::excerpt('[b]Hello[/b] world'));
        $this->assertSame('from the cache', KunenaLayout::excerpt('[b]Hello[/b] world'));
    }

    public function testAnEmptyTextHasNoExcerpt(): void
    {
        $this->cache->setValue(null, []);

        $this->assertSame('', KunenaLayout::excerpt(''));
        $this->assertSame([], $this->cache->getValue());
    }

    public function testTheLinksOfTheTopicsUseTheExcerpt(): void
    {
        $source = Files::read('src/libraries/kunena/src/Layout/KunenaLayout.php');

        // getTopicLink(): one excerpt, of the post the link goes to; getLastPostLink(): the same.
        $this->assertStringContainsString('$title = self::excerpt((string) ($last ? $topic->last_post_message : $topic->first_post_message));', $source);
        $this->assertStringContainsString('$title   = self::excerpt((string) $message);', $source);
        $this->assertSame(1, substr_count($source, 'KunenaParser::stripBBCode('), 'Only excerpt() strips the text of a post.');
    }
}
