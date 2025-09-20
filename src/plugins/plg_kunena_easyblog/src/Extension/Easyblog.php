<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easyblog
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Easyblog\Extension;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Event\KunenaGetAvatarEvent;
use Kunena\Forum\Libraries\Event\KunenaGetProfileEvent;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Plugin\Kunena\Easyblog\Helper\KunenaAvatarEasyblog;
use Kunena\Forum\Plugin\Kunena\Easyblog\Helper\KunenaProfileEasyblog;

/**
 * Class plgKunenaEasyblog
 *
 * @since Kunena
 */
class Easyblog extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
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
            $mapping['onKunenaGetAvatar']        = 'onKunenaGetAvatar';
            $mapping['onKunenaGetProfile']       = 'onKunenaGetProfile';

            if ($app->isClient('site')) {
                // Only allowed in the frontend
            } elseif ($app->isClient('administrator')) {
                // Only allowed in the backend
            }
        }

        return $mapping;
    }

    /**
     * plgKunenaEasyblog constructor.
     *
     * @param  array $config
     */
    public function __construct(array $config = [])
    {
        // Do not load if Kunena version is not supported or Kunena is offline
        if (!(\class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('6.4') && KunenaForum::enabled())) {
            return;
        }

        // Do not load if Easyblog is not installed
        $path = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

        if (!\is_file($path)) {
            return;
        }

        include_once $path;

        parent::__construct($config);

        $this->loadLanguage('plg_kunena_easyblog.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_easyblog.sys', JPATH_ADMINISTRATOR . '/components/com_kunena');
    }

    /**
     * Get Kunena avatar integration object.
     * 
     * @param   KunenaGetAvatarEvent  $event  The event instance
     *
     * @return void
     * @since Kunena
     */
    public function onKunenaGetAvatar(KunenaGetAvatarEvent $event): void
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('avatar', 1)) {
            return;
        }

        require_once __DIR__ . "/KunenaAvatarEasyblog.php";

        $event->stopPropagation();
        $event->setAvatar(new KunenaAvatarEasyblog($this->params));
    }

    /**
     * Get Kunena profile integration object.
     * 
     * @param   KunenaGetProfileEvent  $event  The event instance
     *
     * @return void
     * @since Kunena
     */
    public function onKunenaGetProfile(KunenaGetProfileEvent $event)
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('profile', 1)) {
            return;
        }

        require_once __DIR__ . "/KunenaProfileEasyblog.php";

        $event->stopPropagation();
        $event->setProfile(new KunenaProfileEasyblog($this->params));
    }
}
