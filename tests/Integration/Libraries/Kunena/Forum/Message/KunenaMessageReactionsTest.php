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
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageReactions;
use Kunena\Tests\Integration\DBTestHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The reactions on the real table: one per user and message, the same one removes it, another one replaces it, every emoji is
 * counted apart (the column is binary: the collation of the forum compares many emoji as equal), and a page loads them at once.
 */
class KunenaMessageReactionsTest extends IntegrationTestCase implements DBTestInterface
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

        KunenaMessageReactions::clearCache();
        DBTestHelper::markDirty('#__kunena_reactions');
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
        }

        KunenaMessageReactions::clearCache();

        parent::tearDown();
    }

    public function testAReactionIsSaved(): void
    {
        $this->assertTrue(KunenaMessageReactions::toggle(5, 10, '👍'));

        $this->assertSame('👍', KunenaMessageReactions::getMine(5, 10));
        $this->assertSame(['👍' => 1], KunenaMessageReactions::getCounts(5));
    }

    public function testTheSameReactionAgainRemovesIt(): void
    {
        KunenaMessageReactions::toggle(5, 10, '👍');

        $this->assertFalse(KunenaMessageReactions::toggle(5, 10, '👍'));
        $this->assertNull(KunenaMessageReactions::getMine(5, 10));
        $this->assertSame([], KunenaMessageReactions::getCounts(5));
    }

    public function testAnotherReactionReplacesIt(): void
    {
        KunenaMessageReactions::toggle(5, 10, '👍');

        $this->assertTrue(KunenaMessageReactions::toggle(5, 10, '❤️'));
        $this->assertSame('❤️', KunenaMessageReactions::getMine(5, 10));
        $this->assertSame(['❤️' => 1], KunenaMessageReactions::getCounts(5));
        $this->assertSame(1, (int) $this->getDBDriver()->setQuery('SELECT COUNT(*) FROM #__kunena_reactions')->loadResult());
    }

    public function testEveryEmojiIsCountedApart(): void
    {
        KunenaMessageReactions::toggle(5, 10, '👍');
        KunenaMessageReactions::toggle(5, 11, '😂');
        KunenaMessageReactions::toggle(5, 12, '👍');
        KunenaMessageReactions::toggle(5, 13, '🎉');

        $counts = KunenaMessageReactions::getCounts(5);
        ksort($counts);
        $expected = ['👍' => 2, '😂' => 1, '🎉' => 1];
        ksort($expected);

        $this->assertSame($expected, $counts);
    }

    public function testAPageLoadsTheReactionsOfItsMessagesAndOfTheUser(): void
    {
        KunenaMessageReactions::toggle(5, 10, '👍');
        KunenaMessageReactions::toggle(5, 11, '👍');
        KunenaMessageReactions::toggle(6, 11, '😮');
        KunenaMessageReactions::clearCache();

        KunenaMessageReactions::load([5, 6, 7], 10);

        $this->assertSame(['👍' => 2], KunenaMessageReactions::getCounts(5));
        $this->assertSame(['😮' => 1], KunenaMessageReactions::getCounts(6));
        $this->assertSame([], KunenaMessageReactions::getCounts(7));
        $this->assertSame('👍', KunenaMessageReactions::getMine(5, 10));
        $this->assertNull(KunenaMessageReactions::getMine(6, 10));
    }

    public function testTheDiagnosticsFindAndDeleteTheReactionsOfMessagesWhichAreGone(): void
    {
        KunenaMessageReactions::toggle(5, 10, '👍');
        KunenaMessageReactions::toggle(6, 10, '👍');

        // Only the message 6 exists.
        $this->insertRow('#__kunena_messages', ['id' => 6, 'parent' => 0, 'thread' => 1, 'catid' => 2, 'name' => 'A', 'userid' => 1,
            'email' => '', 'subject' => 'Six', 'time' => 1, 'ip' => '']);

        $this->assertContains('reactionOrphaned', KunenaDiagnostics::getList());
        $this->assertSame(1, KunenaDiagnostics::count('reactionOrphaned'));
        $this->assertTrue(KunenaDiagnostics::delete('reactionOrphaned'));
        $this->assertSame(
            [6],
            array_map('intval', $this->getDBDriver()->setQuery('SELECT message_id FROM #__kunena_reactions')->loadColumn())
        );
    }
}
