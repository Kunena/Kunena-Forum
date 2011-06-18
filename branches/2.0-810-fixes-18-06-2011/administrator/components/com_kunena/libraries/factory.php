<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

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
		kimport('kunena.template');
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
		kimport('kunena.user.helper');
		return KunenaUserHelper::get($id, $reload);
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
		kimport('kunena.session');
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
		require_once KPATH_ADMIN . '/libraries/integration/login.php';
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
		require_once KPATH_ADMIN . '/libraries/integration/avatar.php';
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
		require_once KPATH_ADMIN . '/libraries/integration/private.php';
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
		require_once KPATH_ADMIN . '/libraries/integration/activity.php';
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
		require_once KPATH_ADMIN . '/libraries/integration/profile.php';
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
		require_once KPATH_ADMIN . '/libraries/integration/access.php';
		return KunenaAccess::getInstance();
	}

	/**
	 * Load Kunena language file
	 *
	 * Helper function for external modules and plugins to load the main Kunena language file(s)
	 *
	 */
	public static function loadLanguage( $file = 'com_kunena' )
	{
		static $loaded = array();
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if (empty($loaded[$file])) {
			$version = new JVersion();
			$lang = JFactory::getLanguage();
			if ($version->RELEASE == '1.5' && !$lang->getDebug()) {
				$filename = JLanguage::getLanguagePath( JPATH_BASE, $lang->_lang)."/{$lang->_lang}.{$file}.ini";
				$loaded[$file] = self::parseLanguage($lang, $filename);
				if (!$loaded[$file]) {
					$filename = JLanguage::getLanguagePath( JPATH_BASE, $lang->_lang)."/{$lang->_default}.{$file}.ini";
					$loaded[$file] = self::parseLanguage($lang, $filename);
				}
			} else {
				$loaded[$file] = $lang->load($file, JPATH_SITE, null, 1);
			}
		}
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return $loaded[$file];
}

	protected function parseLanguage($lang, $filename) {
		if (!file_exists($filename)) return false;
		$version = phpversion();
		if ($version >= '5.3.1') {
			$contents = file_get_contents($filename);
			$contents = str_replace('_QQ_','"\""',$contents);
			$strings = (array) @parse_ini_string($contents);
		} else {
			$strings = (array) @parse_ini_file($filename);
			// _QQ_ is currently not used in Kunena -- we can ignore the following code for now:
			/*if ($version == '5.3.0') {
				foreach($strings as $key => $string) {
					$strings[$key]=str_replace('_QQ_','"',$string);
				}
			}*/
		}
		$lang->_strings = array_merge($lang->_strings, $strings);
		return !empty($strings);
	}
}