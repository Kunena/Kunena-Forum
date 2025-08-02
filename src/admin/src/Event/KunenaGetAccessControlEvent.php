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

namespace Kunena\Forum\Administrator\Event;

\defined('_JEXEC') or die;

use Joomla\CMS\Event\AbstractImmutableEvent;
use Joomla\CMS\Event\Result\ResultAware;
use Joomla\CMS\Event\Result\ResultAwareInterface;
use Joomla\CMS\Event\Result\ResultTypeObjectAware;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Plugin\Kunena\Joomla\KunenaAccessJoomla;

/**
 * Class for onKunenaGetAccessControl event.
 * Example:
 *  new KunenaGetAccessControlEvent('onKunenaGetAccessControl', []);
 *
 * @since  6.5.0
 */
class KunenaGetAccessControlEvent extends AbstractImmutableEvent implements ResultAwareInterface
{
    use ResultAware;
    use ResultTypeObjectAware;

    /**
     * Setter for the object argument.
     *
     * @param   KunenaAvatar  $value  The value to set
     *
     * @return  KunenaAvatar
     * @since   6.5.0
     * @throws  \BadMethodCallException
     */
    /*protected function onSetAvatar(KunenaAvatar $avatar): KunenaAvatar
    {
        return $avatar;
    }*/

    /**
     * Getter for the data argument.
     *
     * @return  KunenaAvatar|array
     * @since   6.5.0
     */
    public function getAccessControl(): array
    {
        return $this->arguments['result'] ?? [];
    }

    /**
     * Setter for the data argument.
     *
     * @return  $this
     * @since   6.5.0
     */
    /*public function setAvatar(KunenaAvatar $avatar)
    {
        return $this->setArgument('avatar', $avatar);
    }*/
}
