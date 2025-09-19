<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      UddeIM
 *
 * @copyright       Copyright (C) 2008 - 2025 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Uddeim\Extension;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Event\KunenaGetPrivateEvent;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Plugin\Kunena\Uddeim\Helper\KunenaPrivateUddeim;

/**
 * Class PlgKunenaUddeIM
 * @since Kunena
 */
class Uddeim extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
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
            $mapping['onKunenaGetPrivate'] = 'onKunenaGetPrivate';

            if ($app->isClient('site')) {
                // Only allowed in the frontend
            } elseif ($app->isClient('administrator')) {
                // Only allowed in the backend
            }
        }

        return $mapping;
    }

    /**
     * plgKunenaUddeim constructor.
     *
     * @param  array $config
     */
    public function __construct(array $config = [])
    {
        // Do not load if Kunena version is not supported or Kunena is offline
        if (!(\class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('6.4') && KunenaForum::enabled())) {
            return;
        }

        $path = JPATH_SITE . "/components/com_uddeim/uddeim.api.php";

        if (!\is_file($path)) {
            return;
        }

        include_once $path;

        $uddeim = new \uddeIMAPI;

        if ($uddeim->version() < 1) {
            return;
        }

        parent::__construct($config);

        $this->loadLanguage('plg_kunena_uddeim.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_uddeim.sys', JPATH_ADMINISTRATOR . '/components/com_kunena');
    }

    /**
     * Get Kunena private message integration object.
     * 
     * @param   KunenaGetPrivateEvent  $event The event  instance
     * 
     * @return void
     * @since Kunena
     */
    public function onKunenaGetPrivate(KunenaGetPrivateEvent $event): void
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('private', 1)) {
            return;
        }

        $event->stopPropagation();
        $event->setPrivate(new KunenaPrivateUddeim($this->params));
    }
}
