<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Integration
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Integration;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Kunena\Forum\Administrator\Event\KunenaGetProfileEvent;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * Class KunenaProfile
 *
 * @since   Kunena 6.0
 */
class KunenaProfile
{
    /**
     * @var     ?KunenaProfile
     * @since   Kunena 6.0
     */
    protected static ?KunenaProfile $instance = \null;

    /**
     * @var   boolean
     * @since Kunena 5.2
     */
    public bool $enabled = \true;

    /**
     * Function to get the active KunenaProfle instance from the integration
     * 
     * @return  KunenaProfile
     * @throws  \Exception
     * @since   Kunena 6.0
     */
    public static function getInstance(): KunenaProfile
    {
        if (\null === self::$instance) {
            PluginHelper::importPlugin('kunena');

            $profileEvent = new KunenaGetProfileEvent('onKunenaGetProfile');
            Factory::getApplication()->getDispatcher()->dispatch('onKunenaGetProfile', $profileEvent);

            $profile = $profileEvent->getProfile();

            if ($profile instanceof KunenaProfile) {
                self::$instance = $profile;
            } elseif (\is_array($profile) && $profile[0] instanceof KunenaProfile) {
                self::$instance = $profile[0];
            } else {
                self::$instance = new self();
            }
        }

        return self::$instance;
    }

    /**
     * Function to get Top Hits
     * 
     * @param   int  $limit  limit
     *
     * @return  array
     * @since   Kunena 6.0
     * @throws  \Exception
     */
    public function getTopHits(int $limit = 0): array
    {
        if (!$limit) {
            $limit = KunenaFactory::getConfig()->popUserCount;
        }

        return (array) $this->getTopHitsArray($limit);
    }

    /**
     * Function to get Top Hits
     * 
     * @param   int  $limit  limit
     *
     * @return  array
     * @since   Kunena 6.0
     */
    protected function getTopHitsArray(int $limit = 0): array
    {
        return [];
    }

    /**
     * Function to get Statistics URL
     * 
     * @param   string  $action  action
     * @param   bool    $xhtml   xhtml
     *
     * @return  string
     * @since   Kunena 5.0
     * @throws  \Exception
     */
    public function getStatisticsURL(string $action = '', bool $xhtml = \true): string
    {
        $config = KunenaFactory::getConfig();
        $my     = Factory::getApplication()->getIdentity();

        if ($config->statsLinkAllowed == 0 && $my->id == 0) {
            return false;
        }

        return KunenaRoute::_('index.php?option=com_kunena&view=statistics' . $action, $xhtml);
    }

    /**
     * Function to get User List URL
     * 
     * @param   string  $action  action
     * @param   bool    $xhtml   xhtml
     *
     * @return  string|false
     * @since   Kunena 5.0
     */
    public function getUserListURL(string $action = '', bool $xhtml = \true): string|false
    {
        return '';
    }

    /**
     * Function to get User Profile URL
     * 
     * @param   int     $userid     userid
     * @param   string  $task       task
     * @param   bool    $xhtml      xhtml
     * @param   string  $avatarTab  avatarTab
     *
     * @return  string|false
     * @since   Kunena 5.0
     * @throws  \Exception
     */
    public function getProfileURL(int $userid, $task = '', bool $xhtml = true, string $avatarTab = ''): string|false
    {
        return '';
    }

    /**
     * Function to get the User Profile
     * 
     * @param   KunenaLayout  $view    view
     * @param   Registry      $params  params
     *
     * @return  string
     * @since   Kunena 5.0
     */
    public function showProfile(KunenaLayout $view, Registry $params): string
    {
        return '';
    }

    /**
     * Function to get User Profile Edit URL
     * 
     * @param   int   $userid  userid
     * @param   bool  $xhtml   xhtml
     *
     * @return  string|false
     * @since   Kunena 5.0
     * @throws  \Exception
     */
    public function getEditProfileURL(int $userid, bool $xhtml = true): string|false
    {
        return '';
    }
}
