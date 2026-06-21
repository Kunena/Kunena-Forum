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

/**
 * Class for Model event.
 * Example:
 *  new KunenaBeforeModifySocialsEvent('onEventName', ['socials' => $socials]);
 *
 * Joomla Core doesn't have a Finder\BeforeDeleteEvent
 * 
 * @since  7.1.0
 */
class KunenaBeforeModifySocialsEvent extends GenericEvent {
    /**
     * Setter for the socials argument.
     *
     * @param   array  $value  The value to set
     *
     * @return  array
     *
     * @since  7.1.0
     */
    protected function onSetSocials(array $value): array
    {
        return $value;
    }
    
    /**
     * Getter for the socials argument.
     *
     * @return  array
     *
     * @since  7.1.0
     */
    public function getSocials(): array
    {
        return $this->arguments['socials'];
    }
}