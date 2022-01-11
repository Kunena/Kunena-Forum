<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Factory;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Integration\KunenaActivity;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\Integration\KunenaPrivate;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\Session\KunenaSession;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use KunenaAdminTemplate;

/**
 * Class KunenaFactory
 *
 * @since   Kunena 6.0
 */
abstract class KunenaFactory
{
	/**
	 * @var     void
	 * @since   Kunena 6.0
	 */
	public static $session = null;

	/**
	 * Get a Kunena template object
	 *
	 * Returns the global {@link KunenaTemplate} object, only creating it if it doesn't already exist.
	 *
	 * @return  KunenaAdminTemplate
	 *
	 * @since   Kunena 6.0
	 */
	public static function getAdminTemplate()
	{
		require_once KPATH_ADMIN . '/tmpl/template.php';

		return new KunenaAdminTemplate;
	}

	/**
	 * Get a Kunena template object
	 *
	 * Returns the global {@link Template} object, only creating it if it doesn't already exist.
	 *
	 * @param   string  $name  name
	 *
	 * @return  KunenaTemplate
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getTemplate($name = null): KunenaTemplate
	{
		return KunenaTemplate::getInstance($name);
	}

	/**
	 * Get Kunena user object
	 *
	 * Returns the global {@link KunenaUser} object, only creating it if it doesn't
	 * already exist.
	 *
	 * @param   int   $id      The user to load - Can be an integer or string - If string, it is converted to Id
	 *                         automatically.
	 * @param   bool  $reload  reload
	 *
	 * @return  KunenaUser
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getUser($id = null, $reload = false): KunenaUser
	{
		return KunenaUserHelper::get($id, $reload);
	}

	/**
	 * Get Kunena session object
	 *
	 * Returns the global {@link Session} object, only creating it if it doesn't already exist.
	 *
	 * @param   array|bool  $update  An array containing session options
	 *
	 * @return \Kunena\Forum\Libraries\Session\KunenaSession|null
	 *
	 * @since   Kunena 6.0
	 * @throws \Exception
	 */
	public static function getSession($update = false): ?KunenaSession
	{
		if (!\is_object(self::$session))
		{
			self::$session = KunenaSession::getInstance($update);
		}

		return self::$session;
	}

	/**
	 * @param   boolean  $session  null
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public static function setSession(bool $session): void
	{
		self::$session = $session;
	}

	/**
	 * Get Kunena avatar integration object
	 *
	 * Returns the global {@link KunenaAvatar} object, only creating it if it doesn't
	 * already exist.
	 *
	 * @return  KunenaAvatar
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getAvatarIntegration(): KunenaAvatar
	{
		return KunenaAvatar::getInstance();
	}

	/**
	 * Get Kunena private message system integration object
	 *
	 * Returns the global {@link KunenaPrivate} object, only creating it if it doesn't already exist.
	 *
	 * @return   KunenaPrivate
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getPrivateMessaging(): KunenaPrivate
	{
		return KunenaPrivate::getInstance();
	}

	/**
	 * Get Kunena activity integration object
	 *
	 * Returns the global {@link Activity} object, only creating it if it doesn't already exist.
	 *
	 * @return  KunenaActivity
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getActivityIntegration(): KunenaActivity
	{
		return KunenaActivity::getInstance();
	}

	/**
	 * Get Kunena profile integration object
	 *
	 * Returns the global {@link KunenaProfile} object, only creating it if it doesn't already exist.
	 *
	 * @return  KunenaProfile
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getProfile(): KunenaProfile
	{
		return KunenaProfile::getInstance();
	}

	/**
	 * Load Kunena language file
	 *
	 * Helper function for external modules and plugins to load the main Kunena language file(s)
	 *
	 * @param   string  $file    file
	 * @param   string  $client  client
	 *
	 * @return  mixed
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function loadLanguage($file = 'com_kunena', $client = 'site'): bool
	{
		static $loaded = [];
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if ($client == 'site')
		{
			$lookup1 = JPATH_SITE;
			$lookup2 = KPATH_SITE;
		}
		else
		{
			$client  = 'admin';
			$lookup1 = JPATH_ADMINISTRATOR;
			$lookup2 = KPATH_ADMIN;
		}

		if (empty($loaded["{$client}/{$file}"]))
		{
			$lang = Factory::getApplication()->getLanguage();

			$english = false;

			if ($lang->getTag() != 'en-GB' && !JDEBUG && !$lang->getDebug()
				&& !self::getConfig()->get('debug') && self::getConfig()->get('fallbackEnglish')
			)
			{
				$lang->load($file, $lookup2, 'en-GB', true, false);
				$english = true;
			}

			$loaded[$file] = $lang->load($file, $lookup1, null, $english, false)
				|| $lang->load($file, $lookup2, null, $english, false);
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $loaded[$file];
	}

	/**
	 * Get a Kunena configuration object
	 *
	 * Returns the global {@link KunenaConfig} object, only creating it if it doesn't already exist.
	 *
	 * @return  KunenaConfig
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getConfig(): KunenaConfig
	{
		return KunenaConfig::getInstance();
	}

	/**
	 * @param   object  $lang      language
	 * @param   string  $filename  filename
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	protected static function parseLanguage(object $lang, string $filename): bool
	{
		if (!is_file($filename))
		{
			return false;
		}

		$contents = file_get_contents($filename);
		$contents = str_replace('_QQ_', '"\""', $contents);
		$strings  = @parse_ini_string($contents);

		if (!\is_array($strings))
		{
			$strings = [];
		}

		$lang->_strings = array_merge($lang->_strings, $strings);

		return !empty($strings);
	}
}
