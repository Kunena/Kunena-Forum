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

namespace Kunena\Forum\Plugin\Kunena\Gravatar\Extension;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Event\KunenaGetAvatarEvent;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Plugin\Kunena\Gravatar\Helper\KunenaAvatarGravatar;

/**
 * Class plgKunenaGravatar
 *
 * @since Kunena
 */
class Gravatar extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
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

        parent::__construct($config);
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
        if (!$this->params->get('avatar', 1)) {
            return;
        }

        require_once KPATH_FRAMEWORK . '/External/Emberlabs/Gravatar.php';

        $event->stopPropagation();
        $event->setAvatar(new KunenaAvatarGravatar($this->params));
    }
}
