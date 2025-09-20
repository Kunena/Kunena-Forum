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
use Kunena\Forum\Libraries\Integration\KunenaAvatar;

/**
 * Class for onKunenaGetAvatar event.
 * Example:
 *  new KunenaGetAvatarEvent('onKunenaGetAvatar', []);
 *
 * @since  7.0.0
 */
class KunenaGetAvatarEvent extends AbstractImmutableEvent implements ResultAwareInterface
{
    use ResultAware;
    use ResultTypeObjectAware;

    /**
     * Setter for the object argument.
     *
     * @param   KunenaAvatar  $value  The value to set
     *
     * @return  KunenaAvatar
     * @since   7.0.0
     * @throws  \BadMethodCallException
     */
    protected function onSetAvatar(KunenaAvatar $avatar): KunenaAvatar
    {
        return $avatar;
    }

    /**
     * Getter for the data argument.
     *
     * @return  KunenaAvatar|array
     * @since   7.0.0
     */
    public function getAvatar(): KunenaAvatar|array
    {
        if (isset($this->arguments['avatar']) && $this->arguments['avatar'] instanceof KunenaAvatar) {
            return $this->arguments['avatar'];
        }

        return $this->arguments['result'] ?? [];
    }

    /**
     * Setter for the data argument.
     *
     * @return  $this
     * @since   7.0.0
     */
    public function setAvatar(KunenaAvatar $avatar)
    {
        return $this->setArgument('avatar', $avatar);
    }
}
