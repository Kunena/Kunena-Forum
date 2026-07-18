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

use Joomla\CMS\Event\AbstractEvent;

/**
 * Class for onKunenaSidebar Model event.
 * Example:
 *  new KunenaSidebarEvent('onEventName', ['context' => 'com_example.example', 'subject' => $itemObjectToDelete]);
 *
 * Joomla Core doesn't have a Finder\BeforeDeleteEvent
 * 
 * @since  7.1.0
 */
class KunenaSidebarEvent extends AbstractEvent {}
