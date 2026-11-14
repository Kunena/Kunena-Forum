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

namespace Kunena\Tests\Integration\Libraries\Kunena\KunenaPrivate;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\KunenaPrivate\KunenaPrivateMessage;
use Kunena\Forum\Libraries\KunenaPrivate\Message\KunenaPrivateMessageFinder;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionProperty;

/**
 * A page looks for the same private message more than once (a topic, and the check of each private file of it): the message is
 * read once, from its row (not read again for each object), and kept until a private message is saved or deleted.
 */
class KunenaPrivateMessageFinderTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The finder reads the database of the container of Joomla.
        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        $this->saved        = ['container' => Factory::$container];
        Factory::$container = $container;
        KunenaPrivateMessageFinder::forget();

        $this->insertRow('#__kunena_private', [
            'id'          => 2,
            'parent_id'   => 0,
            'author_id'   => 46,
            'created_at'  => '2026-01-01 00:00:00',
            'attachments' => 1,
            'subject'     => 'Only private files',
            'body'        => '',
            'params'      => '{"receivers":{"users":[47]}}',
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            KunenaPrivateMessageFinder::forget();
            Factory::$container = $this->saved['container'];
        }

        parent::tearDown();
    }

    private function find(int $id): KunenaPrivateMessage
    {
        return (new KunenaPrivateMessageFinder())->where('id', '=', $id)->firstOrNew();
    }

    public function testTheRowBecomesTheMessage(): void
    {
        $message = $this->find(2);

        $this->assertTrue($message->exists());
        $this->assertSame(2, (int) $message->id);
        $this->assertSame('Only private files', $message->subject);
        $this->assertSame(46, (int) $message->author_id);
    }

    public function testAMessageWhichWasFoundIsKeptForThePage(): void
    {
        $first = $this->find(2);

        // The second search finds its id, the message itself is not read again.
        $this->getDBDriver()->setQuery('UPDATE ' . $this->getDBDriver()->quoteName('#__kunena_private') . " SET subject = 'Changed'")->execute();

        $second = $this->find(2);
        $this->assertSame($first, $second);
        $this->assertSame('Only private files', $second->subject);

        // After a change (a save or a delete forgets them), the message is read again.
        KunenaPrivateMessageFinder::forget();
        $this->assertSame('Changed', $this->find(2)->subject);
    }

    public function testAMessageWhichIsNotThereIsNotFound(): void
    {
        $this->assertSame([], (new KunenaPrivateMessageFinder())->where('id', '=', 5)->find());
        $this->assertArrayNotHasKey(5, (new ReflectionProperty(KunenaPrivateMessageFinder::class, 'instances'))->getValue());
    }
}
