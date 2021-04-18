<?php
/**
 * Kunena Package
 *
 * @package        Kunena.Package
 *
 * @copyright      Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Adapter\ComponentAdapter;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Install\KunenaInstallerException;
use Kunena\Forum\Libraries\KunenaInstaller;
use Kunena\Forum\Libraries\Path\KunenaPath;

/**
 * Kunena package installer script.
 *
 * @since Kunena
 */
class Pkg_KunenaInstallerScript extends InstallerScript
{
	/**
	 * The extension name. This should be set in the installer script.
	 *
	 * @var    string
	 * @since  5.4.0
	 */
	protected $extension = 'com_kunena';

	/**
	 * Minimum PHP version required to install the extension
	 *
	 * @var    string
	 * @since  5.4.0
	 */
	protected $minimumPhp = '7.2.5';

	/**
	 * Minimum Joomla! version required to install the extension
	 *
	 * @var    string
	 * @since  6.0.0
	 */
	protected $minimumJoomla = '4.0.0-dev';

	/**
	 * List of required PHP extensions.
	 *
	 * @var array
	 * @since Kunena
	 */
	protected $extensions = array('dom', 'gd', 'json', 'pcre', 'SimpleXML');

	/**
	 * @var  CMSApplication  Holds the application object
	 *
	 * @since ?
	 */
	private $app;

	/**
	 * @var  string  During an update, it will be populated with the old release version
	 *
	 * @since ?
	 */
	private $oldRelease;

	/**
	 *  Constructor
	 *
	 * @since Kunena 6.0
	 */
	public function __construct()
	{
		$this->app = Factory::getApplication();
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   string            $type    'install', 'update' or 'discover_install'
	 * @param   ComponentAdapter  $parent  Installerobject
	 *
	 * @return  boolean  false will terminate the installation
	 *
	 * @since Kunena 6.0
	 */
	public function preflight($type, $parent)
	{
		// Storing old release number for process in postflight
		if (strtolower($type) == 'update')
		{
			$manifest         = $this->getItemArray('manifest_cache', '#__extensions', 'element', $this->extension);
			$this->oldRelease = $manifest['version'];

			// Check if update is allowed (only update from 5.1.0 and higher)
			if (version_compare($this->oldRelease, '5.1.0', '<'))
			{
				$this->app->enqueueMessage(Text::sprintf('COM_kunena_UPDATE_UNSUPPORTED', $this->oldRelease, '5.1.0'), 'error');

				return false;
			}
		}

		return parent::preflight($type, $parent);
	}

	/**
	 * Method to install the component
	 *
	 * @param   ComponentAdapter  $parent  Installerobject
	 *
	 * @return void
	 *
	 * @since Kunena 6.0
	 */
	public function install($parent)
	{
		// Notice $parent->getParent() returns JInstaller object
		$parent->getParent()->setRedirectUrl('index.php?option=com_kunena');
	}

	/**
	 * Method to uninstall the component
	 *
	 * @param   ComponentAdapter  $parent  Installerobject
	 *
	 * @return void
	 *
	 * @since Kunena 6.0
	 */
	public function uninstall($parent)
	{
	}

	/**
	 * method to update the component
	 *
	 * @param   ComponentAdapter  $parent  Installerobject
	 *
	 * @return void
	 *
	 * @since Kunena 6.0
	 */
	public function update($parent)
	{
		if (version_compare($this->oldRelease, '6.0.0', '<'))
		{
			// Remove integrated player classes
			$this->deleteFiles[]   = '/administrator/components/com_kunena/models/fields/player.php';
			$this->deleteFolders[] = '/components/com_kunena/helpers/player';

			// Remove old SQL files
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/4.5.0.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/4.5.1.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/4.5.2.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/4.5.3.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/4.5.4.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/5.0.0.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/5.0.1.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/5.0.2.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/5.0.3.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/5.0.4.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/5.4.0.sql';
			$this->deleteFiles[] = '/administrator/components/com_kunena/sql/updates/mysql/5.5.0.sql';
		}
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @param   string            $type    'install', 'update' or 'discover_install'
	 * @param   ComponentAdapter  $parent  Installer object
	 *
	 * @return  boolean  false will terminate the installation
	 *
	 * @throws KunenaInstallerException
	 * @since Kunena 6.0
	 */
	public function postflight($type, $parent)
	{
		$this->fixUpdateSite();

		// Clear Joomla system cache.
		$cache = Factory::getCache();
		$cache->clean('_system');

		// Remove all compiled files from APC cache.
		if (function_exists('apc_clear_cache'))
		{
			@apc_clear_cache();
		}

		if (strtolower($type) == 'install' || strtolower($type) == 'discover_install')
		{
			$file = JPATH_MANIFESTS . '/packages/pkg_kunena.xml';

			$manifest    = simplexml_load_file($file);
			$version     = (string) $manifest->version;
			$build       = (string) $manifest->version;
			$date        = (string) $manifest->creationDate;
			$versionname = (string) $manifest->versionname;
			$installdate = Factory::getDate('now');

			$db    = Factory::getDbo();
			$query = $db->getQuery(true);

			$values = [
				$db->quote($version),
				$db->quote($build),
				$db->quote($date),
				$db->quote($versionname),
				$db->quote($installdate),
				$db->quote('')
			];

			$query->insert($db->quoteName('#__kunena_version'))
				->columns(
					[
						$db->quoteName('version'),
						$db->quoteName('build'),
						$db->quoteName('versiondate'),
						$db->quoteName('versionname'),
						$db->quoteName('installdate'),
						$db->quoteName('state')
					]
				)
				->values(implode(', ', $values));
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		$this->addDashboardMenu('kunena', 'kunena');

		// Delete the tmp install directory
		foreach (glob(KunenaPath::tmpdir() . '/install_*') as $dir)
		{
			if (is_dir($dir))
			{
				Folder::delete($dir);
			}
		}

		$version = '';
		$date    = '';
		$file    = JPATH_MANIFESTS . '/packages/pkg_kunena.xml';

		if (file_exists($file))
		{
			$manifest = simplexml_load_file($file);
			$version  = (string) $manifest->version;
			$date     = (string) $manifest->creationDate;
		}
		else
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('version')->from('#__kunena_version')->order('id');
			$query->setLimit(1);
			$db->setQuery($query);

			$version = $db->loadResult();
			$date    = (string) $version->versiondate;
		}

		$tmpfile = KunenaPath::tmpdir() . '/pkg_kunena_v' . $version . '_' . $date . '.zip';

		if (is_file($tmpfile))
		{
			File::delete(KunenaPath::tmpdir() . '/pkg_kunena_v' . $version . '_' . $date . '.zip');
		}

		return true;
	}

	/**
	 * @param   string  $parent  parent
	 *
	 * @return void
	 *
	 * @since Kunena
	 */
	public function discover_install($parent)
	{
		return self::install($parent);
	}

	/**
	 * @param   string  $uri  uri
	 *
	 * @return string
	 *
	 * @since version
	 */
	public function makeRoute($uri)
	{
		return Route::_($uri, false);
	}

	/**
	 * @param   string  $group    group
	 * @param   string  $element  element
	 *
	 * @return boolean
	 *
	 * @since version
	 */
	public function enablePlugin($group, $element)
	{
		$plugin = Table::getInstance('extension');

		if (!$plugin->load(array('type' => 'plugin', 'folder' => $group, 'element' => $element)))
		{
			return false;
		}

		$plugin->enabled = 1;

		return $plugin->store();
	}

	/**
	 * @param   string  $version  version
	 *
	 * @return boolean|integer
	 *
	 * @since version
	 */
	public function checkRequirements($version)
	{
		$db   = Factory::getDbo();
		$pass = $this->checkVersion('PHP', $this->getCleanPhpVersion());
		$pass &= $this->checkVersion('Joomla!', JVERSION);
		$pass &= $this->checkVersion('MySQL', $db->getVersion());
		$pass &= $this->checkDbo($db->name, array('mysql', 'mysqli', 'pdomysql'));
		$pass &= $this->checkExtensions($this->extensions);
		$pass &= $this->checkKunena($version);

		return $pass;
	}

	// Internal functions

	/**
	 *  On some hosting the PHP version given with the version of the packet in the distribution
	 *
	 * @return string
	 *
	 * @since Kunena
	 */
	protected function getCleanPhpVersion()
	{
		$version = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION;

		return $version;
	}

	/**
	 * @param   string  $name     name
	 * @param   string  $version  version
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since version
	 */
	protected function checkVersion($name, $version)
	{
		$app = Factory::getApplication();

		$major = $minor = 0;

		foreach ($this->versions[$name] as $major => $minor)
		{
			if (!$major || version_compare($version, $major, '<'))
			{
				continue;
			}

			if (version_compare($version, $minor, '>='))
			{
				return true;
			}

			break;
		}

		if (!$major)
		{
			$minor = reset($this->versions[$name]);
		}

		$recommended = end($this->versions[$name]);
		$app->enqueueMessage(sprintf("%s %s is not supported. Minimum required version is %s %s, but it is highly recommended to use %s %s or later.",
			$name, $version, $name, $minor, $name, $recommended
		), 'notice'
		);

		return false;
	}

	/**
	 * @param   string  $name   name
	 * @param   array   $types  types
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since version
	 */
	protected function checkDbo($name, $types)
	{
		$app = Factory::getApplication();

		if (in_array($name, $types))
		{
			return true;
		}

		$app->enqueueMessage(sprintf("Database driver '%s' is not supported. Please use MySQL instead.", $name), 'notice');

		return false;
	}

	/**
	 * @param   array  $extensions  extensions
	 *
	 * @return integer
	 *
	 * @throws Exception
	 * @since version
	 */
	protected function checkExtensions($extensions)
	{
		$app = Factory::getApplication();

		$pass = 1;

		foreach ($extensions as $name)
		{
			if (!extension_loaded($name))
			{
				$pass = 0;
				$app->enqueueMessage(sprintf("Required PHP extension '%s' is missing. Please install it into your system.", $name), 'notice');
			}
		}

		return $pass;
	}

	/**
	 * @param   string  $version  version
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since version
	 */
	protected function checkKunena($version)
	{
		$app = Factory::getApplication();
		$db  = Factory::getDbo();

		// Always load Kunena API if it exists.
		$api = JPATH_ADMINISTRATOR . '/components/com_kunena/api/api.php';

		if (is_file($api))
		{
			require_once $api;
		}

		// Do not install over Git repository (K1.6+).
		if (class_exists('KunenaForum') && method_exists('KunenaForum', 'isDev') && KunenaForum::isDev())
		{
			$app->enqueueMessage('Oops! You should not install Kunena over your Git repository!', 'notice');

			return false;
		}

		// Check if Kunena can be found from the database.
		$table = $db->getPrefix() . 'kunena_version';
		$db->setQuery("SHOW TABLES LIKE {$db->quote($table)}");

		if ($db->loadResult() != $table)
		{
			return true;
		}

		// Get installed Kunena version.
		$db->setQuery("SELECT version FROM {$table} ORDER BY `id` DESC", 0, 1);
		$installed = $db->loadResult();

		if (!$installed)
		{
			return true;
		}

		// Always allow upgrade to the newer version.
		if (version_compare($version, $installed, '>='))
		{
			return true;
		}

		// Check if we can downgrade to the current version.
		if (class_exists('KunenaInstaller'))
		{
			if (KunenaInstaller::canDowngrade($version))
			{
				return true;
			}
		}
		else
		{
			// Workaround when Kunena files were removed to allow downgrade between bugfix versions.
			$major = preg_replace('/(\d+.\d+)\..*$/', '\\1', $version);

			if (version_compare($installed, $major, '>'))
			{
				return true;
			}
		}

		$app->enqueueMessage(sprintf('Sorry, it is not possible to downgrade Kunena %s to version %s.', $installed, $version),
			'notice'
		);

		return false;
	}

	/**
	 *
	 *
	 * @return void
	 * @throws Exception
	 * @since version
	 */
	protected function fixUpdateSite()
	{
		$db = Factory::getDbo();

		// Find all update sites.
		$query = $db->getQuery(true)
			->select($db->quoteName('update_site_id'))->from($db->quoteName('#__update_sites'))
			->where($db->quoteName('location') . ' LIKE ' . $db->quote('https://update.kunena.org/%'))
			->order($db->quoteName('update_site_id') . ' ASC');
		$db->setQuery($query);
		$list = (array) $db->loadColumn();

		$query = $db->getQuery(true)
			->set($db->quoteName('name') . '=' . $db->quote('Kunena 5.1 Update Site'))
			->set($db->quoteName('type') . '=' . $db->quote('collection'))
			->set($db->quoteName('location') . '=' . $db->quote('https://update.kunena.org/6.0/list.xml'))
			->set($db->quoteName('enabled') . '=1')
			->set($db->quoteName('last_check_timestamp') . '=0');

		if (!$list)
		{
			// Create new update site.
			$query->insert($db->quoteName('#__update_sites'));
			$id = $db->insertid();
		}
		else
		{
			// Update last Kunena update site with new information.
			$id = array_pop($list);
			$query->update($db->quoteName('#__update_sites'))->where($db->quoteName('update_site_id') . '=' . $id);
		}

		$db->setQuery($query);
		$db->execute();

		if ($list)
		{
			$ids = implode(',', $list);

			// Remove old update sites.
			$query = $db->getQuery(true)->delete($db->quoteName('#__update_sites'))->where($db->quoteName('update_site_id') . 'IN (' . $ids . ')');
			$db->setQuery($query);
			$db->execute();
		}

		// Currently only pkg_kunena gets registered to update site, so remove everything else.
		$list[] = $id;
		$ids    = implode(',', $list);

		// Remove old updates.
		$query = $db->getQuery(true)->delete($db->quoteName('#__updates'))->where($db->quoteName('update_site_id') . 'IN (' . $ids . ')');
		$db->setQuery($query);
		$db->execute();

		// Remove old update extension bindings.
		$query = $db->getQuery(true)->delete($db->quoteName('#__update_sites_extensions'))->where($db->quoteName('update_site_id') . 'IN (' . $ids . ')');
		$db->setQuery($query);
		$db->execute();
	}
}
