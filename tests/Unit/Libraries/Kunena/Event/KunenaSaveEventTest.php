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

namespace Kunena\Tests\Unit\Libraries\Kunena\Event;

use Joomla\CMS\Event\Finder\FinderEventInterface;
use Joomla\CMS\Event\Model\AfterSaveEvent;
use Joomla\CMS\Event\Model\BeforeSaveEvent;
use Joomla\Event\Dispatcher;
use Kunena\Forum\Libraries\Event\KunenaAfterSaveEvent;
use Kunena\Forum\Libraries\Event\KunenaBeforeSaveEvent;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

/**
 * The save events of Kunena (onKunenaBeforeSave, onKunenaAfterSave, sent by KunenaDatabaseObject::save()) are the save events of
 * Joomla (port of K7.1 #10511): they were delete events, so a listener typed for a save event (the finder plugin of Kunena) got
 * a TypeError on every save, and the third argument (isNew) was dropped.
 */
class KunenaSaveEventTest extends UnitTestCase
{
    public static function events(): array
    {
        return [
            'before, a new item'      => [true, true],
            'before, an item changed' => [true, false],
            'after, a new item'       => [false, true],
            'after, an item changed'  => [false, false],
        ];
    }

    #[DataProvider('events')]
    public function testAListenerOfASaveEventGetsTheContextTheItemAndIsNew(bool $before, bool $isNew): void
    {
        $this->requireJoomla();

        $name  = $before ? 'onKunenaBeforeSave' : 'onKunenaAfterSave';
        $table = new stdClass();

        // The arguments as KunenaDatabaseObject::save() gives them: in their order, not by name.
        $event = $before
            ? new KunenaBeforeSaveEvent($name, ['com_kunena.message', &$table, $isNew])
            : new KunenaAfterSaveEvent($name, ['com_kunena.message', &$table, $isNew]);

        $received   = null;
        $dispatcher = new Dispatcher();

        // The listeners are typed as those of the finder plugin of Kunena.
        if ($before) {
            $dispatcher->addListener($name, static function (BeforeSaveEvent $event) use (&$received): void {
                $received = $event;
            });
        } else {
            $dispatcher->addListener($name, static function (AfterSaveEvent $event) use (&$received): void {
                $received = $event;
            });
        }

        $dispatcher->dispatch($name, $event);

        $this->assertSame($event, $received, 'The listener typed for the save event of Joomla gets the event.');
        $this->assertInstanceOf(FinderEventInterface::class, $received);
        $this->assertSame('com_kunena.message', $received->getContext());
        $this->assertSame($table, $received->getItem());
        $this->assertSame($isNew, $received->getIsNew(), 'The third argument (isNew) is kept.');
    }
}
