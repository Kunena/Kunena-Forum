<?php

namespace Kunena\Forum\Tests\Unit\Forum\Message;

use Joomla\CMS\Event\Finder\FinderEventInterface;
use Joomla\CMS\Event\Model\AfterSaveEvent;
use Joomla\CMS\Event\Model\BeforeSaveEvent;
use Joomla\Event\Dispatcher;
use Kunena\Forum\Libraries\Event\KunenaAfterSaveEvent;
use Kunena\Forum\Libraries\Event\KunenaBeforeSaveEvent;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class KunenaSaveEventTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $joomlaRoot = getenv('KUNENA_TEST_JOOMLA_ROOT');

        if ($joomlaRoot === false || $joomlaRoot === '') {
            return;
        }

        if (!defined('_JEXEC')) {
            define('_JEXEC', 1);
        }

        require_once $joomlaRoot . '/libraries/vendor/autoload.php';
        spl_autoload_register(static function (string $class) use ($joomlaRoot): void {
            $prefix = 'Joomla\\CMS\\';

            if (str_starts_with($class, $prefix)) {
                $file = $joomlaRoot . '/libraries/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });

        require_once __DIR__ . '/../../../../src/libraries/kunena/src/Event/KunenaBeforeSaveEvent.php';
        require_once __DIR__ . '/../../../../src/libraries/kunena/src/Event/KunenaAfterSaveEvent.php';
    }

    public static function saveEvents(): array
    {
        return [
            ['Before'],
            ['After'],
        ];
    }

    #[DataProvider('saveEvents')]
    public function testSaveEventUsesJoomlaSaveEvent(string $phase): void
    {
        $source = file_get_contents(__DIR__ . "/../../../../src/libraries/kunena/src/Event/Kunena{$phase}SaveEvent.php");

        $this->assertStringContainsString("use Joomla\\CMS\\Event\\Model\\{$phase}SaveEvent;", $source);
        $this->assertStringContainsString(
            "class Kunena{$phase}SaveEvent extends {$phase}SaveEvent implements FinderEventInterface",
            $source
        );
    }

    public static function eventCases(): array
    {
        return [
            'before insert' => [true, true],
            'before update' => [true, false],
            'after insert'  => [false, true],
            'after update'  => [false, false],
        ];
    }

    #[DataProvider('eventCases')]
    public function testPositionalArgumentsReachTypedListeners(bool $before, bool $isNew): void
    {
        if (!getenv('KUNENA_TEST_JOOMLA_ROOT')) {
            $this->markTestSkipped('Set KUNENA_TEST_JOOMLA_ROOT to a Joomla installation to exercise its event classes.');
        }

        $name  = $before ? 'onKunenaBeforeSave' : 'onKunenaAfterSave';
        $table = new \stdClass();
        $event = $before
            ? new KunenaBeforeSaveEvent($name, ['com_kunena.message', &$table, $isNew])
            : new KunenaAfterSaveEvent($name, ['com_kunena.message', &$table, $isNew]);
        $dispatcher = new Dispatcher();
        $received   = null;
        $listener   = static function ($event) use (&$received): void {
            $received = $event;
        };

        if ($before) {
            $dispatcher->addListener($name, static function (BeforeSaveEvent $event) use ($listener): void {
                $listener($event);
            });
        } else {
            $dispatcher->addListener($name, static function (AfterSaveEvent $event) use ($listener): void {
                $listener($event);
            });
        }

        $dispatcher->dispatch($name, $event);

        $this->assertSame($event, $received);
        $this->assertInstanceOf(FinderEventInterface::class, $received);
        $this->assertSame('com_kunena.message', $received->getContext());
        $this->assertSame($table, $received->getItem());
        $this->assertArrayHasKey('isNew', $received->getArguments());
        $this->assertSame($isNew, $received->getIsNew());
    }
}
