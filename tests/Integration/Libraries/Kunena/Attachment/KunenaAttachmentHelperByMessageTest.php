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

namespace Kunena\Tests\Integration\Libraries\Kunena\Attachment;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionProperty;

/**
 * The files of a message are all its files, also when one of them was loaded by its id before in the request: the list of the
 * message was then that file alone. A crafted edit which named one of the files of a message counted one file against the limit
 * of the attachments (and the delete of a topic or a category could leave the others).
 */
class KunenaAttachmentHelperByMessageTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private mixed $container;

    public function getSchemasToLoad(): array
    {
        return ['kunena'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The helper keeps the files for the request: each test starts without them.
        foreach (['_instances', '_messages', '_messagesLoaded'] as $cache) {
            (new ReflectionProperty(KunenaAttachmentHelper::class, $cache))->setValue(null, []);
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        $this->container    = Factory::$container;
        Factory::$container = $container;

        foreach ([1, 2, 3] as $id) {
            $this->insertRow('#__kunena_attachments', [
                'id' => $id, 'mesid' => 5, 'userid' => 42, 'hash' => '', 'size' => 1, 'folder' => 'media/kunena/attachments/42',
                'filetype' => 'image/png', 'filename' => "file$id.png", 'filename_real' => "file$id.png", 'caption' => '',
            ]);
        }
    }

    protected function tearDown(): void
    {
        if (\defined('JVERSION')) {
            Factory::$container = $this->container;

            foreach (['_instances', '_messages', '_messagesLoaded'] as $cache) {
                (new ReflectionProperty(KunenaAttachmentHelper::class, $cache))->setValue(null, []);
            }
        }

        parent::tearDown();
    }

    public function testAFileLoadedByItsIdDoesNotMakeTheListOfTheMessage(): void
    {
        KunenaAttachmentHelper::getById([2], 'none');

        $this->assertSame([1, 2, 3], array_keys(KunenaAttachmentHelper::getByMessage(5, 'none')));
    }
}
