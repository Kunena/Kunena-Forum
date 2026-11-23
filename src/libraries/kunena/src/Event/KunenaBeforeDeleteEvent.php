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

use Joomla\CMS\Event\Finder\FinderEventInterface;
use Joomla\CMS\Event\Model\BeforeDeleteEvent;

/**
 * Class for Model event.
 * Example:
 *  new KunenaBeforeDeleteEvent('onEventName', ['context' => 'com_example.example', 'subject' => $itemObjectToDelete]);
 *
 * A delete event of the models of Joomla, as the save events: the listeners typed for it get the event (plg_kunena_finder). It was
 * a sibling class, so they got a TypeError and every delete failed while that plugin was on.
 *
 * @since  7.0.0
 */
class KunenaBeforeDeleteEvent extends BeforeDeleteEvent implements FinderEventInterface
{
}
