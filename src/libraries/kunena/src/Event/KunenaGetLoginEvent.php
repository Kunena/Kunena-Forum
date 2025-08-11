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

use Joomla\CMS\Event\AbstractImmutableEvent;
use Joomla\CMS\Event\Result\ResultAware;
use Joomla\CMS\Event\Result\ResultAwareInterface;
use Joomla\CMS\Event\Result\ResultTypeObjectAware;
use Kunena\Forum\Libraries\Integration\KunenaProfile;

/**
 * Class for onKunenaGetLogin event.
 * Example:
 *  new KunenaGetLoginEvent('onKunenaGetLogin', []);
 *
 * @since  6.5.0
 */
class KunenaGetLoginEvent extends AbstractImmutableEvent implements ResultAwareInterface
{
    use ResultAware;
    use ResultTypeObjectAware;

    /**
     * Setter for the object argument.
     *
     * @param   KunenaProfile  $value  The value to set
     *
     * @return  KunenaProfile
     * @since   6.5.0
     * @throws  \BadMethodCallException
     */
    /*protected function onSetProfile(KunenaProfile $profile): KunenaProfile
    {
        return $profile;
    }*/

    /**
     * Getter for the data argument.
     *
     * @return  array
     * @since   6.5.0
     */
    public function getLogin(): array
    {
        return $this->arguments['result'] ?? [];
    }

    /**
     * Setter for the data argument.
     *
     * @return  $this
     * @since   6.5.0
     */
    /*public function setProfile(KunenaProfile $profile)
    {
        return $this->setArgument('profile', $profile);
    }*/
}
