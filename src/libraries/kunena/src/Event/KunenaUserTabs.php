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

use Joomla\CMS\Event\GenericEvent;
use Joomla\CMS\Event\Result\ResultAware;
use Joomla\CMS\Event\Result\ResultAwareInterface;
use Joomla\CMS\Event\Result\ResultTypeStringAware;

/**
 * Class for Model event.
 * Example:
 *  new KunenaUserTabs('onEventName', ['context' => 'com_example.example', 'subject' => $itemObjectToDelete]);
 *
 * Joomla Core doesn't have a Finder\BeforeDeleteEvent
 * 
 * @since  7.1.0
 */
class KunenaUserTabs extends GenericEvent implements ResultAwareInterface {
    use ResultAware;
    use ResultTypeStringAware;
    
    /**
     * Getter for the socials argument.
     *
     * @return  array
     *
     * @since  7.1.0
     */
    public function getUserTabs(): array
    {
        return $this->arguments['usertabs'];
    }
}