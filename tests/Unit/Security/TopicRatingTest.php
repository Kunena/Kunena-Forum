<?php

/**
 * Kunena Component
 *
 * The task that stores the rating of a topic took everything from the request: any number as the stars (the rating of the topic
 * is the average, so one member decided it alone with a million), the category for the check "are ratings allowed here" (any
 * category that allows them did), and a topic that the member may not read or that does not exist.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Security;

use Kunena\Forum\Libraries\Forum\Topic\Rate\KunenaRateStars;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

if (!\defined('_JEXEC')) {
    \define('_JEXEC', 1);
}

require_once __DIR__ . '/../../../src/libraries/kunena/src/Forum/Topic/Rate/KunenaRateStars.php';

class TopicRatingTest extends TestCase
{
    private const CONTROLLER = __DIR__ . '/../../../src/site/src/Controllers/TopicController.php';

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

    private function task(): string
    {
        $source = file_get_contents(self::CONTROLLER);

        $this->assertSame(1, preg_match('/public function setrate\(\)\s*\{(.*?)\r?\n    \}\r?\n/s', $source, $m));

        return $m[1];
    }

    public function testTopicIsCheckedBeforeAnythingIsStored(): void
    {
        $body = $this->task();

        $read = strpos($body, "!\$topic->exists() || !\$topic->isAuthorised('read')");
        $save = strpos($body, '$rate->save(');

        $this->assertIsInt($read, 'The topic exists and the member may read it.');
        $this->assertIsInt($save);
        $this->assertLessThan($save, $read);
        $this->assertLessThan(strpos($body, '$topic->save()'), $read);
        $this->assertLessThan($read, strpos($body, 'Session::checkToken('), 'The token is still checked first.');
    }

    public function testCategoryIsTheOneOfTheTopic(): void
    {
        $body = $this->task();

        $this->assertStringContainsString('!$topic->getCategory()->allowRatings', $body);
        $this->assertStringNotContainsString("'catid'", $body, 'No category is taken from the request.');
        $this->assertStringNotContainsString('KunenaCategoryHelper::get(', $body);
    }

    public function testStarsAreCheckedBeforeTheyAreStored(): void
    {
        $body = $this->task();

        $clean = strpos($body, 'KunenaRateStars::clean(');

        $this->assertIsInt($clean);
        $this->assertLessThan(strpos($body, '$rate->rate'), $clean);
        $this->assertMatchesRegularExpression('/if \(\$starid === null\) \{\s*throw new RuntimeException\(/', $body);
    }
}
