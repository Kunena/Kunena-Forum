<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Comprofiler\Helper;

\defined('_JEXEC') or die();

use CBAuthentication;
use CBLib\Registry\Registry as CBRegistry;
use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Login\KunenaLoginAbstract;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use RuntimeException;

/**
 * Class KunenaLoginComprofiler
 *
 * @since   Kunena 6.0
 */
class KunenaLoginComprofiler extends KunenaLoginAbstract
{
    /**
     * @param   string       $username    username
     * @param   string       $password    password
     * @param   bool         $rememberme  remember me
     * @param   string|null  $secretkey   secret key
     *
     * @return  null|string
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public function loginUser(string $username, string $password, bool $rememberme = false, $secretkey = null)
    {
        cbimport('cb.authentication');
        global $ueConfig;

        $cbAuthenticate = new CBAuthentication();

        $messagesToUser = [];
        $alertmessages  = [];
        $redirect_url   = KunenaRoute::current();

        $loginType   = (isset($ueConfig['login_type']) ? $ueConfig['login_type'] : 0);
        $resultError = $cbAuthenticate->login($username, $password, $rememberme, 1, $redirect_url, $messagesToUser, $alertmessages, $loginType);

        return $resultError ? $resultError : null;
    }

    /**
     * @param   string|null  $return  The URL to go to after the logout (not used).
     *
     * @return  null|string
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function logoutUser($return = null)
    {
        cbimport('cb.authentication');

        $cbAuthenticate = new CBAuthentication();

        $redirect_url = KunenaRoute::current();
        $resultError  = $cbAuthenticate->logout($redirect_url);

        return $resultError ? $resultError : null;
    }

    /**
     * @return  array|boolean|float|integer|object|string|null
     *
     * @since   Kunena 6.0
     */
    public function getRememberMe(): bool
    {
        // The option of the login module of Community Builder, in its module. The query was never given to the database (the last
        // query of the request was run again and its first column read), and it read the table of the extensions with a join to an
        // unknown alias: the option was never what the module says.
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName('params'))
            ->from($db->quoteName('#__modules'))
            ->where($db->quoteName('module') . ' = ' . $db->quote('mod_cblogin'))
            ->where($db->quoteName('published') . ' = 1');
        $db->setQuery($query, 0, 1);

        try {
            $raw_params = (string) $db->loadResult();
        } catch (RuntimeException $e) {
            $raw_params = '';
        }

        $params = new CBRegistry($raw_params);

        return (bool) $params->get('remember_enabled', 1);
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public function getLoginURL(): string
    {
        global $_CB_framework;

        return $_CB_framework->viewUrl('login');
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public function getLogoutURL(): string
    {
        global $_CB_framework;

        return $_CB_framework->viewUrl('logout');
    }

    /**
     * @return  void|string
     *
     * @since   Kunena 6.0
     */
    public function getRegistrationURL()
    {
        global $_CB_framework, $ueConfig;

        $usersConfig = ComponentHelper::getParams('com_users');

        if (
            $ueConfig['reg_admin_allowcbregistration'] == 1
            || ($ueConfig['reg_admin_allowcbregistration'] == 0 && $usersConfig->get('allowUserRegistration'))
        ) {
            return $_CB_framework->viewUrl('registers');
        }

        return;
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public function getResetURL(): string
    {
        global $_CB_framework;

        return $_CB_framework->viewUrl('lostpassword');
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public function getRemindURL(): string
    {
        global $_CB_framework;

        return $_CB_framework->viewUrl('lostpassword');
    }
}
