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
 * Class for onKunenaGetProfile event.
 * Example:
 *  new KunenaGetProfileEvent('onKunenaGetProfile', []);
 *
 * @since  7.0.0
 */
class KunenaGetProfileEvent extends AbstractImmutableEvent implements ResultAwareInterface
{
    use ResultAware;
    use ResultTypeObjectAware;

    /**
     * Setter for the object argument.
     *
     * @param   KunenaProfile  $value  The value to set
     *
     * @return  KunenaProfile
     * @since   7.0.0
     * @throws  \BadMethodCallException
     */
    protected function onSetProfile(KunenaProfile $profile): KunenaProfile
    {
        return $profile;
    }

    /**
     * Getter for the data argument.
     *
     * @return  KunenaProfile|array
     * @since   7.0.0
     */
    public function getProfile(): KunenaProfile|array
    {
        if (isset($this->arguments['profile']) && $this->arguments['profile'] instanceof KunenaProfile) {
            return $this->arguments['profile'];
        }

        return $this->arguments['result'] ?? [];
    }

    /**
     * Setter for the data argument.
     *
     * @return  $this
     * @since   7.0.0
     */
    public function setProfile(KunenaProfile $profile)
    {
        return $this->setArgument('profile', $profile);
    }
}
