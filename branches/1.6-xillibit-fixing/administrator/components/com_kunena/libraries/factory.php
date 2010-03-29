<?php
/**
* @version $Id$
* Kunena Component - Kunena Factory
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

abstract class KunenaFactory {
	static $session = null;

	/**
	 * Get a Kunena configuration object
	 *
	 * Returns the global {@link CKunenaConfig} object, only creating it if it doesn't already exist.
	 *
	 * @return object CKunenaConfig
	 */
	public static function getConfig()
	{
		require_once(KPATH_SITE.'/lib/kunena.config.class.php');
		return CKunenaConfig::getInstance();
	}

	/**
	 * Get Kunena user object
	 *
	 * Returns the global {@link KunenaUser} object, only creating it if it doesn't already exist.
	 *
	 * @param	int	$id	The user to load - Can be an integer or string - If string, it is converted to Id automatically.
	 *
	 * @return object KunenaUser
	 */
	static public function getUser($id = null, $reload = false) {
		kimport('user');
		return KunenaUser::getInstance($id, $reload);
	}

	/**
	 * Get Kunena session object
	 *
	 * Returns the global {@link KunenaSession} object, only creating it if it doesn't already exist.
	 *
	 * @param array An array containing session options
	 * @return object KunenaSession
	 */
	public static function getSession($update = false)
	{
		kimport('session');
		if (!is_object(KunenaFactory::$session)) {
			KunenaFactory::$session = KunenaSession::getInstance($update);
		}
		return KunenaFactory::$session;
	}

	/**
	 * Get Kunena login object
	 *
	 * Returns the global {@link KunenaLogin} object, only creating it if it doesn't already exist.
	 *
	 * @return object KunenaLogin
	 */
	public static function getLogin()
	{
		kimport('integration.login');
		return KunenaLogin::getInstance();
	}

	/**
	 * Get Kunena avatar integration object
	 *
	 * Returns the global {@link KunenaAvatar} object, only creating it if it doesn't already exist.
	 *
	 * @return object KunenaAvatar
	 */
	public static function getAvatarIntegration()
	{
		kimport('integration.avatar');
		return KunenaAvatar::getInstance();
	}

	/**
	 * Get Kunena access control object
	 *
	 * Returns the global {@link KunenaAccess} object, only creating it if it doesn't already exist.
	 *
	 * @return object KunenaAccess
	 */
	public static function getAccessControl()
	{
		kimport('integration.access');
		return KunenaAccess::getInstance();
	}
}