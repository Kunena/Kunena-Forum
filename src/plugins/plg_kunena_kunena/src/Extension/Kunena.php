<?php

/**
 * Kunena System Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      System
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Kunena\Extension;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Event\KunenaGetAvatarEvent;
use Kunena\Forum\Libraries\Event\KunenaGetProfileEvent;
use Kunena\Forum\Plugin\Kunena\Kunena\Helper\KunenaAvatarKunena;
use Kunena\Forum\Plugin\Kunena\Kunena\Helper\KunenaProfileKunena;

/**
 * Class Kunena
 *
 * @since   Kunena 6.0
 */
class Kunena extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Load language file for front-end translations
     *
     * @var boolean
     */
    protected $autoloadLanguage = \true;

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  - The method name to call (priority defaults to 0)
     *  - An array composed of the method name to call and the priority
     *
     * @return  array
     * @since   Kunena 7.0.0
     */
    public static function getSubscribedEvents(): array
    {
        $app     = Factory::getApplication();
        $mapping = [];

        if ($app->isClient('site') || $app->isClient('administrator')) {
            $mapping['onKunenaGetAvatar']  = 'onKunenaGetAvatar';
            $mapping['onKunenaGetProfile'] = 'onKunenaGetProfile';

            if ($app->isClient('site')) {
                // Only allowed in the frontend
            } elseif ($app->isClient('administrator')) {
                // Only allowed in the backend
            }
        }

        return $mapping;
    }

    /**
     * Function to get the KunenaAvatar for this integration
     * 
     * @param   KunenaGetAvatarEvent  $event  The event instance
     * 
     * @return  void
     * @since   Kunena 6.0
     */
    public function onKunenaGetAvatar(KunenaGetAvatarEvent $event): void
    {
        if (!$this->params->get('avatar', 1)) {
            return;
        }

        $event->stopPropagation();
        $event->setAvatar(new KunenaAvatarKunena($this->params));
    }

    /**
     * Function to get the KunenaProfile for this integration
     * 
     * @param   KunenaGetProfileEvent  $event  The event instance
     * 
     * @return  void
     * @since   Kunena 6.0
     */
    public function onKunenaGetProfile(KunenaGetProfileEvent $event)
    {
        if (!$this->params->get('profile', 1)) {
            return;
        }

        $event->stopPropagation();
        $event->setProfile(new KunenaProfileKunena($this->params));
    }
}
