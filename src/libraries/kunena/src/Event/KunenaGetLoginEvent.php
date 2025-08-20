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
use Kunena\Forum\Plugin\Kunena\Joomla\Helper\KunenaLoginJoomla;

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
     * @param   KunenaLoginJoomla  $value  The value to set
     *
     * @return  KunenaLoginJoomla
     * @since   6.5.0
     * @throws  \BadMethodCallException
     */
    protected function onSetLogin(KunenaLoginJoomla $login): KunenaLoginJoomla
    {
        return $login;
    }

    /**
     * Getter for the data argument.
     *
     * @return  KunenaLoginJoomla|array
     * @since   6.5.0
     */
    public function getLogin(): KunenaLoginJoomla|array
    {
        if (isset($this->arguments['login']) && $this->arguments['login'] instanceof KunenaLoginJoomla) {
            return $this->arguments['login'];
        }

        return $this->arguments['result'] ?? [];
    }

    /**
     * Setter for the data argument.
     *
     * @return  $this
     * @since   6.5.0
     */
    public function setLogin(KunenaLoginJoomla $login)
    {
        return $this->setArgument('login', $login);
    }
}
