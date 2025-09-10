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

namespace Kunena\Forum\Plugin\Kunena\Comprofiler\Extension;

\defined('_JEXEC') or die();

use CBLib\Core\CBLib;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Event\KunenaGetActivityEvent;
use Kunena\Forum\Libraries\Event\KunenaGetAvatarEvent;
use Kunena\Forum\Libraries\Event\KunenaGetPrivateEvent;
use Kunena\Forum\Libraries\Event\KunenaGetProfileEvent;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Plugin\Kunena\Comprofiler\Helper\KunenaAccessComprofiler;
use Kunena\Forum\Plugin\Kunena\Comprofiler\Helper\KunenaAvatarComprofiler;
use Kunena\Forum\Plugin\Kunena\Comprofiler\Helper\KunenaProfileComprofiler;
use Kunena\Forum\Plugin\Kunena\Comprofiler\Helper\KunenaLoginComprofiler;
use Kunena\Forum\Plugin\Kunena\Comprofiler\Helper\KunenaPrivateComprofiler;
use Kunena\Forum\Plugin\Kunena\Comprofiler\Helper\KunenaActivityComprofiler;
use Kunena\Forum\Libraries\Forum\KunenaForum;

/**
 * Class Kunena
 *
 * @since   Kunena 6.0
 */
class Comprofiler extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Load language file for front-end translations
     *
     * @var boolean
     */
    protected $autoloadLanguage = \true;

    /**
     * @var     string  CB version 2.10 works with Php 8.1 and with Joomla! 5.2
     * @since   Kunena 6.0
     */
    public $minCBVersion = '2.10';

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
            $mapping['onKunenaDisplay']          = 'onKunenaDisplay';
            $mapping['onKunenaGetAccessControl'] = 'onKunenaGetAccessControl';
            $mapping['onKunenaGetActivity']      = 'onKunenaGetActivity';
            $mapping['onKunenaGetAvatar']        = 'onKunenaGetAvatar';
            $mapping['onKunenaGetLogin']         = 'onKunenaGetLogin';
            $mapping['onKunenaGetPrivate']       = 'onKunenaGetPrivate';
            $mapping['onKunenaGetProfile']       = 'onKunenaGetProfile';
            $mapping['onKunenaGetProfile']       = 'onKunenaGetProfile';
            $mapping['onKunenaPrepare']          = 'onKunenaPrepare';

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
        global $_PLUGINS;

        // Do not load if Kunena version is not supported or Kunena is offline
        if (!(class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('6.4') && KunenaForum::enabled())) {
            return;
        }

        $app = Factory::getApplication();

        // Do not load if CommunityBuilder is not installed
        if (
            (!\file_exists(JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php')) ||
            (!\file_exists(JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php'))
        ) {
            return;
        }

        require_once JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php';

        $this->loadLanguage('plg_kunena_comprofiler.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_comprofiler.sys', JPATH_ADMINISTRATOR . '/components/com_kunena');

        if (\version_compare($this->minCBVersion, CBLib::version(), '>=')) {
            if ($app->isClient('administrator')) {
                $app->enqueueMessage(Text::sprintf('PLG_KUNENA_COMPROFILER_WARN_VERSION', $this->minCBVersion), 'notice');
            }
            return;
        }

        cbimport('cb.html');
        cbimport('language.front');

        $_PLUGINS->loadPluginGroup('user');

        parent::__construct($config);
    }


    /**
     * @param   string  $type    type
     * @param   null    $view    view
     * @param   null    $params  params
     *
     * @return  void
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public function onKunenaDisplay(string $type, $view = null, $params = null): void
    {
        $integration = KunenaFactory::getProfile();

        if (!$integration instanceof KunenaProfileComprofiler) {
            return;
        }

        switch ($type) {
            case 'start':
                $integration->open();
                break;
            case 'end':
                $integration->close();
        }
    }

    /**
     * @param   string  $context  context
     * @param   object  $item     items
     * @param   object  $params   params
     * @param   int     $page     page
     *
     * @return  void
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public function onKunenaPrepare(string $context, &$item, object $params, $page = 0): void
    {
        if ($context == 'kunena.user') {
            $triggerParams = ['userid' => $item->userid, 'userinfo' => &$item];
            $integration   = KunenaFactory::getProfile();

            if ($integration instanceof KunenaProfileComprofiler) {
                KunenaProfileComprofiler::trigger('profileIntegration', $triggerParams);
            }
        }
    }

    /**
     * Get Kunena access control object.
     *
     * @return  KunenaAccessComprofiler|void
     *
     * @since   Kunena 6.0
     */
    public function onKunenaGetAccessControl()
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('access', 1)) {
            return;
        }

        return new KunenaAccessComprofiler($this->params);
    }

    /**
     * Get Kunena login integration object.
     *
     * @return  KunenaLoginComprofiler|void
     *
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

        return new KunenaLoginComprofiler($this->params);
    }

    /**
     * Get Kunena avatar integration object.
     *
     * @return  void
     *
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

        $event->setAvatar(new KunenaAvatarComprofiler($this->params));
    }

    /**
     * Get Kunena profile integration object.
     *
     * @return  void
     *
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

        $event->setProfile(new KunenaProfileComprofiler($this->params));
    }

    /**
     * Get Kunena private message integration object.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function onKunenaGetPrivate(KunenaGetPrivateEvent $event)
    {
        global $_PLUGINS;

        if (!isset($this->params)) {
            return;
        }

        if (!$_PLUGINS->getLoadedPlugin('user', 'pms.mypmspro')) {
            return;
        }

        if (!$this->params->get('private', 1)) {
            return;
        }

        $event->setPrivate(new KunenaPrivateComprofiler($this->params));
    }

    /**
     * Get Kunena activity stream integration object.
     *
     * @return void
     *
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

        $event->setActivity(new KunenaActivityComprofiler($this->params));
    }
}
