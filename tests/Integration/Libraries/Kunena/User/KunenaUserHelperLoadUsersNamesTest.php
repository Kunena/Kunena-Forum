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

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserName;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The nicknames of the users which a page loads come with one query, also of a user who was loaded before (the current user):
 * on the index of the forum the nickname of the current user, the author of a last post, cost a query of its own.
 */
class KunenaUserHelperLoadUsersNamesTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const FIELD = 7;

    private const ME = 46;

    private const AUTHOR = 49;

    /**
     * The values of the static properties which the test replaces, put back after the test.
     */
    private array $saved = [];

    /**
     * The users in memory, by id.
     */
    private array $users = [];

    public function getSchemasToLoad(): array
    {
        return ['kunena', 'fields', 'joomla'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The names read the database of the container of Joomla, and the field of the nickname from the configuration. Loading
        // the helper makes the current user, a guest here: his table needs the dispatcher, and he reads the avatars, the avatars
        // of Kunena itself (no plugin).
        $driver     = $this->getDBDriver();
        $dispatcher = new Dispatcher();
        $container  = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($dispatcher);

        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['displayNameField' => self::FIELD]);

        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'config'      => $this->replace(KunenaConfig::class, 'instance', $config),
            'avatars'     => $this->replace(KunenaAvatar::class, 'instance', new KunenaAvatar()),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;

        // Both users are in memory already, as the current user is when the index loads the authors of the last posts: loading
        // them reads no user from the database.
        $users = [];

        foreach ([self::ME, self::AUTHOR] as $userid) {
            $user           = (new ReflectionClass(KunenaUser::class))->newInstanceWithoutConstructor();
            $user->userid   = $userid;
            $users[$userid] = $user;
        }

        $this->users  = $users;
        $this->saved += [
            'me'        => $this->replace(KunenaUserHelper::class, '_me', null),
            'instances' => $this->replace(KunenaUserHelper::class, '_instances', $users),
            'names'     => $this->replace(KunenaUserName::class, 'displayNames', []),
            'field'     => $this->replace(KunenaUserName::class, 'displayNameField', null),
        ];

        $this->insertRow('#__fields', ['id' => self::FIELD, 'context' => 'com_users.user', 'state' => 1]);
        $this->insertRows('#__fields_values', [
            ['field_id' => self::FIELD, 'item_id' => (string) self::ME, 'value' => 'Boss'],
            ['field_id' => self::FIELD, 'item_id' => (string) self::AUTHOR, 'value' => 'Author'],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaAvatar::class, 'instance', $this->saved['avatars']);
            $this->replace(KunenaUserHelper::class, '_me', $this->saved['me']);
            $this->replace(KunenaUserHelper::class, '_instances', $this->saved['instances']);
            $this->replace(KunenaUserName::class, 'displayNames', $this->saved['names']);
            $this->replace(KunenaUserName::class, 'displayNameField', $this->saved['field']);
        }

        parent::tearDown();
    }

    public function testLoadingUsersInMemoryLoadsTheirNicknamesTogether(): void
    {
        KunenaUserHelper::loadUsers([self::AUTHOR, self::ME, 0]);

        // The nicknames came with the load: without the values in the database, they are still known (no query of their own).
        $this->getDBDriver()->setQuery('DELETE FROM ' . $this->getDBDriver()->quoteName('#__fields_values'))->execute();

        $this->assertSame('Boss', KunenaUserName::getDisplayName(self::ME));
        $this->assertSame('Author', KunenaUserName::getDisplayName(self::AUTHOR));
    }

    /**
     * The name of the current user is on every page (the menu): a page which loads other users (the authors of a list without
     * a topic of his) loads his nickname with theirs, not with a query of its own when the menu shows his name.
     */
    public function testTheNicknameOfTheCurrentUserComesWithTheOtherUsers(): void
    {
        $this->replace(KunenaUserHelper::class, '_me', $this->users[self::ME]);

        KunenaUserHelper::loadUsers([self::AUTHOR]);

        $this->getDBDriver()->setQuery('DELETE FROM ' . $this->getDBDriver()->quoteName('#__fields_values'))->execute();

        $this->assertSame('Boss', KunenaUserName::getDisplayName(self::ME));
    }

    /**
     * A user who is not there (deleted, or the id of an old link) is known after the load as one who does not exist: get() does
     * not read him again with a query of its own.
     */
    public function testAUserWhoIsNotThereIsKnownAsNotExisting(): void
    {
        $this->addUserDates();
        $this->insertRow('#__users', ['id' => 48, 'name' => 'Anna', 'username' => 'anna', 'email' => 'anna@example.com', 'params' => '{}']);

        $loaded = KunenaUserHelper::loadUsers([48, 999]);

        $this->assertSame([48], array_keys($loaded), 'only the user who is there is in the list');
        $this->assertTrue($loaded[48]->exists() || $loaded[48]->userid == 48);

        // Without the users in the database, get() still knows both: the one who is there, and the one who is not.
        $this->getDBDriver()->setQuery('DELETE FROM ' . $this->getDBDriver()->quoteName('#__users'))->execute();

        $this->assertSame('Anna', KunenaUserHelper::get(48)->name);
        $missing = KunenaUserHelper::get(999);
        $this->assertSame(999, (int) $missing->userid);
        $this->assertFalse($missing->exists());
    }

    /**
     * The table of the users of an older test database has no dates (the datasets of Joomla are made only when the table is not
     * there): the load of the users reads them.
     */
    private function addUserDates(): void
    {
        $db      = $this->getDBDriver();
        $columns = $db->getTableColumns('#__users');

        foreach (['registerDate', 'lastvisitDate'] as $column) {
            if (!isset($columns[$column])) {
                $db->setQuery('ALTER TABLE ' . $db->quoteName('#__users') . ' ADD ' . $db->quoteName($column) . ' datetime NULL DEFAULT NULL')->execute();
            }
        }
    }

    /**
     * Put a value in a static property of a class, and give back the value it had.
     */
    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }
}
