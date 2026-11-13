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

namespace Kunena\Tests\Unit\Libraries\Kunena\Attachment;

use BadMethodCallException;
use Exception;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\Dispatcher;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Event\KunenaAfterAttachmentUploadEvent;
use Kunena\Forum\Libraries\Event\KunenaDisplayEvent;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;
use stdClass;
use TypeError;

/**
 * A plugin could not act on an uploaded attachment, to store it in another place too (issue #3425). Once a file uploaded
 * through the editor is stored and saved, the plugins of the kunena group get onKunenaAfterAttachmentUpload; a listener
 * that fails never fails the upload.
 */
class KunenaAttachmentAfterUploadEventTest extends UnitTestCase
{
    private const string EVENT = 'onKunenaAfterAttachmentUpload';

    /**
     * The values which the test replaces, put back after the test.
     */
    private array $saved = [];

    private Dispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->requireJoomla();

        // A stub application with a real dispatcher, and no plugin to load from the database.
        $this->dispatcher = new Dispatcher();
        $application      = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($this->dispatcher);

        $plugins     = new ReflectionProperty(PluginHelper::class, 'plugins');
        $this->saved = [
            'application' => Factory::$application,
            'plugins'     => $plugins->getValue(),
        ];

        Factory::$application = $application;
        $plugins->setValue(null, []);
    }

    protected function tearDown(): void
    {
        if (!$this->saved) {
            return;
        }

        Factory::$application = $this->saved['application'];
        (new ReflectionProperty(PluginHelper::class, 'plugins'))->setValue(null, $this->saved['plugins']);
    }

    private static function attachment(int $id, bool $exists): KunenaAttachment
    {
        return self::bare(KunenaAttachment::class, ['id' => $id, 'userid' => 7, '_exists' => $exists]);
    }

    public function testListenerGetsTheSavedAttachmentOnce(): void
    {
        $attachment = self::attachment(15, true);
        $events     = [];

        $this->dispatcher->addListener(self::EVENT, function ($event) use (&$events) {
            $events[] = $event;
        });

        $attachment->dispatchAfterUpload();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(KunenaAfterAttachmentUploadEvent::class, $events[0]);
        $this->assertSame($attachment, $events[0]->getAttachment());
    }

    public function testNoEventForAnAttachmentThatDoesNotExist(): void
    {
        $calls = 0;

        $this->dispatcher->addListener(self::EVENT, function () use (&$calls) {
            $calls++;
        });

        self::attachment(15, false)->dispatchAfterUpload();
        self::attachment(0, true)->dispatchAfterUpload();

        $this->assertSame(0, $calls);
    }

    public function testThrowingListenerDoesNotFailTheUpload(): void
    {
        $called = false;

        $this->dispatcher->addListener(self::EVENT, function () use (&$called) {
            $called = true;

            throw new Exception('The remote storage is down.');
        });

        self::attachment(15, true)->dispatchAfterUpload();

        $this->assertTrue($called);
    }

    public function testWronglyTypedListenerDoesNotFailTheUpload(): void
    {
        // The dispatcher passes the upload event to a listener which expects another event: a TypeError.
        $this->dispatcher->addListener(self::EVENT, function (KunenaDisplayEvent $event) {
        });

        $attachment = self::attachment(15, true);
        $attachment->dispatchAfterUpload();

        // The same dispatch outside the method does throw.
        $this->expectException(TypeError::class);
        $this->dispatcher->dispatch(self::EVENT, new KunenaAfterAttachmentUploadEvent(self::EVENT, ['subject' => $attachment]));
    }

    public function testEventNeedsTheSubject(): void
    {
        $this->expectException(BadMethodCallException::class);

        new KunenaAfterAttachmentUploadEvent(self::EVENT, []);
    }

    public function testEventNeedsAnAttachmentAsSubject(): void
    {
        $this->expectException(BadMethodCallException::class);

        new KunenaAfterAttachmentUploadEvent(self::EVENT, ['subject' => new stdClass()]);
    }

    public function testUploadDispatchesAfterTheSavedFile(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertSame(1, preg_match('/public function upload\(\)[^{]*\{(.*?)\n    \}\n/s', $source, $m));

        $completed = strpos($m[1], 'if (!empty($response->completed)) {');
        $save      = strpos($m[1], 'if ($attachment->saveFile(');
        $dispatch  = strpos($m[1], '$attachment->dispatchAfterUpload();');
        $response  = strpos($m[1], '$response->id       = $attachment->id;');

        $this->assertIsInt($completed);
        $this->assertIsInt($save, 'The event depends on the result of saveFile().');
        $this->assertIsInt($dispatch);
        $this->assertSame(1, substr_count($m[1], 'dispatchAfterUpload('), 'Once per completed file.');
        $this->assertLessThan($save, $completed, 'Only in the completed branch.');
        $this->assertLessThan($dispatch, $save, 'After the file is saved.');
        $this->assertLessThan($response, $dispatch, 'Before the response is filled.');
    }
}
