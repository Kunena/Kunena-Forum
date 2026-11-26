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
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\User\KunenaUserName;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The search of the users (the list of the users, the autocomplete of the user field, the mentions) finds a user by the name which
 * the forum shows of him only. It found him by any of his names: when the forum shows the real names, a part of a username (the
 * name with which he logs in) still found him, and searching letter by letter gave the username of each member.
 */
class KunenaUserNameSearchConditionTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const FIELD = 7;

    private const JOE = 50;

    private const ANN = 51;

    private const BOB = 52;

    /**
     * The values of the static properties which the test replaces, put back after the test.
     */
    private array $saved = [];

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

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        $this->saved = [
            'container' => Factory::$container,
            'config'    => $this->replace(KunenaConfig::class, 'instance', null),
            'field'     => $this->replace(KunenaUserName::class, 'displayNameField', null),
        ];

        Factory::$container = $container;

        // Joe and Bob have no choice; Ann chose to show her username. Bob has a nickname.
        $this->insertRows('#__users', [
            ['id' => self::JOE, 'name' => 'Joe Real', 'username' => 'loginjoe', 'email' => 'joe@example.com', 'params' => '{}'],
            ['id' => self::ANN, 'name' => 'Ann Real', 'username' => 'loginann', 'email' => 'ann@example.com', 'params' => '{"kunena_name":1}'],
            ['id' => self::BOB, 'name' => 'Bob Real', 'username' => 'loginbob', 'email' => 'bob@example.com', 'params' => '{}'],
        ]);
        $this->insertRow('#__fields', ['id' => self::FIELD, 'context' => 'com_users.user', 'state' => 1]);
        $this->insertRow('#__fields_values', ['field_id' => self::FIELD, 'item_id' => (string) self::BOB, 'value' => 'Bobby']);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container = $this->saved['container'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaUserName::class, 'displayNameField', $this->saved['field']);
        }

        parent::tearDown();
    }

    public function testTheRealNamesOfTheForumAreFoundAndNotTheUsernames(): void
    {
        $this->configure(KunenaUserName::REALNAME, false);

        $this->assertSame([], $this->find('login'), 'a part of the usernames finds nobody');
        $this->assertSame([self::JOE, self::ANN, self::BOB], $this->find('Real'), 'the real names (a nickname is shown only when the forum shows them)');
    }

    public function testTheUsernamesOfTheForumAreFoundAndNotTheRealNames(): void
    {
        $this->configure(KunenaUserName::USERNAME, false);

        $this->assertSame([], $this->find('Real'));
        $this->assertSame([self::JOE], $this->find('loginjoe'));
    }

    public function testTheNicknameIsFoundAndOtherwiseTheRealName(): void
    {
        $this->configure(KunenaUserName::DISPLAYNAME, false);

        $this->assertSame([self::BOB], $this->find('Bobby'));
        $this->assertSame([], $this->find('Bob Real'), 'Bob shows his nickname, not his real name');
        $this->assertSame([self::JOE], $this->find('Joe Real'), 'Joe has no nickname: his real name');
    }

    public function testTheChoiceOfAUserDecidesWhichOfHisNamesIsFound(): void
    {
        $this->configure(KunenaUserName::REALNAME, true);

        $this->assertSame([self::ANN], $this->find('login'), 'only Ann shows her username');
        $this->assertSame([], $this->find('Ann Real'));
        $this->assertSame([self::JOE], $this->find('Joe Real'));
    }

    /**
     * The option of the user list "Search from the beginning of the name" ("TODO: add strict search from the beginning of the
     * name"): the names which start with the text, not those which have it anywhere.
     */
    public function testAStrictSearchFindsTheNamesWhichStartWithTheText(): void
    {
        $this->configure(KunenaUserName::REALNAME, false);

        $this->assertSame([self::JOE, self::ANN, self::BOB], $this->find('Real'), 'anywhere: the end of the names');
        $this->assertSame([], $this->find('Real', true), 'from the start: no name starts with Real');
        $this->assertSame([self::ANN], $this->find('Ann', true));
        $this->assertSame([self::ANN], $this->find('Ann Real', true), 'the whole name starts with itself');
    }

    /**
     * The ids of the users which the condition finds, in order.
     */
    private function find(string $search, bool $fromStart = false): array
    {
        $db    = $this->getDBDriver();
        $query = $db->getQuery(true)
            ->select($db->quoteName('a.id'))
            ->from($db->quoteName('#__users', 'a'))
            ->where(KunenaUserName::getSearchCondition($search, 'a', false, $fromStart))
            ->order($db->quoteName('a.id'));

        return array_map('intval', $db->setQuery($query)->loadColumn());
    }

    /**
     * The settings of the forum: the name which it shows, whether a user may choose it, and the field of the nickname.
     */
    private function configure(int $type, bool $choice): void
    {
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, [
            'username'         => $type,
            'nameUserChoice'   => (int) $choice,
            'displayNameField' => self::FIELD,
        ]);

        $this->replace(KunenaConfig::class, 'instance', $config);
        $this->replace(KunenaUserName::class, 'displayNameField', null);
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
