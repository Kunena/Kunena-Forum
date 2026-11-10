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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum;

use Kunena\Forum\Libraries\Forum\Topic\Rate\KunenaRateStars;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The stars of the rating of a topic. The rating is the average of the stored values and the value of the request was stored as
 * it came, so one member decided the rating of a topic alone with a million (or a negative number).
 */
class KunenaRateStarsTest extends UnitTestCase
{
    public static function stars(): array
    {
        return [[1], [2], [3], [4], [5], ['1'], ['5']];
    }

    #[DataProvider('stars')]
    public function testOneToFiveStarsAreKept(mixed $value): void
    {
        $this->assertSame((int) $value, KunenaRateStars::clean($value));
    }

    public static function noStars(): array
    {
        return [
            'zero'          => [0],
            'six'           => [6],
            'a million'     => [1000000],
            'negative'      => [-5],
            'text'          => ['five'],
            'number, text'  => ['5 stars'],
            'two digits'    => ['05'],
            'a fraction'    => [4.5],
            'fraction text' => ['4.5'],
            'empty'         => [''],
            'null'          => [null],
            'true'          => [true],
            'a list'        => [[5]],
            'a line more'   => ["5\n"],
        ];
    }

    #[DataProvider('noStars')]
    public function testAnythingElseIsNoRating(mixed $value): void
    {
        $this->assertNull(KunenaRateStars::clean($value));
    }

    public function testTaskChecksTheStarsBeforeTheyAreStored(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertSame(1, preg_match('/public function setrate\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $clean = strpos($m[1], 'KunenaRateStars::clean(');

        $this->assertIsInt($clean);
        $this->assertLessThan(strpos($m[1], '$rate->rate'), $clean);
        $this->assertMatchesRegularExpression('/if \(\$starid === null\) \{\s*throw new RuntimeException\(/', $m[1]);
        $this->assertStringContainsString('$ratedTopic->category_id', $m[1], 'The category is the one of the topic.');
    }
}
