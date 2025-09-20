<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Joomla
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Joomla\Extension;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Event\KunenaGetAccessControlEvent;
use Kunena\Forum\Libraries\Event\KunenaGetLoginEvent;
use Kunena\Forum\Plugin\Kunena\Joomla\Helper\KunenaAccessJoomla;
use Kunena\Forum\Plugin\Kunena\Joomla\Helper\KunenaLoginJoomla;

/**
 * Class Joomla
 *
 * @since   Kunena 6.0
 */
class Joomla extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
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
            $mapping['onKunenaGetAccessControl'] = 'onKunenaGetAccessControl';
            $mapping['onKunenaGetLogin']         = 'onKunenaGetLogin';

            if ($app->isClient('site')) {
                // Only allowed in the frontend
            } elseif ($app->isClient('administrator')) {
                // Only allowed in the backend
            }
        }

        return $mapping;
    }

    /**
     * Joomla constructor.
     *
     * @param   array   $config    An optional associative array of configuration settings.
     *                             Recognized key values include 'name', 'group', 'params', 'language'
     *                             (this list is not meant to be comprehensive).
     *
     * @since   Kunena 6.0
     */
    public function __construct($config)
    {
        parent::__construct($config);

        $this->loadLanguage('plg_kunena_joomla.sys');
    }

    /**
     * Function to get the KunenaAccessControl for this integration
     * 
     * @param   KunenaGetAccessControlEvent  $event  The event instance
     *
     * @return  void
     * @since   Kunena 6.0
     */
    public function onKunenaGetAccessControl(KunenaGetAccessControlEvent $event)
    {
        if (!$this->params->get('access', 1)) {
            return;
        }

        $event->addResult(new KunenaAccessJoomla($this->params));
    }

    /**
     * Function to get the KunenaLogin for this integration
     * 
     * @param   KunenaGetLoginEvent  $event  The event instance
     *
     * @return  void
     * @since   Kunena 6.0
     */
    public function onKunenaGetLogin(KunenaGetLoginEvent $event)
    {
        $event->addResult(new KunenaLoginJoomla($this->params));
    }
}
