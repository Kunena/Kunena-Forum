<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Factory;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Integration\Activity;
use Kunena\Forum\Libraries\Integration\Avatar;
use Kunena\Forum\Libraries\Integration\KunenaPrivate;
use Kunena\Forum\Libraries\Integration\Profile;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\Session\Session;
use Kunena\Forum\Libraries\Template\Template;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use KunenaAdminTemplate;
use function defined;

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
	 * @return  KunenaAdminTemplate|Template
	 *
	 * @since   Kunena 6.0
	 */
	public static function getAdminTemplate()
	{
		require_once KPATH_ADMIN . '/tmpl/template.php';
		$template = new KunenaAdminTemplate;

		return $template;
	}

	/**
	 * Get a Kunena template object
	 *
	 * Returns the global {@link Template} object, only creating it if it doesn't already exist.
	 *
	 * @param   string  $name  name
	 *
	 * @return  Template
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getTemplate($name = null)
	{
		return Template::getInstance($name);
	}

	/**
	 * Get Kunena user object
	 *
	 * Returns the global {@link \Kunena\Forum\Libraries\User\KunenaUser} object, only creating it if it doesn't
	 * already exist.
	 *
	 * @param   int   $id      The user to load - Can be an integer or string - If string, it is converted to Id
	 *                         automatically.
	 * @param   bool  $reload  reload
	 *
	 * @return  KunenaUser
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getUser($id = null, $reload = false)
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
	 * @return  Session
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getSession($update = false)
	{
		if (!is_object(self::$session))
		{
			self::$session = Session::getInstance($update);
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
	public static function setSession($session)
	{
		self::$session = $session;
	}

	/**
	 * Get Kunena avatar integration object
	 *
	 * Returns the global {@link \Kunena\Forum\Libraries\Integration\Avatar} object, only creating it if it doesn't
	 * already exist.
	 *
	 * @return  Avatar
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getAvatarIntegration()
	{
		return Avatar::getInstance();
	}

	/**
	 * Get Kunena private message system integration object
	 *
	 * Returns the global {@link KunenaPrivate} object, only creating it if it doesn't already exist.
	 *
	 * @return   KunenaPrivate
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getPrivateMessaging()
	{
		return KunenaPrivate::getInstance();
	}

	/**
	 * Get Kunena activity integration object
	 *
	 * Returns the global {@link KunenaIntegrationActivity} object, only creating it if it doesn't already exist.
	 *
	 * @return  Activity
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getActivityIntegration()
	{
		return Activity::getInstance();
	}

	/**
	 * Get Kunena profile integration object
	 *
	 * Returns the global {@link Profile} object, only creating it if it doesn't already exist.
	 *
	 * @return  Profile
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getProfile()
	{
		return Profile::getInstance();
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
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function loadLanguage($file = 'com_kunena', $client = 'site')
	{
		static $loaded = [];
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

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
			$lang = Factory::getLanguage();

			$english = false;

			if ($lang->getTag() != 'en-GB' && !JDEBUG && !$lang->getDebug()
				&& !self::getConfig()->get('debug') && self::getConfig()->get('fallback_english')
			)
			{
				$lang->load($file, $lookup2, 'en-GB', true, false);
				$english = true;
			}

			$loaded[$file] = $lang->load($file, $lookup1, null, $english, false)
				|| $lang->load($file, $lookup2, null, $english, false);
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $loaded[$file];
	}

	/**
	 * Get a Kunena configuration object
	 *
	 * Returns the global {@link Config} object, only creating it if it doesn't already exist.
	 *
	 * @return  KunenaConfig
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getConfig()
	{
		return KunenaConfig::getInstance();
	}

	/**
	 * @param   string  $lang      language
	 * @param   string  $filename  filename
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	protected static function parseLanguage($lang, $filename)
	{
		if (!is_file($filename))
		{
			return false;
		}

		// Capture hidden PHP errors from the parsing.
		$php_errormsg = null;

		$contents = file_get_contents($filename);
		$contents = str_replace('_QQ_', '"\""', $contents);
		$strings  = @parse_ini_string($contents);

		if (!is_array($strings))
		{
			$strings = [];
		}

		$lang->_strings = array_merge($lang->_strings, $strings);

		return !empty($strings);
	}
}
