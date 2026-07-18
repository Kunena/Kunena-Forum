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

use Joomla\CMS\Event\Content\BeforeDisplayEvent;

/**
 * Class for onKunenaBeforeRender event.
 * Example:
 *  new KunenaBeforeRenderEvent('onEventName', ['context' => 'com_example.example', 'content' => $content]);
 *
 * @since  7.0.0
 */
class KunenaBeforeRenderEvent extends BeforeDisplayEvent {}
