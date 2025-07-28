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
use Kunena\Forum\Libraries\Integration\KunenaPrivate;

/**
 * Class for onKunenaGetPrivate event.
 * Example:
 *  new KunenaGetPrivateEvent('onKunenaGetPrivate', []);
 *
 * @since  6.5.0
 */
class KunenaGetPrivateEvent extends AbstractImmutableEvent implements ResultAwareInterface
{
    use ResultAware;
    use ResultTypeObjectAware;
    
    /**
     * Setter for the object argument.
     *
     * @param   KunenaPrivate  $value  The value to set
     *
     * @return  KunenaPrivate
     * @since   6.5.0
     * @throws  \BadMethodCallException
     */
    protected function onSetPrivate(KunenaPrivate $private): KunenaPrivate
    {
        return $private;
    }
    
    /**
     * Getter for the data argument.
     *
     * @return  KunenaPrivate|array
     * @since   6.5.0
     */
    public function getPrivate(): KunenaPrivate|array
    {
        if (isset($this->arguments['private']) && $this->arguments['private'] instanceof KunenaPrivate) {
            return $this->arguments['private'];
        }
        
        return $this->arguments['result'] ?? [];
    }
    
    /**
     * Setter for the data argument.
     *
     * @return  $this
     * @since   6.5.0
     */
    public function setPrivate(KunenaPrivate $private)
    {
        return $this->setArgument('private', $private);
    }
}
