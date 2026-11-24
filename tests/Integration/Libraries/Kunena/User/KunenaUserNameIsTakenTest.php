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
 * A nickname is refused when it is the name of another member. The check took the nickname as it was typed, and the save cut it to
 * 100 characters afterwards: the 100 characters of the nickname (or the real name) of another member plus one more were accepted,
 * and saved as his name.
 */
class KunenaUserNameIsTakenTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const FIELD = 7;

    private const JOE = 50;

    private const ANN = 51;

    private array $saved = [];

    private string $long = '';

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

        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['username' => KunenaUserName::DISPLAYNAME, 'displayNameField' => self::FIELD]);
        $this->replace(KunenaConfig::class, 'instance', $config);

        // Joe's nickname has the 100 characters which a nickname may have; his real name too.
        $this->long = str_repeat('Joe', 33) . 'X';

        $this->insertRows('#__users', [
            ['id' => self::JOE, 'name' => 'R' . str_repeat('e', 99), 'username' => 'loginjoe', 'email' => 'joe@example.com', 'params' => '{}'],
            ['id' => self::ANN, 'name' => 'Ann Real', 'username' => 'loginann', 'email' => 'ann@example.com', 'params' => '{}'],
        ]);
        $this->insertRow('#__fields', ['id' => self::FIELD, 'context' => 'com_users.user', 'state' => 1]);
        $this->insertRow('#__fields_values', ['field_id' => self::FIELD, 'item_id' => (string) self::JOE, 'value' => $this->long]);
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

    public function testTheNicknameOfAnotherMemberIsTaken(): void
    {
        $this->assertTrue(KunenaUserName::isTaken($this->long, self::ANN));
        $this->assertFalse(KunenaUserName::isTaken('Something else', self::ANN));
    }

    public function testANicknameWhichIsCutToTheNicknameOfAnotherMemberIsTaken(): void
    {
        $this->assertTrue(KunenaUserName::isTaken($this->long . 'x', self::ANN), 'saved as the 100 characters of Joe\'s nickname');
        $this->assertTrue(KunenaUserName::isTaken('R' . str_repeat('e', 99) . 'ee', self::ANN), 'saved as the real name of Joe');
    }

    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }
}
