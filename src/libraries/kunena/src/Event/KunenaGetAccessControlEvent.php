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
use Kunena\Forum\Libraries\Access\KunenaAccessAbstract;

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
     * @param   KunenaAccessAbstract  $value  The value to set
     *
     * @return  KunenaAccessAbstract
     * @since   6.5.0
     * @throws  \BadMethodCallException
     */
    protected function onSetAccessControl(KunenaAccessAbstract $accesscontrol): KunenaAccessAbstract
    {
        return $accesscontrol;
    }

    /**
     * Getter for the data argument.
     *
     * @return  KunenaAccessAbstract|array
     * @since   6.5.0
     */
    public function getAccessControl(): KunenaAccessAbstract|array
    {
        if (isset($this->arguments['accesscontrol']) && $this->arguments['accesscontrol'] instanceof KunenaAccessAbstract) {
            return $this->arguments['accesscontrol'];
        }

        return $this->arguments['result'] ?? [];
    }

    /**
     * Setter for the data argument.
     *
     * @return  $this
     * @since   6.5.0
     */
    public function setAccessControl(KunenaAccessAbstract $accesscontrol)
    {
        return $this->setArgument('accesscontrol', $accesscontrol);
    }
}
