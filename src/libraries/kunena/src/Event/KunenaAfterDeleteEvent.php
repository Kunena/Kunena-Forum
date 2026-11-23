<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Event
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Event;

\defined('_JEXEC') or die;

use Joomla\CMS\Event\Model\AfterDeleteEvent;

/**
 * Class for Model event.
 * Example:
 *  new KunenaAterDeleteEvent('onEventName', ['context' => 'com_example.example', 'subject' => $itemObjectToDelete]);
 *
 * A delete event of the models of Joomla, as the save events: the listeners typed for it get the event (plg_kunena_finder). It was
 * a content event, so they got a TypeError after every delete while that plugin was on.
 *
 * @since  7.1.0
 */
class KunenaAfterDeleteEvent extends AfterDeleteEvent
{
}
