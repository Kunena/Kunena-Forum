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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Topic;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicFinder;
use Kunena\Forum\Libraries\User\KunenaUserName;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The author filter of the topic lists (fauthor, in the address of the list) looked for the text in the username and in the real
 * name of the accounts, whichever name the forum shows: on a forum which shows the real names, anybody could find out the username
 * (the login) of a member by trying it in the filter, and on a forum which shows the usernames, the real names.
 */
class KunenaTopicFinderAuthorNameTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

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

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        // The forum shows the real names; the users may not choose another name.
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, [
            'username'         => KunenaUserName::REALNAME,
            'nameUserChoice'   => 0,
            'displayNameField' => 0,
        ]);

        $this->saved = [
            'container' => Factory::$container,
            'config'    => $this->replace(KunenaConfig::class, 'instance', $config),
            'field'     => $this->replace(KunenaUserName::class, 'displayNameField', null),
        ];

        Factory::$container = $container;

        $this->insertRow('#__users', ['id' => 60, 'name' => 'Anna Shown', 'username' => 'secretlogin', 'email' => 'anna@example.com', 'params' => '{}']);

        // Topic 20 was started by Anna, topic 21 by a visitor.
        $this->insertRows('#__kunena_topics', [
            ['id' => 20, 'category_id' => 2, 'subject' => 'By Anna', 'hold' => 0, 'first_post_userid' => 60, 'first_post_guest_name' => 'Anna Shown'],
            ['id' => 21, 'category_id' => 2, 'subject' => 'By a visitor', 'hold' => 0, 'first_post_userid' => 0, 'first_post_guest_name' => 'Visitor Bob'],
        ]);
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

    public function testTheUsernameWhichTheForumDoesNotShowFindsNothing(): void
    {
        $this->assertSame([], $this->topics('secretlogin'), 'the login of Anna gives her topic away');
    }

    public function testTheShownNameFindsTheTopics(): void
    {
        $this->assertSame([20], $this->topics('Anna'));
        $this->assertSame([21], $this->topics('Visitor Bob'), 'a visitor by the name he wrote');
    }

    /**
     * The ids of the topics which the author filter keeps.
     */
    private function topics(string $author): array
    {
        $db         = $this->getDBDriver();
        $reflection = new ReflectionClass(KunenaTopicFinder::class);
        $finder     = $reflection->newInstanceWithoutConstructor();
        $reflection->getProperty('db')->setValue($finder, $db);
        $query = $db->createQuery()
            ->select($db->quoteName('a.id'))
            ->from($db->quoteName('#__kunena_topics', 'a'))
            ->order($db->quoteName('a.id'));
        $reflection->getProperty('query')->setValue($finder, $query);

        $finder->filterByAuthorName($author);

        return array_map('intval', $db->setQuery($query)->loadColumn());
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
