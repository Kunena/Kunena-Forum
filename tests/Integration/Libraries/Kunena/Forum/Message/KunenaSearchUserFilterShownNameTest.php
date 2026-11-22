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
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Message\KunenaSearchUserFilter;
use Kunena\Forum\Libraries\User\KunenaUserName;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The search of the messages of a user (the user field of the search form, also with "exact name") found the messages of a member by
 * any of his names: a search for a login name which the forum keeps for itself (it shows the real names) gave the messages of its
 * member, and so told which member has that login name. The accounts are found by the name which the forum shows of them only.
 */
class KunenaSearchUserFilterShownNameTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const FIELD = 7;

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
            'names'     => $this->replace(KunenaUserName::class, 'displayNames', []),
            'field'     => $this->replace(KunenaUserName::class, 'displayNameField', null),
        ];

        Factory::$container = $container;

        // 50: Jelle Kok, login "jkok8", shows nothing of his own. 51: login "anna", real name "Anna Smit", nickname "Moonlight".
        // 52: login "piet", no real name. 53: login "noor", real name "Noor", chose to show his login name.
        $this->insertRows('#__users', [
            ['id' => 50, 'name' => 'Jelle Kok', 'username' => 'jkok8', 'email' => 'a@example.com', 'params' => ''],
            ['id' => 51, 'name' => 'Anna Smit', 'username' => 'anna', 'email' => 'b@example.com', 'params' => '{}'],
            ['id' => 52, 'name' => ' ', 'username' => 'piet', 'email' => 'c@example.com', 'params' => '{}'],
            ['id' => 53, 'name' => 'Noor', 'username' => 'noor', 'email' => 'd@example.com', 'params' => '{"kunena_name":1}'],
        ]);
        $this->insertRow('#__fields', ['id' => self::FIELD, 'context' => 'com_users.user', 'state' => 1]);
        $this->insertRow('#__fields_values', ['field_id' => self::FIELD, 'item_id' => '51', 'value' => 'Moonlight']);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container = $this->saved['container'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaUserName::class, 'displayNames', $this->saved['names']);
            $this->replace(KunenaUserName::class, 'displayNameField', $this->saved['field']);
        }

        parent::tearDown();
    }

    public function testTheSearchOfTheMessagesOfAMemberDoesNotFindHimByHisLoginName(): void
    {
        $this->configure(['username' => KunenaUserName::REALNAME]);

        foreach ([[1, 50, 'Jelle Kok'], [2, 51, 'Anna Smit']] as [$id, $userid, $name]) {
            $this->insertRow('#__kunena_messages', [
                'id' => $id, 'parent' => 0, 'thread' => 1, 'catid' => 2, 'userid' => $userid, 'name' => $name, 'subject' => 'Topic', 'time' => 1000,
            ]);
        }

        $this->assertSame([], $this->messages('jkok8'), 'his login name');
        $this->assertSame([1], $this->messages('Jelle Kok'), 'the name which the forum shows');
    }

    /**
     * The ids of the messages which the search "messages of this user" finds (exact name), as SearchModel builds it.
     */
    private function messages(string $typed): array
    {
        $db       = $this->getDBDriver();
        $accounts = KunenaUserName::getSearchCondition($typed, 'su', true);
        $ids      = $db->setQuery('SELECT m.id FROM #__kunena_messages AS m WHERE '
            . KunenaSearchUserFilter::condition($db->quote($typed), 0, '#__', $accounts) . ' ORDER BY m.id')->loadColumn();

        return array_map('intval', $ids);
    }

    /**
     * The configuration of the names (a fresh instance, the field of the nickname read again).
     */
    private function configure(array $values): void
    {
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, $values);
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
