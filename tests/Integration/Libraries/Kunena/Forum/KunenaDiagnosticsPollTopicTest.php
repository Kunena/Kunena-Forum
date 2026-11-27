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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\KunenaDiagnostics;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * Diagnostics, "poll topic mismatch": a poll which names another topic than the one which shows it. An old split gave the poll to
 * the new topic and left the poll naming the old one. The only action was the delete, which deleted the poll which the new topic
 * shows. The delete keeps such a poll and a fix gives it its topic; a poll which no topic shows is still deleted.
 */
class KunenaDiagnosticsPollTopicTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private mixed $savedContainer = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;

        // The poll 1 names the topic 10, which no longer has it; the topic 11 (split from it) shows it. The poll 2 names the topic
        // 12, which does not have it, and no topic shows it.
        $this->insertRows('#__kunena_topics', [
            ['id' => 10, 'category_id' => 2, 'subject' => 'Old', 'poll_id' => 0],
            ['id' => 11, 'category_id' => 2, 'subject' => 'Split', 'poll_id' => 1],
            ['id' => 12, 'category_id' => 2, 'subject' => 'Other', 'poll_id' => 0],
        ]);
        $this->insertRows('#__kunena_polls', [
            ['id' => 1, 'title' => 'Shown', 'threadid' => 10, 'polltimetolive' => '1000-01-01 00:00:00'],
            ['id' => 2, 'title' => 'Lost', 'threadid' => 12, 'polltimetolive' => '1000-01-01 00:00:00'],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
        }

        parent::tearDown();
    }

    private function polls(): array
    {
        return array_map('intval', array_column($this->fetchAll('SELECT id, threadid FROM #__kunena_polls ORDER BY id'), 'threadid', 'id'));
    }

    public function testTheDeleteKeepsAPollWhichATopicShows(): void
    {
        $this->assertSame(2, KunenaDiagnostics::count('pollTopicMismatch'));

        KunenaDiagnostics::delete('pollTopicMismatch');

        $this->assertSame([1 => 10], $this->polls());
    }

    public function testTheFixGivesThePollTheTopicWhichShowsIt(): void
    {
        $this->assertTrue(KunenaDiagnostics::canFix('pollTopicMismatch'));

        KunenaDiagnostics::fix('pollTopicMismatch');

        $this->assertSame([1 => 11, 2 => 12], $this->polls());
    }
}
