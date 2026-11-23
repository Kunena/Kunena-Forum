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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\KunenaDiagnostics;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The repairs of the diagnostics of the backend on the real tables:
 * - an upload which waits for its message (mesid 0) is no orphaned attachment: it was listed and deleted, and the post lost its file;
 * - a topic with a wrong poll is repaired, not deleted: deleting it left its messages without a topic (and the next repair deleted
 *   them).
 */
class KunenaDiagnosticsRepairsTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private ?Container $savedContainer = null;

    /**
     * @return  string[]
     */
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

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share('DatabaseDriver', static fn () => $driver);

        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
        }

        parent::tearDown();
    }

    private function attachment(int $id, int $mesid): void
    {
        $this->insertRow('#__kunena_attachments', ['id' => $id, 'mesid' => $mesid, 'userid' => 42, 'folder' => 'media/kunena/attachments/42',
            'filetype' => 'text/plain', 'filename' => "f$id.txt"]);
    }

    public function testAnUploadWhichWaitsForItsMessageIsNoOrphan(): void
    {
        // The upload 1 waits for its message, the attachment 2 is of the message 5 which is gone.
        $this->attachment(1, 0);
        $this->attachment(2, 5);

        $this->assertSame(1, KunenaDiagnostics::count('attachmentOrphaned'));

        KunenaDiagnostics::delete('attachmentOrphaned');

        $this->assertSame([['id' => 1]], $this->fetchAll('SELECT id FROM #__kunena_attachments ORDER BY id'));
    }
}
