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

namespace Kunena\Tests\Integration\Plugin\Privacy\Kunena;

use Joomla\CMS\Event\Privacy\ExportRequestEvent;
use Joomla\CMS\Event\Privacy\RemoveDataEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Plugin\Privacy\Kunena\Extension\Kunena;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;

/**
 * The export of the data of a user (the privacy tools of Joomla) has his data, not the data of other users, and the removal of his
 * data leaves the data of the others.
 */
class ExportDataOfOthersTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const USER = 42;

    private const OTHER = 7;

    private ?Container $savedContainer = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $root = \dirname(__DIR__, 5);

        require_once $root . '/src/plugins/plg_privacy_kunena/src/Helper/KunenaHelper.php';
        require_once $root . '/src/plugins/plg_privacy_kunena/src/Extension/Kunena.php';

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share('DatabaseDriver', static fn () => $driver);

        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;

        // The tables of the inbox plugin, dropped after the test.
        foreach (explode(';', (string) file_get_contents($root . '/src/plugins/plg_kunena_inbox/sql/install.mysql.utf8.sql')) as $sql) {
            if (trim($sql) !== '') {
                $driver->setQuery($sql)->execute();
            }
        }
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            foreach (['#__kunena_inbox', '#__kunena_inbox_attachments', '#__kunena_inbox_blocks'] as $table) {
                $this->getDBDriver()->setQuery('DROP TABLE IF EXISTS ' . $table)->execute();
            }

            Factory::$container = $this->savedContainer;
        }

        parent::tearDown();
    }

    private function user(): User
    {
        $user     = new User();
        $user->id = self::USER;

        return $user;
    }

    /**
     * The domains of the export of the user, as [name => [items as arrays]], and the names in their order.
     */
    private function export(): array
    {
        $domains = [];
        $event   = $this->createStub(ExportRequestEvent::class);
        $event->method('getUser')->willReturn($this->user());
        $event->method('addResult')->willReturnCallback(static function ($result) use (&$domains): void {
            $domains = $result;
        });

        $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
        $plugin->setDatabase($this->getDBDriver());
        $plugin->onPrivacyExportRequest($event);

        $items = [];
        $names = [];

        foreach ($domains as $domain) {
            $names[] = $domain->name;

            foreach ($domain->getItems() as $item) {
                $fields = [];

                foreach ($item->getFields() as $field) {
                    $fields[$field->name] = $field->value;
                }

                $items[$domain->name][] = $fields;
            }
        }

        return ['items' => $items, 'names' => $names];
    }

    /**
     * The inbox which he "sent" is his private messages, not the notifications which he caused: the subscribers of his topics, his
     * followers and the moderators were in it, with their ids and when they read them.
     */
    public function testTheSentInboxHasHisPrivateMessagesOnly(): void
    {
        // A private message of his to 7; and a notification of 7 about his reply (7 subscribed to the topic).
        // Not with insertRows(): the helper of the tests would empty the table before the next test, after it was dropped.
        foreach ([
            ['user_id' => self::OTHER, 'sender_id' => self::USER, 'type' => 'message', 'subject' => 'Hello', 'body' => 'Hi'],
            ['user_id' => self::OTHER, 'sender_id' => self::USER, 'type' => 'subscription', 'subject' => 'A topic of 7', 'body' => '', 'read_time' => 1234],
        ] as $row) {
            $row = (object) $row;
            $this->getDBDriver()->insertObject('#__kunena_inbox', $row);
        }

        $sent = $this->export()['items']['kunena_user_inbox_sent'] ?? [];

        $this->assertSame(['message'], array_column($sent, 'type'));
    }

    /**
     * His bans without the comments of the moderators (their ids and notes, which the site shows to the moderators only).
     */
    public function testHisBanHasNoCommentsOfTheModerators(): void
    {
        $this->insertRow('#__kunena_users_banned', [
            'userid'        => self::USER,
            'created_by'    => 1,
            'created_time'  => '2026-01-01 00:00:00',
            'reason_public' => 'Spam',
            'comments'      => '[{"userid":1,"time":1,"comment":"a note of the moderators"}]',
        ]);

        $bans = $this->export()['items']['kunena_user_banned'] ?? [];

        $this->assertCount(1, $bans);
        $this->assertSame('Spam', $bans[0]['reason_public']);
        $this->assertArrayNotHasKey('comments', $bans[0]);
    }
}
