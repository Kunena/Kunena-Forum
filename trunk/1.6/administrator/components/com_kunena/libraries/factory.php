<?php
/**
* @version $Id$
* Kunena Component - Kunena Factory
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.org All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
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
	 * Get a Kunena template object
	 *
	 * Returns the global {@link KunenaTemplate} object, only creating it if it doesn't already exist.
	 *
	 * @return object KunenaTemplate
	 */
	public static function getTemplate($name = null)
	{
		kimport('template');
		return KunenaTemplate::getInstance($name);
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
	 * Get Kunena private message system integration object
	 *
	 * Returns the global {@link KunenaPrivate} object, only creating it if it doesn't already exist.
	 *
	 * @return object KunenaPrivate
	 */
	public static function getPrivateMessaging()
	{
		kimport('integration.private');
		return KunenaPrivate::getInstance();
	}

	/**
	 * Get Kunena activity integration object
	 *
	 * Returns the global {@link KunenaActivity} object, only creating it if it doesn't already exist.
	 *
	 * @return object KunenaActivity
	 */
	public static function getActivityIntegration()
	{
		kimport('integration.activity');
		return KunenaActivity::getInstance();
	}

	/**
	 * Get Kunena profile integration object
	 *
	 * Returns the global {@link KunenaProfile} object, only creating it if it doesn't already exist.
	 *
	 * @return object KunenaProfile
	 */
	public static function getProfile()
	{
		kimport('integration.profile');
		return KunenaProfile::getInstance();
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

	/**
	 * Load Kunena language file
	 *
	 * Helper function for external modules and plugins to load the main Kunena language file(s)
	 *
	 */
	public static function loadLanguage( $file = 'com_kunena', $reload = false )
	{
		static $lang = null;

		if ($lang == null or $reload == true) {
			$lang = JFactory::getLanguage();
			$lang->load($file, JPATH_SITE, null, $reload);
		}
	}
}