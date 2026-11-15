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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Message;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\KunenaDiagnostics;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageBookmarks;
use Kunena\Tests\Integration\DBTestHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The bookmarks on the real table: a bookmark is saved and removed by the same toggle, the bookmarks of a page are loaded at once,
 * the bookmarks of one user are not the ones of another, and the diagnostics find the bookmarks of messages which are gone.
 */
class KunenaMessageBookmarksTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private ?Container $savedContainer = null;

    /**
     * @return  string[]
     */
    public function getSchemasToLoad(): array
    {
        return ['kunena'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // The container of Joomla gives the database of the tests to the class.
        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share('DatabaseDriver', static fn () => $driver);

        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;

        KunenaMessageBookmarks::clearCache();
        DBTestHelper::markDirty('#__kunena_bookmarks');
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
        }

        KunenaMessageBookmarks::clearCache();

        parent::tearDown();
    }

    public function testABookmarkIsSavedAndRemovedByTheSameToggle(): void
    {
        $this->assertTrue(KunenaMessageBookmarks::toggle(5, 42));
        $this->assertTrue(KunenaMessageBookmarks::isBookmarked(5, 42));

        $this->assertFalse(KunenaMessageBookmarks::toggle(5, 42));
        $this->assertFalse(KunenaMessageBookmarks::isBookmarked(5, 42));
    }

    public function testTheBookmarksOfAnotherUserAreNotMine(): void
    {
        KunenaMessageBookmarks::toggle(5, 7);

        $this->assertFalse(KunenaMessageBookmarks::isBookmarked(5, 42));
        $this->assertTrue(KunenaMessageBookmarks::isBookmarked(5, 7));
    }

    public function testAPageLoadsTheBookmarksOfItsMessages(): void
    {
        KunenaMessageBookmarks::toggle(5, 42);
        KunenaMessageBookmarks::toggle(7, 42);
        KunenaMessageBookmarks::clearCache();

        KunenaMessageBookmarks::load([5, 6, 7], 42);

        $this->assertTrue(KunenaMessageBookmarks::isBookmarked(5, 42));
        $this->assertFalse(KunenaMessageBookmarks::isBookmarked(6, 42));
        $this->assertTrue(KunenaMessageBookmarks::isBookmarked(7, 42));
    }

    public function testTheDiagnosticsFindAndDeleteTheBookmarksOfMessagesWhichAreGone(): void
    {
        KunenaMessageBookmarks::toggle(5, 42);
        KunenaMessageBookmarks::toggle(6, 42);

        // Only the message 6 exists.
        $this->insertRow('#__kunena_messages', ['id' => 6, 'parent' => 0, 'thread' => 1, 'catid' => 2, 'name' => 'A', 'userid' => 1,
            'email' => '', 'subject' => 'Six', 'time' => 1, 'ip' => '']);

        $this->assertContains('bookmarkOrphaned', KunenaDiagnostics::getList());
        $this->assertSame(1, KunenaDiagnostics::count('bookmarkOrphaned'));
        $this->assertTrue(KunenaDiagnostics::delete('bookmarkOrphaned'));

        KunenaMessageBookmarks::clearCache();

        $this->assertFalse(KunenaMessageBookmarks::isBookmarked(5, 42));
        $this->assertTrue(KunenaMessageBookmarks::isBookmarked(6, 42));
    }
}
