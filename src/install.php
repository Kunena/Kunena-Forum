<?php
/**
 * Kunena Package
 *
 * @package        Kunena.Package
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

/**
 * Kunena package installer script.
 * @since Kunena
 */
class Pkg_KunenaInstallerScript
{
	/**
	 * List of supported versions. Newest version first!
	 *
	 * @var array
	 * @since Kunena
	 */
	protected $versions = array(
		'PHP'     => array(
			'8.0' => '8.0.0',
			'7.4' => '7.4.0',
			'7.3' => '7.3.0',
			'7.2' => '7.2.0',
			'7.1' => '7.1.9',
			'7.0' => '7.0.4',
			'0'   => '7.1.9', // Preferred version
		),
		'MySQL'   => array(
			'5.7' => '5.7.8',
			'5.6' => '5.6.5',
			'5.5' => '5.5.3',
			'0'   => '5.5.3', // Preferred version
		),
		'Joomla!' => array(
			'3.10' => '3.10.4',
			'0'    => '3.10.4', // Preferred version
		),
	);

	/**
	 * List of required PHP extensions.
	 *
	 * @var array
	 * @since Kunena
	 */
	protected $extensions = array('dom', 'gd', 'json', 'pcre', 'SimpleXML');

	/**
	 * @param   string $parent parent
	 *
	 * @return boolean
	 *
	 * @since version
	 */
	public function install($parent)
	{
		return true;
	}

	/**
	 * @param   string $parent parent
	 *
	 * @return boolean
	 *
	 * @since Kunena
	 */
	public function discover_install($parent)
	{
		return self::install($parent);
	}

	/**
	 * @param   string $parent parent
	 *
	 * @return boolean
	 *
	 * @since version
	 */
	public function update($parent)
	{
		return self::install($parent);
	}

	/**
	 * @param   string $parent parent
	 *
	 * @return boolean
	 *
	 * @since version
	 */
	public function uninstall($parent)
	{
		return true;
	}

	/**
	 * @param   string $type   type
	 * @param   string $parent parent
	 *
	 * @return boolean
	 *
	 * @since version
	 */
	public function preflight($type, $parent)
	{
		/** @var JInstallerComponent $parent */
		$manifest = $parent->getParent()->getManifest();
		$app = Factory::getApplication();

		// Prevent to be installed on Joomla! 4.x and later
		if (version_compare(JVERSION, '4.0', '>='))
		{
			$app->enqueueMessage(sprintf('Joomla! %s is not supported. Kunena 5.2 only works on Joomla! 3.10.x',
				JVERSION
				), 'notice'
			);

			return false;
		}

		// Prevent installation if requirements are not met.
		if (!$this->checkRequirements($manifest->version))
		{
			return false;
		}

		// Remove old log file before installation.
		$logFile = Factory::getConfig()->get('log_path') . '/kunena.php';

		if (is_file($logFile))
		{
			@unlink($logFile);
		}

		return true;
	}

	/**
	 * @param   string  $type   type
	 * @param   string  $parent parent
	 *
	 * @return boolean
	 *
	 * @since version
	 * @throws Exception
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

		if ($type == 'uninstall')
		{
			return true;
		}

		$this->enablePlugin('system', 'kunena');
		$this->enablePlugin('quickicon', 'kunena');

		$app = Factory::getApplication();
		$app->redirect(Route::_('index.php?option=com_kunena&view=install', false));

		return true;
	}

	/**
	 * @param   string $group   group
	 * @param   string $element element
	 *
	 * @return boolean
	 *
	 * @since version
	 */
	public function enablePlugin($group, $element)
	{
		$plugin = Joomla\CMS\Table\Table::getInstance('extension');

		if (!$plugin->load(array('type' => 'plugin', 'folder' => $group, 'element' => $element)))
		{
			return false;
		}

		$plugin->enabled = 1;

		return $plugin->store();
	}

	/**
	 * @param   string $version version
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
	 * @param   string $name    name
	 * @param   string $version version
	 *
	 * @return boolean
	 *
	 * @since version
	 * @throws Exception
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
	 * @param   string $name  name
	 * @param   array  $types types
	 *
	 * @return boolean
	 *
	 * @since version
	 * @throws Exception
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
	 * @param   array $extensions extensions
	 *
	 * @return integer
	 *
	 * @since version
	 * @throws Exception
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
	 * @param   string $version version
	 *
	 * @return boolean
	 *
	 * @since version
	 * @throws Exception
	 */
	protected function checkKunena($version)
	{
		$app = Factory::getApplication();
		$db  = Factory::getDbo();

		// Always load Kunena API if it exists.
		$api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

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
	 * @since version
	 * @throws Exception
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
			->set($db->quoteName('name') . '=' . $db->quote('Kunena 5.2 Update Site'))
			->set($db->quoteName('type') . '=' . $db->quote('collection'))
			->set($db->quoteName('location') . '=' . $db->quote('https://update.kunena.org/5.2/list.xml'))
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
