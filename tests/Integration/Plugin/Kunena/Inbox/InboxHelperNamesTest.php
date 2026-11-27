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

namespace Kunena\Tests\Integration\Plugin\Kunena\Inbox;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use RuntimeException;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Plugin\Kunena\Inbox\Helper\InboxHelper;
use Kunena\Forum\Plugin\Kunena\Inbox\Helper\KunenaPrivateInbox;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The names in the inbox: the page puts every value of the JSON into the page escaped (esc() of its script), so the names come as
 * text. They came HTML-escaped already (getName()), and "Tom & Jerry" read "Tom &amp; Jerry" in the list, the conversation and the
 * list of the blocked users.
 */
class InboxHelperNamesTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $root = \dirname(__DIR__, 5);
        require_once $root . '/src/plugins/plg_kunena_inbox/src/Helper/InboxAttachments.php';
        require_once $root . '/src/plugins/plg_kunena_inbox/src/Helper/InboxHelper.php';

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnArgument(0);

        // The forum shows the usernames, a user can not choose; no profile integration.
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['username' => 1, 'userCanChooseName' => 0, 'displayNameField' => 0]);
        $profile          = new KunenaProfile();
        $profile->enabled = false;

        // The user 11 is "Tom & Jerry".
        $tom = (new ReflectionClass(KunenaUser::class))->newInstanceWithoutConstructor();
        foreach (['userid' => 11, 'username' => 'Tom & Jerry', 'name' => 'Tom & Jerry'] as $name => $value) {
            (new ReflectionProperty(KunenaUser::class, $name))->setValue($tom, $value);
        }

        // A command line application without plugins: the helper of the users finds the current user (a guest) with it.
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn(new Dispatcher());
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client === 'cli');

        $this->saved = [
            'container'   => Factory::$container,
            'language'    => Factory::$language,
            'application' => Factory::$application,
            'plugins'     => $this->replace(PluginHelper::class, 'plugins', []),
            'config'    => $this->replace(KunenaConfig::class, 'instance', $config),
            'profile'   => $this->replace(KunenaProfile::class, 'instance', $profile),
        ];

        Factory::$container   = $container;
        Factory::$language    = $language;
        Factory::$application = $application;

        $this->saved['users'] = $this->replace(KunenaUserHelper::class, '_instances', [11 => $tom]);

        foreach (explode(';', (string) file_get_contents($root . '/src/plugins/plg_kunena_inbox/sql/install.mysql.utf8.sql')) as $sql) {
            if (trim($sql) !== '') {
                $driver->setQuery($sql)->execute();
            }
        }

        // The tables of the plugin are dropped after the test: their rows are not ones which the helper of the tests empties.
        $driver->setQuery('INSERT INTO #__kunena_inbox_blocks (user_id, blocked_id, created_time) VALUES (10, 11, 1)')->execute();
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            foreach (['#__kunena_inbox', '#__kunena_inbox_attachments', '#__kunena_inbox_blocks'] as $table) {
                $this->getDBDriver()->setQuery('DROP TABLE IF EXISTS ' . $table)->execute();
            }

            Factory::$container = $this->saved['container'];
            Factory::$language  = $this->saved['language'];
            Factory::$application = $this->saved['application'];
            $this->replace(PluginHelper::class, 'plugins', $this->saved['plugins']);
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaProfile::class, 'instance', $this->saved['profile']);
            $this->replace(KunenaUserHelper::class, '_instances', $this->saved['users']);
        }

        parent::tearDown();
    }

    public function testTheListOfTheBlockedUsersHasTheNamesAsText(): void
    {
        $this->assertSame([['id' => 11, 'name' => 'Tom & Jerry']], InboxHelper::getBlocked(10));
    }

    /**
     * A user who blocked another one gets no e-mail of his mentions (nor of his thanks): the inbox left the notification out, the
     * e-mail was sent all the same. (User 10 blocked user 11.)
     */
    public function testNoMailOfTheMentionOfABlockedUser(): void
    {
        require_once \dirname(__DIR__, 5) . '/src/plugins/plg_kunena_inbox/src/Helper/KunenaPrivateInbox.php';

        $message = (new ReflectionClass(KunenaMessage::class))->newInstanceWithoutConstructor();
        foreach (['id' => 61, 'thread' => 60, 'catid' => 2, 'userid' => 11, 'subject' => 'Hello'] as $name => $value) {
            $message->$name = $value;
        }

        // The e-mail is on by default: only the user who blocked the author is left out of it.
        $inbox = new KunenaPrivateInbox(new Registry(['notifications' => 1, 'emailDefault' => 1]));

        $this->assertSame([10], $inbox->notify($message, 'mention', [10, 12]));
    }

    /**
     * In the read only mode of the forum no private message is sent (nobody can post in it, but a message still went out).
     */
    public function testNoMessageIsSentInReadOnlyMode(): void
    {
        $config = KunenaConfig::getInstance();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['username' => 1, 'userCanChooseName' => 0, 'displayNameField' => 0, 'readOnly' => 1]);

        try {
            InboxHelper::send(10, 11, 'Subject', 'Text');
            $this->fail('The message was sent.');
        } catch (RuntimeException $e) {
            $this->assertSame('PLG_KUNENA_INBOX_ERROR_READONLY', $e->getMessage());
        }

        $this->assertSame([], $this->fetchAll('SELECT id FROM #__kunena_inbox'));
    }

    /**
     * Set a static property of a class and give back the value it had.
     */
    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }
}
