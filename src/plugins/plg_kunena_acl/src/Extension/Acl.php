<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Acl
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Acl\Extension;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Event\KunenaGetAccessControlEvent;
use Kunena\Forum\Plugin\Kunena\Acl\Helper\KunenaAccessAcl;

/**
 * Permission by rank (ACL) for the categories of the forum.
 *
 * @since   Kunena 8.0
 */
class Acl extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Load language file for front-end translations
     *
     * @var     boolean
     * @since   Kunena 8.0
     */
    protected $autoloadLanguage = \true;

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     * @since   Kunena 8.0
     */
    public static function getSubscribedEvents(): array
    {
        $app     = Factory::getApplication();
        $mapping = [];

        if ($app->isClient('site') || $app->isClient('administrator')) {
            $mapping['onKunenaGetAccessControl'] = 'onKunenaGetAccessControl';
        }

        return $mapping;
    }

    /**
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @since   Kunena 8.0
     */
    public function __construct($config)
    {
        parent::__construct($config);

        $this->loadLanguage('plg_kunena_acl.sys');
    }

    /**
     * Function to get the KunenaAccessControl for this integration
     *
     * @param   KunenaGetAccessControlEvent  $event  The event instance
     *
     * @return  void
     * @since   Kunena 8.0
     */
    public function onKunenaGetAccessControl(KunenaGetAccessControlEvent $event)
    {
        if (!$this->params->get('access', 1)) {
            return;
        }

        $event->addResult(new KunenaAccessAcl($this->params));
    }
}
