<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Site\View\Common\HtmlView;
use stdClass;

/**
 * class KunenaForum
 *
 * Main class for Kunena Forum which is always present if Kunena framework has been installed.
 *
 * This class can be used to detect and initialize Kunena framework and to make sure that your extension
 * is compatible with the current version.
 *
 * @since   Kunena 6.0
 */
abstract class KunenaForum
{
	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	const PUBLISHED = 0;

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	const UNAPPROVED = 1;

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	const DELETED = 2;

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	const TOPIC_DELETED = 3;

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	const TOPIC_CREATION = 4;

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	const MODERATOR = 1;

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	const ADMINISTRATOR = 2;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected static $version = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected static $version_major = false;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected static $version_date = null;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected static $version_name = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected static $version_sampleData = false;

	/**
	 * Checks if Kunena Forum is safe to be used and online.
	 *
	 * It is a good practice to check if Kunena Forum is online before displaying
	 * forum content to the user. It's even more important if you allow user to post
	 * or manipulate forum! By following this practice administrator can have single
	 * point which he can use to be sure that nobody has access to any data inside
	 * his forum.
	 *
	 * Use case: Administrator is upgrading his forum to the next major version and wants
	 * to be sure that everything works before putting forum back to online. He logs in
	 * and can see everything. For everyone else no forum related information is shown.
	 *
	 * <code>
	 * // Check if Kunena Forum has been installed, online and compatible with your code
	 *    if (class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && \Kunena\Forum\Libraries\Forum\KunenaForum::enabled() &&
	 *    \Kunena\Forum\Libraries\Forum\KunenaForum::isCompatible('6.0')) {
	 *        // Initialize the framework (new since 2.0.0)
	 *        \Kunena\Forum\Libraries\Forum\KunenaForum::setup();
	 *        // It's now safe to display something or to save Kunena objects
	 * }
	 * </code>
	 *
	 * @param   boolean  $checkAdmin  True if administrator is considered as a special case.
	 *
	 * @return  boolean True if online.
	 *
	 * @throws  Exception
	 * @see     \Kunena\Forum\Libraries\Forum\KunenaForum::installed()
	 * @see     \Kunena\Forum\Libraries\Forum\KunenaForum::isCompatible()
	 * @since   Kunena 6.0
	 *
	 * @see     \Kunena\Forum\Libraries\Forum\KunenaForum::setup()
	 */
	public static function enabled($checkAdmin = true): bool
	{
		if (!ComponentHelper::isEnabled('com_kunena'))
		{
			return false;
		}

		$config = KunenaFactory::getConfig();

		return !$config->boardOffline
			|| ($checkAdmin && self::installed() && KunenaUserHelper::getMyself()->isAdmin());
	}

	/**
	 * Check if Kunena Forum is safe to be used.
	 *
	 * If installer is running, it's unsafe to use our framework. Files may be currently replaced with
	 * new ones and the database structure might be inconsistent. Using forum during installation will
	 * likely cause fatal errors and data corruption if you attempt to update objects in the database.
	 *
	 * Always detect Kunena in your code before you start using the framework:
	 *
	 * <code>
	 *    // Check if Kunena Forum has been installed and compatible with your code
	 *    if (class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && \Kunena\Forum\Libraries\Forum\KunenaForum::installed() &&
	 * \Kunena\Forum\Libraries\Forum\KunenaForum::isCompatible('6.0')) {
	 *        // Initialize the framework (new since 2.0.0)
	 *        \Kunena\Forum\Libraries\Forum\KunenaForum::setup();
	 *        // Start using the framework
	 *    }
	 * </code>
	 *
	 * @return  boolean True if Kunena has been fully installed.
	 *
	 * @see     \Kunena\Forum\Libraries\Forum\KunenaForum::setup()
	 *
	 * @see     \Kunena\Forum\Libraries\Forum\KunenaForum::enabled()
	 * @see     \Kunena\Forum\Libraries\Forum\KunenaForum::isCompatible()
	 * @since   Kunena 6.0
	 */
	public static function installed(): bool
	{
		return !is_file(KPATH_ADMIN . '/install.php') || self::isDev();
	}

	/**
	 * Check if Kunena Forum is running from a Git repository.
	 *
	 * Developers tend to do their work directly in the Git repositories instead of
	 * creating and installing new builds after every change. This function can be
	 * used to check the condition and make sure we do not break users repository
	 * by replacing files during upgrade.
	 *
	 * @return  boolean True if Git repository is detected.
	 *
	 * @since   Kunena 6.0
	 */
	public static function isDev(): bool
	{
		SELF::version();

		$match = [];
		preg_match('/(-DEV)|(-GIT)/i', SELF::$version, $match);

		if (!empty($match))
		{
			return true;
		}

		return false;
	}

	/**
	 * Initialize Kunena Framework.
	 *
	 * This function initializes Kunena Framework. Main purpose of this
	 * function right now is to make sure all the translations have been loaded,
	 * but later it may contain other initialization tasks.
	 *
	 * Following code gives an example how to create backwards compatible code.
	 * Normally I wouldn't bother supporting deprecated unstable releases.
	 *
	 * <code>
	 *    // We have already checked that Kunena 2.0+ has been installed and is online
	 *
	 *    if (\Kunena\Forum\Libraries\Forum\KunenaForum::isCompatible('2.0.0')) {
	 *        \Kunena\Forum\Libraries\Forum\KunenaForum::setup();
	 *    } else {
	 *        \Kunena\Forum\Libraries\Factory\KunenaFactory::loadLanguage();
	 *    }
	 * </code>
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @see     \Kunena\Forum\Libraries\Forum\KunenaForum::installed()
	 *
	 * Alternatively you could use method_exists() to check that the new API is in there.
	 */
	public static function setup(): void
	{
		$config = KunenaFactory::getConfig();

		// Load language file for libraries.
		KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');

		// Setup output caching.
		$cache = Factory::getCache('com_kunena', 'output');

		if (!$config->get('cache'))
		{
			$cache->setCaching(0);
		}

		$cache->setLifeTime($config->get('cacheTime', 60));

		// Setup error logging.
		$options    = ['logger' => 'w3c', 'text_file' => 'kunena.php'];
		$categories = ['kunena'];
		$levels     = JDEBUG || $config->debug ? Log::ALL :
			Log::EMERGENCY | Log::ALERT | Log::CRITICAL | Log::ERROR;
		Log::addLogger($options, $levels, $categories);
	}

	/**
	 * Check if Kunena Forum is compatible with your code.
	 *
	 * This function can be used to make sure that user has installed Kunena version
	 * that has been tested to work with your extension. All existing functions should
	 * be backwards compatible, but each release can add some new functionality, which
	 * you may want to use.
	 *
	 * <code>
	 *    if (\Kunena\Forum\Libraries\Forum\KunenaForum::isCompatible('2.0.1')) {
	 *        // We can do it in the new way
	 *    } else {
	 *        // Use the old code instead
	 *    }
	 * </code>
	 *
	 * @param   string  $version  Minimum required version.
	 *
	 * @return  boolean Yes, if it is safe to use Kunena Framework.
	 *
	 * @see     \Kunena\Forum\Libraries\Forum\KunenaForum::installed()
	 *
	 * @since   Kunena 6.0
	 */
	public static function isCompatible(string $version): bool
	{
		// If requested version is smaller than 4.0, it's not compatible
		if (version_compare($version, '6.0', '<'))
		{
			return false;
		}

		// Development version support.
		if ($version == '6.0')
		{
			return true;
		}

		// Check if future version is needed (remove GIT and DEVn from the current version)
		if (version_compare($version, preg_replace('/(-DEV\d*)?(-GIT)?/i', '', self::version()), '>'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Returns the exact version from Kunena Forum.
	 *
	 * @return boolean Version number.
	 *
	 * @since   Kunena 6.0
	 */
	public static function version()
	{
		if (self::$version === false)
		{
			self::buildVersion();
		}

		return self::$version;
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected static function buildVersion(): void
	{
		if ('@kunenaversion@' == '@' . 'kunenaversion' . '@')
		{
			$file = JPATH_MANIFESTS . '/packages/pkg_kunena.xml';

			if (file_exists($file))
			{
				$manifest           = simplexml_load_file($file);
				self::$version      = (string) $manifest->version . '-GIT';
				self::$version_date = (string) $manifest->creationDate;
			}
			else
			{
				$db    = Factory::getContainer()->get('DatabaseDriver');
				$query = $db->getQuery(true);
				$query->select('version')->from('#__kunena_version')->order('id');
				$query->setLimit(1);
				$db->setQuery($query);

				self::$version      = $db->loadResult();
				self::$version_date = Factory::getDate()->format('Y-m-d');
			}
		}
		else
		{
			self::$version      = strtoupper('@kunenaversion@');
			self::$version_date = strtoupper('@kunenaversiondate@');
		}

		self::$version_major = substr(self::$version, 0, 3);
		self::$version_name  = ('@kunenaversionname@' == '@' . 'kunenaversionname' . '@') ? 'Git Repository' : '@kunenaversionname@';

		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('sampleData')->from('#__kunena_version')->order('id DESC');
		$query->setLimit(1);
		$db->setQuery($query);

		self::$version_sampleData = $db->loadResult();
	}

	/**
	 * Returns all version information together.
	 *
	 * @return  object stdClass containing (version, major, date, name, sampleData).
	 *
	 * @since   Kunena 6.0
	 */
	public static function getVersionInfo()
	{
		$version             = new stdClass;
		$version->version    = self::version();
		$version->major      = self::versionMajor();
		$version->date       = self::versionDate();
		$version->name       = self::versionName();
		$version->sampleData = self::versionSampleData();

		return $version;
	}

	/**
	 * Returns major version number (2.0, 3.0, 3.1 and so on).
	 *
	 * @return  boolean Major version in xxx.yyy format.
	 *
	 * @since   Kunena 6.0
	 */
	public static function versionMajor(): bool
	{
		if (self::$version_major === false)
		{
			self::buildVersion();
		}

		return self::$version_major;
	}

	/**
	 * Returns build date from Kunena Forum (for Git today).
	 *
	 * @return  string Date in yyyy-mm-dd format.
	 *
	 * @since   Kunena 6.0
	 */
	public static function versionDate(): string
	{
		if (self::$version_date === false)
		{
			self::buildVersion();
		}

		return self::$version_date;
	}

	/**
	 * Returns codename from Kunena release.
	 *
	 * @return  string Codename.
	 *
	 * @since   Kunena 6.0
	 */
	public static function versionName(): string
	{
		if (self::$version_name === false)
		{
			self::buildVersion();
		}

		return self::$version_name;
	}

	/**
	 * Returns boolean if the sample data is installed.
	 *
	 * @return  boolean SampleData.
	 *
	 * @since   Kunena 6.0
	 */
	public static function versionSampleData()
	{
		if (self::$version_sampleData === false)
		{
			self::buildVersion();
		}

		return self::$version_sampleData;
	}

	/**
	 * Displays Kunena Forum view/layout inside your extension.
	 *
	 * <code>
	 *
	 * </code>
	 *
	 * @param   string          $viewName  Name of the view.
	 * @param   string          $layout    Name of the layout.
	 * @param   null|string     $template  Name of the template file.
	 * @param   array|Registry  $params    Extra parameters to control the model.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public static function display(string $viewName, $layout = 'default', $template = null, $params = []): void
	{
		// Filter input
		$viewName = preg_replace('/[^A-Z0-9_]/i', '', $viewName);
		$layout   = preg_replace('/[^A-Z0-9_]/i', '', $layout);
		$template = preg_replace('/[^A-Z0-9_]/i', '', $template);
		$template = $template ? $template : null;

		$view  = 'Kunena\Forum\Site\View\\' . $viewName . '\HtmlView';
		$model = 'Kunena\Forum\Site\Model\\' . $viewName . 'Model';

		// Load potentially needed language files
		KunenaFactory::loadLanguage();
		KunenaFactory::loadLanguage('com_kunena.model');
		KunenaFactory::loadLanguage('com_kunena.view');

		$view = new $view(['base_path' => KPATH_SITE]);

		if (!($params instanceof Registry))
		{
			$params = new Registry($params);
		}

		$params->set('layout', $layout);

		// Push the model into the view (as default).
		$model = new $model;
		$model->initialize($params);
		$view->setModel($model, true);

		// Add template path
		if ($params->get('templatepath'))
		{
			$view->addTemplatePath($params->get('templatepath'));
		}

		if ($viewName != 'common')
		{
			$view->common           = new HtmlView(['base_path' => KPATH_SITE]);
			$view->common->embedded = true;
		}

		// Flag view as being embedded
		$view->embedded = true;

		// Flag view as being teaser
		$view->teaser = $params->get('teaser', 0);

		// Render the view.
		$view->displayLayout($layout, $template);
	}
}
