<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Kunena
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Kunena\Helper;

\defined('_JEXEC') or die();

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class KunenaProfile
 *
 * @since   Kunena 5.0
 */
class KunenaProfileKunena extends KunenaProfile
{
    /**
     * @param   Registry  $params  params
     *
     * @since   Kunena 5.0
     */
    public function __construct(Registry $params)
    {
        $this->params = $params;
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
        $config = KunenaFactory::getConfig();
        $my     = Factory::getApplication()->getIdentity();

        if ($config->userlistAllowed == 0 && $my->id == 0) {
            return \false;
        }

        return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list' . $action, $xhtml);
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
        /** @var DatabaseDriver $db */
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->select($db->quoteName(['u.id', 'ku.uhits'], [null, 'count']));
        $query->from($db->quoteName(['#__kunena_users'], ['ku']));
        $query->join('INNER', $db->quoteName('#__users', 'u'), $db->quoteName('u.id') . ' = ' . $db->quoteName('ku.userid'));
        $query->where($db->quoteName('ku.uhits') . ' > 0');
        $query->order($db->quoteName('ku.uhits') . ' DESC');

        if (KunenaFactory::getConfig()->superAdminUserlist) {
            $filter = Access::getUsersByGroup(8);
            $query->where('u.id NOT IN (' . implode(',', $filter) . ')');
        }

        $query->setLimit($limit);
        $db->setQuery($query);

        try {
            $top = $db->loadObjectList() ?? [];
        } catch (\RuntimeException $e) {
            KunenaError::displayDatabaseError($e);
        }

        return $top;
    }

    /**
     * Function to get the User Profile
     * 
     * @param   KunenaLayout  $view    view
     * @param   Registry      $params  params
     *
     * @return  string
     *
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
     */
    public function getEditProfileURL(int $userid, bool $xhtml = true): string|false
    {
        $avatartab = '&avatartab=1';

        return $this->getProfileURL($userid, 'edit', $xhtml, $avatartab);
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
        if ($userid == 0) {
            return false;
        }

        if (!($userid instanceof KunenaUser)) {
            $user = KunenaUserHelper::get($userid);
        }

        if ($user === false) {
            return false;
        }

        $userid = "&userid={$user->userid}";

        if ($task && $task != 'edit') {
            throw new \Exception('Sorry, Kunena 6.0 no support url with func in method getProfileURL class KunenaProfileKunena');
        }

        $layout = $task ? '&layout=' . $task : '';

        if ($layout) {
            return KunenaRoute::_("index.php?option=com_kunena&view=user{$layout}{$userid}{$avatarTab}", $xhtml);
        }

        return KunenaRoute::getUserUrl($user, $xhtml);
    }

    /**
     * Get the name of the user from this profile
     *
     * @param   KunenaUser  $user         user
     * @param   string      $visitorname  name
     * @param   bool        $escape       escape
     *
     * @return  string
     * @since   Kunena 5.2
     * @throws  \Exception
     * @see     KunenaProfile::getProfileName()
     */
    public function getProfileName(KunenaUser $user, string $visitorname = '', bool $escape = true): string
    {
        $config = KunenaFactory::getConfig();

        if (!$user->userid && !$user->name) {
            $name = $visitorname;
        } else {
            $name = $config->username ? $user->username : $user->name;
        }

        if ($escape) {
            $name = \htmlspecialchars($name, \ENT_COMPAT, 'UTF-8');
        }

        return $name;
    }
}
