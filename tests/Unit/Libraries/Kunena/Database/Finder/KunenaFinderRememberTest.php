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

namespace Kunena\Tests\Unit\Libraries\Kunena\Database\Finder;

use Kunena\Forum\Libraries\Database\Finder\KunenaFinder;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * A page which prepares its lists twice (the tabs of a profile: a survey, then for real) remembers the answers of the searches
 * of the finders while it does, so each search runs once; other pages do not remember them.
 */
class KunenaFinderRememberTest extends UnitTestCase
{
    protected function tearDown(): void
    {
        (new ReflectionProperty(KunenaFinder::class, 'remembered'))->setValue(null, null);
    }

    /**
     * The answer of a search with a key, and how many times the search ran.
     */
    private function answer(string $key, int &$runs, mixed $value): mixed
    {
        return static::invoke(KunenaFinder::class, 'rememberedAnswer', $key, static function () use (&$runs, $value) {
            $runs++;

            return $value;
        });
    }

    public function testWithoutRememberingEverySearchRuns(): void
    {
        $runs = 0;

        $this->assertSame([5, 6], $this->answer('find:SELECT a', $runs, [5, 6]));
        $this->assertSame([5, 6], $this->answer('find:SELECT a', $runs, [5, 6]));
        $this->assertSame(2, $runs);
    }

    public function testWhileRememberingASearchRunsOnce(): void
    {
        KunenaFinder::remember(true);
        $runs = 0;

        $this->assertSame([5, 6], $this->answer('find:SELECT a', $runs, [5, 6]));
        $this->assertSame([5, 6], $this->answer('find:SELECT a', $runs, [7]));
        $this->assertSame(1, $runs);

        // Another search (another list, another page of it) has its own answer; an empty answer is remembered too.
        $this->assertSame(3, $this->answer('count:SELECT a', $runs, 3));
        $this->assertSame([], $this->answer('find:SELECT b', $runs, []));
        $this->assertSame([], $this->answer('find:SELECT b', $runs, [1]));
        $this->assertSame(3, $runs);
    }

    public function testStoppingForgetsTheAnswers(): void
    {
        KunenaFinder::remember(true);
        $runs = 0;
        $this->answer('find:SELECT a', $runs, [5]);

        KunenaFinder::remember(false);
        $this->assertSame([8], $this->answer('find:SELECT a', $runs, [8]));

        // Remembering again starts from nothing.
        KunenaFinder::remember(true);
        $this->assertSame([9], $this->answer('find:SELECT a', $runs, [9]));
        $this->assertSame(3, $runs);
    }
}
