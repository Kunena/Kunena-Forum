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

namespace Kunena\Tests\Integration\Libraries\Kunena\Tools;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Tools\KunenaInactiveUsers;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionProperty;
use RuntimeException;

/**
 * The tool "Inactive users" changes at most one batch of accounts in a run, and asks to run it again for the others: every run
 * took the first batch of the list again (the accounts it had just changed), so the accounts after it were never reached. And
 * when the roles could not be read, every account was listed, the staff too.
 */
class KunenaInactiveUsersBatchTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    private array $userColumns = [];

    public function getSchemasToLoad(): array
    {
        return ['kunena', 'joomla'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The tool reads the database of the container of Joomla, that of the tests here; no user is a moderator.
        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(UserFactoryInterface::class, fn () => $this->createStub(UserFactoryInterface::class));
        $access = $this->createStub(KunenaAccess::class);
        $access->method('getAdmins')->willReturn([]);
        $access->method('getModerators')->willReturn([]);
        $language = $this->createStub(Language::class);
        $language->method('getTag')->willReturn('en-GB');

        $this->saved = [
            'container' => Factory::$container,
            'language'  => Factory::$language,
            'access'    => $this->replace(KunenaAccess::class, 'instance', $access),
        ];
        Factory::$container = $container;
        Factory::$language  = $language;

        // The dates of the accounts are in the table of an installation of Joomla, not in the dataset of the tests.
        $columns = array_column($this->fetchAll('SHOW COLUMNS FROM #__users'), 'Field');

        foreach (['registerDate' => 'datetime NOT NULL', 'lastvisitDate' => 'datetime NULL'] as $column => $type) {
            if (!\in_array($column, $columns, true)) {
                $driver->setQuery("ALTER TABLE #__users ADD $column $type")->execute();
                $this->userColumns[] = $column;
            }
        }

        // One account more than a batch, registered long ago, never posted, each subscribed to a category.
        $users         = [];
        $subscriptions = [];

        for ($id = 1001; $id <= 1001 + KunenaInactiveUsers::BATCH; $id++) {
            $users[]         = ['id' => $id, 'name' => "User $id", 'username' => "user$id", 'email' => "user$id@example.invalid", 'params' => '', 'registerDate' => '2020-01-01 00:00:00'];
            $subscriptions[] = ['user_id' => $id, 'category_id' => 2, 'subscribed' => 1];
        }

        $this->insertRows('#__users', $users);
        $this->insertRows('#__kunena_user_categories', $subscriptions);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container = $this->saved['container'];
            Factory::$language  = $this->saved['language'];
            $this->replace(KunenaAccess::class, 'instance', $this->saved['access']);
        }

        foreach ($this->userColumns as $column) {
            $this->getDBDriver()->setQuery("ALTER TABLE #__users DROP $column")->execute();
        }

        parent::tearDown();
    }

    /**
     * Replaces a static property of a class: the old value.
     */
    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }

    private function subscribed(): int
    {
        return (int) $this->fetchAll('SELECT COUNT(*) AS n FROM #__kunena_user_categories WHERE subscribed <> 0')[0]['n'];
    }

    public function testTheSecondRunUnsubscribesTheAccountsAfterTheFirstBatch(): void
    {
        $criteria = KunenaInactiveUsers::criteria(['kind' => KunenaInactiveUsers::NEVER_POSTED, 'months' => 12]);
        $allowed  = static fn (int $id): bool => true;

        $first = KunenaInactiveUsers::applyOnSite($criteria, 'unsubscribe', $allowed);
        $this->assertSame(['done' => KunenaInactiveUsers::BATCH, 'skipped' => 0, 'left' => 1], $first);
        $this->assertSame(1, $this->subscribed());

        $second = KunenaInactiveUsers::applyOnSite($criteria, 'unsubscribe', $allowed);
        $this->assertSame(['done' => 1, 'skipped' => 0, 'left' => 0], $second);
        $this->assertSame(0, $this->subscribed());
    }

    public function testWithoutTheRolesNoAccountIsTouched(): void
    {
        $access = $this->createStub(KunenaAccess::class);
        $access->method('getAdmins')->willThrowException(new RuntimeException('No roles'));
        $this->replace(KunenaAccess::class, 'instance', $access);

        $criteria = KunenaInactiveUsers::criteria(['kind' => KunenaInactiveUsers::NEVER_POSTED, 'months' => 12]);

        $this->assertSame(0, KunenaInactiveUsers::findOnSite($criteria)['total']);
        $this->assertSame(0, KunenaInactiveUsers::applyOnSite($criteria, 'unsubscribe', static fn (int $id): bool => true)['done']);
        $this->assertSame(KunenaInactiveUsers::BATCH + 1, $this->subscribed());
    }
}
