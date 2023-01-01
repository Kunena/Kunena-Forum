<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easyprofile
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * Class KunenaProfileEasyprofile
 *
 * @since   Kunena 6.0
 */
class KunenaProfileEasyprofile extends KunenaProfile
{
    /**
     * @var     null
     * @since   Kunena 6.0
     */
    protected $params = null;

    /**
     * KunenaProfileEasyprofile constructor.
     *
     * @param   object  $params  params
     *
     * @since   Kunena 6.0
     */
    public function __construct(object $params)
    {
        $this->params = $params;
    }

    /**
     * @param   string  $action  action
     * @param   bool    $xhtml   xhtml
     *
     * @return string
     *
     * @since   Kunena 6.0
     *@throws Exception
     */
    public function getUserListURL(string $action = '', bool $xhtml = true): string
    {
        $config = KunenaFactory::getConfig();
        $my     = Factory::getApplication()->getIdentity();

        if ($config->userlistAllowed == 0 && $my->id == 0) {
            return false;
        }

        if ($this->params->get('userlist', 0) == 0) {
            return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list' . $action, $xhtml);
        }

        return Route::_('index.php?option=com_jsn&view=list&Itemid=' . $this->params->get('menuitem', ''), false);
    }

    /**
     * @param   KunenaLayout  $view    view
     * @param   object        $params  params
     *
     * @return   void
     *
     * @since   Kunena 6.0
     */
    public function showProfile(KunenaLayout $view, object $params)
    {
    }

    /**
     * @param   int   $userid  userid
     * @param   bool  $xhtml   xhtml
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     */
    public function getEditProfileURL(int $userid, bool $xhtml = true): string
    {
        return $this->getProfileURL($userid, 'edit', $xhtml);
    }

    /**
     * @param   int     $userid     userid
     * @param   string  $task       task
     * @param   bool    $xhtml      xhtml
     * @param   string  $avatarTab  avatartab
     *
     * @return  boolean|string
     *
     * @since   Kunena 5.0
     */
    public function getProfileURL(int $userid, string $task = '', bool $xhtml = true, string $avatarTab = '')
    {
        // Make sure that user profile exist.
        if (!$userid || JsnHelper::getUser($userid) === null) {
            return false;
        }

        $user = JsnHelper::getUser($userid);

        return $user->getLink();
    }

    /**
     * Return username of user
     *
     * @param   KunenaUser  $user         user
     * @param   string      $visitorname  name
     * @param   bool        $escape       escape
     *
     * @return string
     * @since Kunena 5.2
     */
    public function getProfileName(KunenaUser $user, string $visitorname = '', bool $escape = true): string
    {
        $config     = ComponentHelper::getParams('com_jsn');
        $formatName = $config->get('formatname', 'NAME');

        if ($formatName == 'NAME') {
            return JsnHelper::getUser($user->id)->name;
        } elseif ($formatName == 'USERNAME') {
            return JsnHelper::getUser($user->id)->username;
        } elseif ($formatName == 'NAMEUSERNAME') {
            return JsnHelper::getUser($user->id)->name . ' (' . JsnHelper::getUser($user->id)->username . ')';
        } else {
            return JsnHelper::getUser($user->id)->username . ' (' . JsnHelper::getUser($user->id)->name . ')';
        }
    }
}
