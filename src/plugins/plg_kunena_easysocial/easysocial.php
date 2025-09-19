<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easysocial
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @copyright       Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
 * @license         GNU/GPL, see LICENSE.php
 * EasySocial is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Unauthorized Access');

use Kunena\Forum\Libraries\Event\KunenaGetActivityEvent;
use Kunena\Forum\Libraries\Event\KunenaGetAvatarEvent;
use Kunena\Forum\Libraries\Event\KunenaGetLoginEvent;
use Kunena\Forum\Libraries\Event\KunenaGetPrivateEvent;
use Kunena\Forum\Libraries\Event\KunenaGetProfileEvent;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Plugin\Kunena\Easysocial\KunenaAvatarEasySocial;
use Kunena\Forum\Plugin\Kunena\Easysocial\KunenaProfileEasySocial;
use Kunena\Forum\Plugin\Kunena\Easysocial\KunenaLoginEasySocial;
use Kunena\Forum\Plugin\Kunena\Easysocial\KunenaPrivateEasySocial;
use Kunena\Forum\Plugin\Kunena\Easysocial\KunenaActivityEasySocial;

$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/plugins.php';

if (!is_file($file)) {
    return;
}

require_once $file;

/**
 * @package     Kunena
 *
 * @since       Kunena 5.0
 */
class PlgKunenaEasySocial extends EasySocialPlugins
{
    public $params;

    /**
     * plgKunenaEasySocial constructor.
     *
     * @param   object  $subject                The object to observe
     * @param   array   $config                 An optional associative array of configuration settings.
     *                                          Recognized key values include 'name', 'group', 'params', 'language'
     *                                          (this list is not meant to be comprehensive).
     *
     * @throws Exception
     * @since   Kunena 5.0
     */
    public function __construct(object $subject, $config = [])
    {
        // Do not load if Kunena version is not supported or Kunena is offline
        if (!(class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('6.4') && KunenaForum::enabled())) {
            return true;
        }

        parent::__construct($subject, $config);

        $this->loadLanguage('plg_kunena_easysocial.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_easysocial.sys', JPATH_ADMINISTRATOR . '/components/com_kunena');

        return true;
    }

    /**
     * Get Kunena login integration object.
     * 
     * @param   KunenaGetLoginEvent  $event  The event instance
     * 
     * @return  void
     * @since   Kunena 5.0
     */
    public function onKunenaGetLogin(KunenaGetLoginEvent $event): void
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('login', 1)) {
            return;
        }

        $event->addResult(new KunenaLoginEasySocial($this->params));
    }

    /**
     * Get Kunena avatar integration object.
     * 
     * @param   KunenaGetAvatarEvent  $event  The event instance
     *
     * @return  void
     * @since   Kunena 5.0
     */
    public function onKunenaGetAvatar(KunenaGetAvatarEvent $event): void
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('avatar', 1)) {
            return;
        }

        $event->stopPropagation();
        $event->setAvatar(new KunenaAvatarEasySocial($this->params));
    }

    /**
     * Get Kunena profile integration object.
     * 
     * @param   KunenaGetProfileEvent  $event  The event instance
     *
     * @return  void
     *
     * @since   Kunena 5.0
     */
    public function onKunenaGetProfile(KunenaGetProfileEvent $event)
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('profile', 1)) {
            return;
        }

        $event->stopPropagation();
        $event->setProfile(new KunenaProfileEasySocial($this->params));
    }

    /**
     * Get Kunena private message integration object.
     * 
     * @param   KunenaGetPrivateEvent  $event The event  instance
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function onKunenaGetPrivate(KunenaGetPrivateEvent $event)
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('private', 1)) {
            return;
        }

        $event->stopPropagation();
        $event->setPrivate(new KunenaPrivateEasySocial($this->params));
    }

    /**
     * Get Kunena activity stream integration object.
     *
     * @param   KunenaGetActivityEvent  $event  The event instance
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function onKunenaGetActivity(KunenaGetActivityEvent $event): void
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('activity', 1)) {
            return;
        }

        $event->addResult(new KunenaActivityEasySocial($this->params));
    }
}
