<?php
/**
 * Kunena Package
 *
 * @package        Kunena.Package
 *
 * @copyright      Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
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
			'7.4' => '7.4.0',
			'7.3' => '7.3.0',
			'7.2' => '7.2.0',
			'7.1' => '7.1.0',
			'0'   => '7.2.18', // Preferred version
		),
		'MySQL'   => array(
			'8.0' => '8.0',
			'5.7' => '5.7',
			'5.6' => '5.6',
			'0'   => '5.7' // Preferred version
		),
		'Joomla!' => array(
			'4.0'  => '4.0.0-alpha8-dev',
			'0'    => '4.0.0-alpha8-dev', // Preferred version
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
	 * @param   string $uri uri
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

		if (version_compare(JVERSION, '4.0', '<'))
		{
			$app   = Factory::getApplication();
			$modal = <<<EOS
			<div id="kunena-modal" class="modal hide fade" style="width:auto;min-width:32%;margin-left:-13%;top:25%;padding:10px;"><div class="modal-body"></div></div><script>jQuery('#kunena-modal').remove().prependTo('body').modal({backdrop: 'static', keyboard: false, remote: '{$this->makeRoute('index.php?option=com_kunena&view=install&format=raw')}'})</script>
EOS;
			$app->enqueueMessage('Installing Kunena... ' . $modal);
		}
		else
		{
			$app = Factory::getApplication();
			$app->redirect(Route::_('index.php?option=com_kunena&view=install', false));
		}

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
	 * @param   string  $version  version
	 *
	 * @return boolean|integer
	 *
	 * @since   6.0.0
	 * @throws Exception
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
		$query = $db->getQuery(true)
			->select($db->quoteName('version'))
			->from($db->quoteName($table))
			->order($db->quoteName('id') . ' DESC');
		$db->setQuery($query,0, 1);
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
			->set($db->quoteName('name') . '=' . $db->quote('Kunena 6.0 Update Site'))
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

	/**
	 * @param   string  $parent  parent
	 *
	 * @return boolean
	 * @since Kunena
	 * @throws Exception
	 */
	public function uninstall($parent)
	{
		$adminpath = $parent->getParent()->getPath('extension_administrator');
		$model     = "{$adminpath}/install/model.php";

		if (file_exists($model))
		{
			require_once $model;
			$installer = new KunenaModelInstall;
			$installer->uninstall();
		}

		return true;
	}

	/**
	 * @param   string  $type    type
	 * @param   string  $parent  parent
	 *
	 * @return boolean
	 * @since Kunena
	 * @throws Exception
	 */
	public function preflight($type, $parent)
	{
		$parent   = $parent->getParent();
		$manifest = $parent->getManifest();

		// Prevent installation if requirements are not met.
		if (!$this->checkRequirements($manifest->version))
		{
			return false;
		}

		$adminPath = $parent->getPath('extension_administrator');
		$sitePath  = $parent->getPath('extension_site');

		if (is_file($adminPath . '/admin.kunena.php'))
		{
			// Kunena 2.0 or older release found, clean up the directories.
			static $ignoreAdmin = array('index.html', 'kunena.xml', 'archive');

			if (is_file($adminPath . '/install.script.php'))
			{
				// Kunena 1.7 or older release..
				$ignoreAdmin[] = 'install.script.php';
				$ignoreAdmin[] = 'admin.kunena.php';
			}

			static $ignoreSite = array('index.html', 'kunena.php', 'router.php', 'template', 'COPYRIGHT.php', 'CHANGELOG.php');
			$this->deleteFolder($adminPath, $ignoreAdmin);
			$this->deleteFolder($sitePath, $ignoreSite);
		}

		// Remove Blue Eagle template on K5.0
		$oldblue = $sitePath . '/template/blue_eagle';

		if (is_dir($oldblue))
		{
			$this->deleteKfolder($sitePath . '/template/blue_eagle');
		}

		// Delete languages files related to blue eagle in en-gb and others languages
		if (Folder::exists($sitePath . '/language'))
		{
			$kunena_language_folders = Folder::folders($sitePath . '/language');

			foreach ($kunena_language_folders as $folder)
			{
				if (File::exists($sitePath . '/language/' . $folder . '/' . $folder . '.com_kunena.tpl_blue_eagle.ini'))
				{
					File::delete($sitePath . '/language/' . $folder . '/' . $folder . '.com_kunena.tpl_blue_eagle.ini');
				}
			}
		}

		// Copy files to new dir for Crypsis
		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/less/custom.less'))
		{
			$file    = is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/less/custom.less');
			$filenew = is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/assets/less/custom.less');

			if (!empty($file) && !$filenew)
			{
				Folder::create($sitePath . '/template/crypsis/assets/less');
				$src  = $sitePath . '/template/crypsis/less/custom.less';
				$dest = $sitePath . '/template/crypsis/assets/less/custom.less';
				File::copy($src, $dest);
			}

			$this->deleteFile(JPATH_SITE . '/components/com_kunena/template/crypsis/less/custom.less');
		}

		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/css/custom.css'))
		{
			$file    = is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/css/custom.css');
			$filenew = is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/assets/css/custom.css');

			if (!empty($file) && !$filenew)
			{
				Folder::create($sitePath . '/template/crypsis/assets/css');
				$src  = $sitePath . '/template/crypsis/css/custom.css';
				$dest = $sitePath . '/template/crypsis/assets/css/custom.css';
				File::copy($src, $dest);
			}

			$this->deleteFile(JPATH_SITE . '/components/com_kunena/template/crypsis/css/custom.css');
		}

		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/params.ini'))
		{
			$file    = is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/params.ini');
			$filenew = is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/config/params.ini');

			if (!empty($file) && !$filenew)
			{
				Folder::create($sitePath . '/template/crypsis/config');
				$src  = $sitePath . '/template/crypsis/params.ini';
				$dest = $sitePath . '/template/crypsis/config/params.ini';
				File::copy($src, $dest);
			}

			$this->deleteFile(JPATH_SITE . '/components/com_kunena/template/crypsis/params.ini');
		}

		// Remove old Crypsis files
		if (is_file(JPATH_ROOT . '/components/com_kunena/template/crypsis/template.xml'))
		{
			$this->deleteKfolder($sitePath . '/template/crypsis/css');
			$this->deleteKfolder($sitePath . '/template/crypsis/images');
			$this->deleteKfolder($sitePath . '/template/crypsis/less');
			$this->deleteKfolder($sitePath . '/template/crypsis/media');
			$this->deleteFile($sitePath . '/template/crypsis/config.xml');
			$this->deleteFile($sitePath . '/template/crypsis/kunena_tmpl_crypsis.xml');
			$this->deleteFile($sitePath . '/template/crypsis/template.xml');
		}

		$language_folders = Folder::folders(JPATH_ROOT . '/language');

		foreach ($language_folders as $folder)
		{
			if (File::exists(JPATH_ROOT . '/language/' . $folder . '/' . $folder . '.com_kunena.tpl_blue_eagle.ini'))
			{
				File::delete(JPATH_ROOT . '/language/' . $folder . '/' . $folder . '.com_kunena.tpl_blue_eagle.ini');
			}
		}

		// Remove old system directory
		if (is_file(JPATH_ROOT . '/media/kunena/topic_icons/system/topicicons.xml'))
		{
			if (!is_file(JPATH_ROOT . '/media/kunena/archive/topic_icons/system/topicicons.xml'))
			{
				Folder::create(JPATH_ROOT . '/media/kunena/archive/topic_icons');
				$folder    = JPATH_ROOT . '/media/kunena/topic_icons/system';
				$foldernew = JPATH_ROOT . '/media/kunena/archive/topic_icons/system';
				Folder::copy($folder, $foldernew);
				Folder::delete($folder);
			}

			if (!is_file(JPATH_ROOT . '/media/kunena/topic_icons/systemold/topicicons.xml'))
			{
				Folder::create(JPATH_ROOT . '/media/kunena/topic_icons/systemold');
				$file    = JPATH_ROOT . '/media/kunena/topic_icons/default/topicicons.xml';
				$filenew = JPATH_ROOT . '/media/kunena/topic_icons/systemold/topicicons.xml';
				File::copy($file, $filenew);
			}

			$db    = Factory::getDBO();
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__kunena_categories'))
				->set($db->quoteName('iconset') . ' = ' . $db->quote('default'))
				->where($db->quoteName('iconset') . ' = ' . $db->quote('system'));
			$db->setQuery($query);
			$db->execute();
		}

		// K5.1 Remove files
		if (is_file(JPATH_ROOT . '/administrator/components/com_kunena/template/plugin/edit.php'))
		{
			$this->deleteKfolder(JPATH_ROOT . '/administrator/components/com_kunena/template/plugin');
			$this->deleteKfolder(JPATH_ROOT . '/administrator/components/com_kunena/views/plugin');
			$this->deleteFile(JPATH_ROOT . '/administrator/components/com_kunena/controllers/plugin.php');
		}

		if (is_file(JPATH_ROOT . '/media/kunena/js/debug.js'))
		{
			$this->deleteFile(JPATH_ROOT . '/media/kunena/js/debug.js');
		}

		if (is_file(JPATH_ROOT . '/libraries/kunena/compat/joomla/image/image.php'))
		{
			$this->deleteFile(JPATH_ROOT . '/libraries/kunena/compat/joomla/image/image.php');
			$this->deleteKfolder(JPATH_ROOT . '/components/com_kunena/template/crypsis/layouts/topic/edit/editor');
			$this->deleteFile(JPATH_ROOT . '/components/com_kunena/layout/topic/edit/editor.php');
		}

		// Copy files to new dir for Crypsis
		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/assets/js/markitup.editor-min.js'))
		{
			$this->deleteFile(JPATH_SITE . '/components/com_kunena/template/crypsis/assets/js/markitup.editor-min.js');
		}

		// Prepare installation.
		$model = "{$adminPath}/install/model.php";

		if (file_exists($model))
		{
			require_once $model;
			$installer = new KunenaModelInstall;
			$installer->install();
		}

		return true;
	}





	/**
	 * @param   string  $path    path
	 * @param   array   $ignore  ignore
	 *
	 * @return void
	 * @since Kunena
	 */
	public function deleteFolder($path, $ignore = array())
	{
		$this->deleteFiles($path, $ignore);
		$this->deleteFolders($path, $ignore);
	}

	/**
	 * @param   string  $path    path
	 * @param   array   $ignore  ignore
	 *
	 * @return void
	 * @since Kunena
	 */
	public function deleteFiles($path, $ignore = array())
	{
		$ignore = array_merge($ignore, array('.git', '.svn', 'CVS', '.DS_Store', '__MACOSX'));

		if (Folder::exists($path))
		{
			foreach (Folder::files($path, '.', false, true, $ignore) as $file)
			{
				if (File::exists($file))
				{
					File::delete($file);
				}
			}
		}
	}

	/**
	 * @param   string  $path    path
	 * @param   array   $ignore  ignore
	 *
	 * @return void
	 * @since Kunena
	 */
	public function deleteFolders($path, $ignore = array())
	{
		$ignore = array_merge($ignore, array('.git', '.svn', 'CVS', '.DS_Store', '__MACOSX'));

		if (Folder::exists($path))
		{
			foreach (Folder::folders($path, '.', false, true, $ignore) as $folder)
			{
				if (Folder::exists($folder))
				{
					Folder::delete($folder);
				}
			}
		}
	}

	/**
	 * @internal param array $ignore
	 *
	 * @param   string  $path  path
	 *
	 * @return void
	 * @since    Kunena
	 */
	public function deleteKfolder($path)
	{
		Folder::delete($path);
	}

	/**
	 * @param   string  $path  path
	 *
	 * @return void
	 * @since Kunena
	 */
	public function deleteFile($path)
	{
		if (File::exists($path))
		{
			File::delete($path);
		}
	}


	/**
	 * Converts the site's database tables to support UTF-8 Multibyte.
	 *
	 * @return  void
	 *
	 * @since   3.5
	 */
	public function convertTablesToUtf8mb4()
	{
		$db = Factory::getDbo();

		// This is only required for MySQL databases
		$serverType = $db->getServerType();

		if ($serverType != 'mysql')
		{
			return;
		}

		// Set required conversion status
		if (!$db->hasUTF8mb4Support())
		{
			return;
		}

		$query = $db->getQuery(true)
			->select($db->quoteName('default_character_set_name'))
			->from($db->quoteName('#__kunena_version'));
		$db->setQuery($query, 0, 1);

		// Nothing to do, saved conversion status from DB is equal to required
		if ($db->getCollation() == 'utf8mb4_unicode_ci')
		{
			return;
		}

		// Step 1: Drop indexes later to be added again with column lengths limitations at step 2
		$fileName1 = JPATH_ROOT . '/administrator/components/com_kunena/install/sql/migrate/mysql/utf8mb4-conversion.sql';

		if (is_file($fileName1))
		{
			$fileContents1 = @file_get_contents($fileName1);
			$queries1      = $db->splitSql($fileContents1);

			if (!empty($queries1))
			{
				foreach ($queries1 as $query1)
				{
					try
					{
						$db->setQuery($query1)->execute();
					}
					catch (Exception $e)
					{
						// If the query fails we will go on. It just means the index to be dropped does not exist.
					}
				}
			}
		}
	}
}
