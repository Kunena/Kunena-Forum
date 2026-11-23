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

use Joomla\CMS\Event\Model\AfterDeleteEvent;
use Joomla\CMS\Event\Model\BeforeDeleteEvent;
use Kunena\Forum\Libraries\Event\KunenaAfterDeleteEvent;
use Kunena\Forum\Libraries\Event\KunenaBeforeDeleteEvent;
use Kunena\Tests\Unit\UnitTestCase;
use stdClass;

/**
 * The delete events of the Kunena objects (KunenaDatabaseObject::delete()) are the delete events of the models of Joomla, as the save
 * events are: the Smart Search plugin of Kunena (plg_kunena_finder) types its listeners with them. They were a sibling class (and a
 * content event), so with that plugin on every delete of a message, topic, category, file or announcement ended in a TypeError,
 * which the catch of the dispatch (Exception) does not catch.
 */
class KunenaDeleteEventTest extends UnitTestCase
{
    public function testTheBeforeDeleteEventReachesAListenerOfTheModelEvent(): void
    {
        $this->requireJoomla();

        $table    = new stdClass();
        $listener = static fn (BeforeDeleteEvent $event): array => [$event->getContext(), $event->getItem()];

        // As KunenaDatabaseObject::delete() makes it.
        $this->assertSame(
            ['com_kunena.message', $table],
            $listener(new KunenaBeforeDeleteEvent('onKunenaBeforeDelete', ['com_kunena.message', $table]))
        );
    }

    public function testTheAfterDeleteEventReachesAListenerOfTheModelEvent(): void
    {
        $this->requireJoomla();

        $table    = new stdClass();
        $listener = static fn (AfterDeleteEvent $event): array => [$event->getContext(), $event->getItem()];

        $this->assertSame(
            ['com_kunena.message', $table],
            $listener(new KunenaAfterDeleteEvent('onKunenaAfterDelete', ['com_kunena.message', $table]))
        );
    }
}
