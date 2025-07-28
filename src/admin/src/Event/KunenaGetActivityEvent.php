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
use Kunena\Forum\Libraries\Activity\KunenaActivity;

/**
 * Class for onKunenaGetActivity event.
 * Example:
 *  new KunenaGetActivityEvent('onKunenaGetActivity', []);
 *
 * @since  6.5.0
 */
class KunenaGetActivityEvent extends AbstractImmutableEvent implements ResultAwareInterface
{
    use ResultAware;
    use ResultTypeObjectAware;
    
    /**
     * Setter for the object argument.
     *
     * @param   KunenaActivity  $value  The value to set
     *
     * @return  KunenaActivity
     * @since   6.5.0
     * @throws  \BadMethodCallException
     */
    protected function onSetActivity(KunenaActivity $activity): KunenaActivity
    {
        return $activity;
    }
    
    /**
     * Getter for the data argument.
     *
     * @return  KunenaActivity|array
     * @since   6.5.0
     */
    public function getActivity(): KunenaActivity|array
    {
        if (isset($this->arguments['activity']) && $this->arguments['activity'] instanceof KunenaActivity) {
            return $this->arguments['activity'];
        }
        
        return $this->arguments['result'] ?? [];
    }
    
    /**
     * Setter for the data argument.
     *
     * @return  $this
     * @since   6.5.0
     */
    public function setActivity(KunenaActivity $activity)
    {
        return $this->setArgument('activity', $activity);
    }
}
