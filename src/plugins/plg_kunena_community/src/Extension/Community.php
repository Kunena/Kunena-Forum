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
use Kunena\Forum\Libraries\Event\KunenaGetActivityEvent;
use Kunena\Forum\Libraries\Event\KunenaGetAvatarEvent;
use Kunena\Forum\Libraries\Event\KunenaGetPrivateEvent;
use Kunena\Forum\Libraries\Event\KunenaGetProfileEvent;
use Kunena\Forum\Plugin\Kunena\Community\Helper\KunenaAccessCommunity;
use Kunena\Forum\Plugin\Kunena\Community\Helper\KunenaAvatarCommunity;
use Kunena\Forum\Plugin\Kunena\Community\Helper\KunenaProfileCommunity;
use Kunena\Forum\Plugin\Kunena\Community\Helper\KunenaLoginCommunity;
use Kunena\Forum\Plugin\Kunena\Community\Helper\KunenaPrivateCommunity;
use Kunena\Forum\Plugin\Kunena\Community\KunenaActivityCommunity;

/**
 * Class Kunena
 *
 * @since   Kunena 6.0
 */
class Community extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
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
     * @since   Kunena 6.5
     */
    public static function getSubscribedEvents(): array
    {
        $app     = Factory::getApplication();
        $mapping = [];

        if ($app->isClient('site') || $app->isClient('administrator')) {
            $mapping['onKunenaGetAccess'] = 'onKunenaGetAccess';
            $mapping['onKunenaGetActivity'] = 'onKunenaGetActivity';
            $mapping['onKunenaGetAvatar']  = 'onKunenaGetAvatar';
            $mapping['onKunenaGetLogin'] = 'onKunenaGetLogin';
            $mapping['onKunenaGetPrivate'] = 'onKunenaGetPrivate';
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
     * plgKunenaCommunity constructor.
     *
     * @param  array $config
     */
    public function __construct(array $config = [])
    {
        // Do not load if Kunena version is not supported or Kunena is offline
        if (!(class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('6.4') && KunenaForum::enabled())) {
            return;
        }

        // Do not load if JomSocial is not installed
        $path = JPATH_ROOT . '/components/com_community/libraries/core.php';

        if (!\is_file($path)) {
            return;
        }

        include_once $path;

        parent::__construct($config);

        $this->loadLanguage('plg_kunena_community.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_community.sys', JPATH_ADMINISTRATOR . '/components/com_kunena');
    }

    /**
     * Get Kunena access control object.
     *
     * @return  KunenaAccessCommunity|void
     *
     * @todo    Should we remove category ACL integration?
     * @since   Kunena
     */
    public function onKunenaGetAccessControl()
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('access', 1)) {
            return;
        }

        return new KunenaAccessCommunity($this->params);
    }

    /**
     * Get Kunena login integration object.
     *
     * @return  KunenaLoginCommunity|null|void
     * @since   Kunena 6.0
     */
    public function onKunenaGetLogin()
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('login', 1)) {
            return;
        }

        return new KunenaLoginCommunity($this->params);
    }

    /**
     * Get Kunena avatar integration object.
     *
     * @return  void
     * @since   Kunena 6.0
     */
    public function onKunenaGetAvatar(KunenaGetAvatarEvent $event)
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('avatar', 1)) {
            return;
        }

        $event->setAvatar(new KunenaAvatarCommunity($this->params));
    }

    /**
     * Get Kunena profile integration object.
     *
     * @return  void
     * @since   Kunena 6.0
     */
    public function onKunenaGetProfile(KunenaGetProfileEvent $event)
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('profile', 1)) {
            return;
        }

        $event->setProfile(new KunenaProfileCommunity($this->params));
    }

    /**
     * Get Kunena private message integration object.
     *
     * @return  void
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

        $event->setPrivate(new KunenaPrivateCommunity($this->params));
    }

    /**
     * Get Kunena activity stream integration object.
     *
     * @return  void
     * @since   Kunena 6.0
     */
    public function onKunenaGetActivity(KunenaGetActivityEvent $event)
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('activity', 1)) {
            return;
        }

        $event->setActivity(new KunenaActivityCommunity($this->params));
    }
}
