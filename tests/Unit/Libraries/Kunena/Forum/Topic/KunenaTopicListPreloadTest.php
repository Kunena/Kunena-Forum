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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Topic;

use Closure;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicListPreload;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The lists of one page (the tabs of a profile) add what they need while the page prepares them; it is loaded once, together,
 * when the page closes the collection. A list which is prepared when nothing collects loads its own.
 */
class KunenaTopicListPreloadTest extends UnitTestCase
{
    /**
     * What the loader was given, one entry per loading.
     */
    private array $loads = [];

    protected function setUp(): void
    {
        $this->loads = [];
        $this->set('loader', Closure::fromCallable(fn (array $collected) => $this->loads[] = $collected));
    }

    protected function tearDown(): void
    {
        $values = [
            'depth'        => 0,
            'users'        => [],
            'topics'       => [],
            'messages'     => [],
            'locations'    => [],
            'loader'       => null,
            'surveying'    => false,
            'found'        => false,
            'surveyed'     => ['topics' => [], 'messages' => []],
            'surveyLoader' => null,
        ];

        foreach ($values as $name => $value) {
            $this->set($name, $value);
        }
    }

    /**
     * The survey of the lists of a page: the finders note what they find, the page knows which list must be prepared again,
     * and the topics and the messages of all the lists are loaded once, when the survey ends.
     */
    public function testASurveyNotesWhatTheListsFindAndLoadsItOnce(): void
    {
        $surveys = [];
        $this->set('surveyLoader', Closure::fromCallable(function (array $surveyed) use (&$surveys) {
            $surveys[] = $surveyed;
        }));

        $this->assertFalse(KunenaTopicListPreload::isSurveying());

        KunenaTopicListPreload::startSurvey();
        $this->assertTrue(KunenaTopicListPreload::isSurveying());

        // The first list found topics: it is prepared again. The second one found nothing (a list without these finders).
        KunenaTopicListPreload::noteFound('topics', [59, 50, '0']);
        $this->assertTrue(KunenaTopicListPreload::takeFound());
        $this->assertFalse(KunenaTopicListPreload::takeFound());

        KunenaTopicListPreload::noteFound('messages', [110, 109]);
        KunenaTopicListPreload::noteFound('topics', [50, 27]);
        $this->assertTrue(KunenaTopicListPreload::takeFound());

        $this->assertSame([], $surveys, 'nothing is loaded while the page surveys');

        KunenaTopicListPreload::stopSurvey();

        $this->assertFalse(KunenaTopicListPreload::isSurveying());
        $this->assertSame([['topics' => [59 => 59, 50 => 50, 27 => 27], 'messages' => [110 => 110, 109 => 109]]], $surveys);

        // A survey which found nothing loads nothing.
        KunenaTopicListPreload::startSurvey();
        KunenaTopicListPreload::stopSurvey();
        $this->assertCount(1, $surveys);
    }

    private function set(string $name, mixed $value): void
    {
        (new ReflectionProperty(KunenaTopicListPreload::class, $name))->setValue(null, $value);
    }

    private function topic(int $id): object
    {
        return (object) ['id' => $id];
    }

    public function testNothingIsCollectedWithoutACollection(): void
    {
        $this->assertFalse(KunenaTopicListPreload::isCollecting());

        // Closing a collection which was not opened loads nothing.
        KunenaTopicListPreload::flush();
        $this->assertSame([], $this->loads);
    }

    public function testTheListsOfAPageAreLoadedTogetherOnce(): void
    {
        KunenaTopicListPreload::start();
        $this->assertTrue(KunenaTopicListPreload::isCollecting());

        // Two lists (two tabs) with a topic and a user in common.
        $topic = $this->topic(5);
        KunenaTopicListPreload::add([$topic, $this->topic(6)], [46, 47], [105, 106], [105]);
        KunenaTopicListPreload::add([$this->topic(5), $this->topic(9)], [46, 0, -3], [109], ['110', 'x']);

        $this->assertSame([], $this->loads, 'nothing is loaded while the page prepares its lists');

        KunenaTopicListPreload::flush();

        $this->assertFalse(KunenaTopicListPreload::isCollecting());
        $this->assertCount(1, $this->loads);
        $this->assertSame([46 => 46, 47 => 47], $this->loads[0]['users']);
        $this->assertSame([5, 6, 9], array_keys($this->loads[0]['topics']));
        $this->assertSame([105 => 105, 106 => 106, 109 => 109], $this->loads[0]['messages']);
        $this->assertSame([105 => 105, 110 => 110], $this->loads[0]['locations']);

        // The collection is empty again: the next page (or the next collection) starts from nothing.
        $this->assertSame(['users' => [], 'topics' => [], 'messages' => [], 'locations' => []], KunenaTopicListPreload::collected());
    }

    public function testACollectionInsideAnotherOneWaitsForIt(): void
    {
        KunenaTopicListPreload::start();
        KunenaTopicListPreload::start();
        KunenaTopicListPreload::add([$this->topic(5)], [46]);

        KunenaTopicListPreload::flush();
        $this->assertTrue(KunenaTopicListPreload::isCollecting());
        $this->assertSame([], $this->loads);

        KunenaTopicListPreload::flush();
        $this->assertCount(1, $this->loads);
    }

    public function testAnEmptyCollectionLoadsNothing(): void
    {
        KunenaTopicListPreload::start();
        KunenaTopicListPreload::flush();

        $this->assertSame([], $this->loads);
    }
}
