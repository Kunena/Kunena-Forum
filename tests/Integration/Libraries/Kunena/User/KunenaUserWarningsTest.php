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

namespace Kunena\Tests\Integration\Libraries\Kunena\User;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\User\KunenaUserWarnings;
use Kunena\Tests\Integration\DBTestHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The warnings on the real table: a warning is saved with its expiry, the warnings of a user come the newest first with his active
 * points, and a warning is removed.
 */
class KunenaUserWarningsTest extends IntegrationTestCase implements DBTestInterface
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

        DBTestHelper::markDirty('#__kunena_warnings');
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
        }

        parent::tearDown();
    }

    public function testAWarningIsSavedWithItsExpiry(): void
    {
        $id      = KunenaUserWarnings::add(42, 7, 100, 3, '  Spam links  ', 1000, 30);
        $warning = KunenaUserWarnings::getWarning($id);

        $this->assertNotNull($warning);
        $this->assertSame(42, (int) $warning->user_id);
        $this->assertSame(7, (int) $warning->moderator_id);
        $this->assertSame(100, (int) $warning->message_id);
        $this->assertSame(3, (int) $warning->points);
        $this->assertSame('Spam links', $warning->reason);
        $this->assertSame(1000, (int) $warning->created_time);
        $this->assertSame(1000 + 30 * 86400, (int) $warning->expires_time);
    }

    public function testTheWarningsOfAUserAndHisActivePoints(): void
    {
        KunenaUserWarnings::add(42, 7, 0, 2, 'Old', 1000, 1);
        KunenaUserWarnings::add(42, 7, 0, 3, 'New', 500000, 30);
        KunenaUserWarnings::add(43, 7, 0, 5, 'Another user', 500000, 30);

        $warnings = KunenaUserWarnings::getWarnings(42);

        $this->assertSame(['New', 'Old'], array_map(static fn ($w) => $w->reason, $warnings));
        // At 500000 the first one (one day after 1000) expired.
        $this->assertSame(3, KunenaUserWarnings::activePoints($warnings, 500000));
    }

    public function testAWarningIsRemoved(): void
    {
        $id = KunenaUserWarnings::add(42, 7, 0, 2, 'Mistake', 1000, 30);

        KunenaUserWarnings::remove($id);

        $this->assertNull(KunenaUserWarnings::getWarning($id));
        $this->assertSame([], KunenaUserWarnings::getWarnings(42));
    }
}
