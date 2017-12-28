<?php
/**
 * Kunena Component
 *
 * @package    Kunena.Installer
 *
 * @copyright  (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.kunena.org
 **/
defined('_JEXEC') or die();

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class Com_KunenaInstallerScript
{
	protected $versions = array(
		'PHP' => array (
			'7.1' => '7.1.0',
			'7.0' => '7.0.4',
			'5.6' => '5.6.8',
			'5.5' => '5.5.9',
			'5.4' => '5.4.13',
			'5.3' => '5.3.10',
			'0'   => '7.0.11' // Preferred version
		),
		'MySQL' => array (
			'5.1' => '5.1',
			'0' => '5.5' // Preferred version
		),
		'Joomla!' => array (
			'3.5' => '3.5.1',
			'0' => '3.6.2' // Preferred version
		)
	);

	protected $extensions = array('dom', 'gd', 'json', 'pcre', 'SimpleXML');

	/**
	 * @param $parent
	 *
	 * @return boolean
	 *
	 */
	public function install($parent)
	{
		// Delete all cached files.
		$cacheDir = JPATH_CACHE . '/kunena';

		if (is_dir($cacheDir))
		{
			JFolder::delete($cacheDir);
		}

		JFolder::create($cacheDir);

		return true;
	}

	/**
	 * @param $parent
	 *
	 * @return boolean
	 */
	public function discover_install($parent)
	{
		return self::install($parent);
	}

	/**
	 * @param $parent
	 *
	 * @return boolean
	 */
	public function update($parent)
	{
		return self::install($parent);
	}

	/**
	 * @param $parent
	 *
	 * @return boolean
	 */
	public function uninstall($parent)
	{
		$adminpath = $parent->getParent()->getPath('extension_administrator');
		$model     = "{$adminpath}/install/model.php";

		if (file_exists($model))
		{
			require_once($model);
			$installer = new KunenaModelInstall;
			$installer->uninstall();
		}

		return true;
	}

	/**
	 * @param $type
	 * @param $parent
	 *
	 * @return boolean
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
		if (JFolder::exists($sitePath . '/language'))
		{
			$kunena_language_folders = JFolder::folders($sitePath . '/language');

			foreach ($kunena_language_folders as $folder)
			{
				if (JFile::exists($sitePath . '/language/' . $folder . '/' . $folder . '.com_kunena.tpl_blue_eagle.ini'))
				{
					JFile::delete($sitePath . '/language/' . $folder . '/' . $folder . '.com_kunena.tpl_blue_eagle.ini');
				}
			}
		}

		// Copy files to new dir for Crypsis
		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/less/custom.less'))
		{
			$file = is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/less/custom.less');
			$filenew = is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/assets/less/custom.less');

			if (!empty($file) && !$filenew)
			{
				JFolder::create($sitePath . '/template/crypsis/assets/less');
				$src = $sitePath . '/template/crypsis/less/custom.less';
				$dest = $sitePath . '/template/crypsis/assets/less/custom.less';
				KunenaFile::copy($src, $dest);
			}

			$this->deleteFile(JPATH_SITE . '/components/com_kunena/template/crypsis/less/custom.less');
		}

		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/css/custom.css'))
		{
			$file = is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/css/custom.css');
			$filenew = is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/assets/css/custom.css');

			if (!empty($file) && !$filenew)
			{
				JFolder::create($sitePath . '/template/crypsis/assets/css');
				$src = $sitePath . '/template/crypsis/css/custom.css';
				$dest = $sitePath . '/template/crypsis/assets/css/custom.css';
				KunenaFile::copy($src, $dest);
			}

			$this->deleteFile(JPATH_SITE . '/components/com_kunena/template/crypsis/css/custom.css');
		}

		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/params.ini'))
		{
			$file = is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/params.ini');
			$filenew = is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/config/params.ini');

			if (!empty($file) && !$filenew)
			{
				JFolder::create($sitePath . '/template/crypsis/config');
				$src = $sitePath . '/template/crypsis/params.ini';
				$dest = $sitePath . '/template/crypsis/config/params.ini';
				KunenaFile::copy($src, $dest);
			}

			$this->deleteFile(JPATH_SITE . '/components/com_kunena/template/crypsis/params.ini');
		}

		// Copy files to new dir for Crypsisb3
		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/less/custom.less'))
		{
			$file = is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/less/custom.less');
			$filenew = is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/assets/less/custom.less');

			if (!empty($file) && !$filenew)
			{
				JFolder::create($sitePath . '/template/crypsisb3/assets/less');
				$src = $sitePath . '/template/crypsisb3/less/custom.less';
				$dest = $sitePath . '/template/crypsisb3/assets/less/custom.less';
				KunenaFile::copy($src, $dest);
			}

			$this->deleteFile(JPATH_SITE . '/components/com_kunena/template/crypsisb3/less/custom.less');
		}

		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/css/custom.css'))
		{
			$file = is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/css/custom.css');
			$filenew = is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/assets/css/custom.css');

			if (!empty($file) && !$filenew)
			{
				JFolder::create($sitePath . '/template/crypsisb3/assets/css');
				$src = $sitePath . '/template/crypsisb3/css/custom.css';
				$dest = $sitePath . '/template/crypsisb3/assets/css/custom.css';
				KunenaFile::copy($src, $dest);
			}

			$this->deleteFile(JPATH_SITE . '/components/com_kunena/template/crypsisb3/css/custom.css');
		}

		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/params.ini'))
		{
			$file = is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/params.ini');
			$filenew = is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/config/params.ini');

			if (!empty($file) && !$filenew)
			{
				JFolder::create($sitePath . '/template/crypsisb3/config');
				$src = $sitePath . '/template/crypsisb3/params.ini';
				$dest = $sitePath . '/template/crypsisb3/config/params.ini';
				KunenaFile::copy($src, $dest);
			}

			$this->deleteFile(JPATH_SITE . '/components/com_kunena/template/crypsisb3/params.ini');
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

		// Remove old Crypsisb3 files
		if (is_file(JPATH_ROOT . '/components/com_kunena/template/crypsisb3/template.xml'))
		{
			$this->deleteKfolder($sitePath . '/template/crypsisb3/css');
			$this->deleteKfolder($sitePath . '/template/crypsisb3/images');
			$this->deleteKfolder($sitePath . '/template/crypsisb3/less');
			$this->deleteKfolder($sitePath . '/template/crypsisb3/media');
			$this->deleteFile($sitePath . '/template/crypsisb3/config.xml');
			$this->deleteFile($sitePath . '/template/crypsisb3/kunena_tmpl_crypsis.xml');
			$this->deleteFile($sitePath . '/template/crypsisb3/template.xml');
		}

		$language_folders = JFolder::folders(JPATH_ROOT . '/language');

		foreach ($language_folders as $folder)
		{
			if ( JFile::exists(JPATH_ROOT . '/language/' . $folder . '/' . $folder . '.com_kunena.tpl_blue_eagle.ini'))
			{
				JFile::delete(JPATH_ROOT . '/language/' . $folder . '/' . $folder . '.com_kunena.tpl_blue_eagle.ini');
			}
		}

		// Remove old system directory
		if (is_file(JPATH_ROOT . '/media/kunena/topic_icons/system/topicicons.xml'))
		{
			if (!is_file(JPATH_ROOT . '/media/kunena/archive/topic_icons/system/topicicons.xml'))
			{
				JFolder::create(JPATH_ROOT . '/media/kunena/archive/topic_icons');
				$folder    = JPATH_ROOT . '/media/kunena/topic_icons/system';
				$foldernew = JPATH_ROOT . '/media/kunena/archive/topic_icons/system';
				JFolder::copy($folder, $foldernew);
				JFolder::delete($folder);
			}

			if (!is_file(JPATH_ROOT . '/media/kunena/topic_icons/systemold/topicicons.xml'))
			{
				JFolder::create(JPATH_ROOT . '/media/kunena/topic_icons/systemold');
				$file    = JPATH_ROOT . '/media/kunena/topic_icons/default/topicicons.xml';
				$filenew = JPATH_ROOT . '/media/kunena/topic_icons/systemold/topicicons.xml';
				JFile::copy($file, $filenew);
			}

			$db    = JFactory::getDBO();
			$query = "UPDATE `#__kunena_categories` SET iconset='default' WHERE iconset='system'";
			$db->setQuery($query);
			$db->execute();
		}

		// Prepare installation.
		$model = "{$adminPath}/install/model.php";

		if (file_exists($model))
		{
			require_once($model);
			$installer = new KunenaModelInstall;
			$installer->install();
		}

		return true;
	}

	/**
	 * @param $type
	 * @param $parent
	 *
	 * @return boolean
	 */
	public function postflight($type, $parent)
	{
		// Add custom.less
		$customcrypsis = JPATH_ROOT . '/components/com_kunena/template/crypsis/assets/less/custom.less';
		if (!file_exists($customcrypsis))
		{
			$this->createFile($customcrypsis);
		}

		// Add custom.less
		$customcrypsisb3 = JPATH_ROOT . '/components/com_kunena/template/crypsisb3/assets/less/custom.less';
		if (!file_exists($customcrypsisb3))
		{
			$this->createFile($customcrypsisb3);
		}

		return true;
	}

	/**
	 * @param $version
	 *
	 * @return boolean|integer
	 */
	public function checkRequirements($version)
	{
		$db   = JFactory::getDbo();
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
	 * On some hosting the PHP version given with the version of the packet in the distribution
	 *
	 * @return string
	 * @internal param string $version The PHP version to clean
	 *
	 */
	protected function getCleanPhpVersion()
	{
		$version = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION;

		return $version;
	}

	/**
	 * @param $name
	 * @param $version
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 */
	protected function checkVersion($name, $version)
	{
		$app = JFactory::getApplication();

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
		$app->enqueueMessage(sprintf("%s %s is not supported. Minimum required version is %s %s, but it is higly recommended to use %s %s or later.", $name, $version, $name, $minor, $name, $recommended), 'notice');

		return false;
	}

	/**
	 * @param $name
	 * @param $types
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 */
	protected function checkDbo($name, $types)
	{
		$app = JFactory::getApplication();

		if (in_array($name, $types))
		{
			return true;
		}

		$app->enqueueMessage(sprintf("Database driver '%s' is not supported. Please use MySQL instead.", $name), 'notice');

		return false;
	}

	/**
	 * @param $extensions
	 *
	 * @return integer
	 *
	 * @throws Exception
	 */
	protected function checkExtensions($extensions)
	{
		$app = JFactory::getApplication();

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
	 * @param $version
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 */
	protected function checkKunena($version)
	{
		$app = JFactory::getApplication();

		// Always load Kunena API if it exists.
		$api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

		if (file_exists($api))
		{
			require_once $api;
		}

		// Do not install over Git repository (K1.6+).
		if ((class_exists('Kunena') && method_exists('Kunena', 'isSvn') && Kunena::isSvn())
			|| (class_exists('KunenaForum') && method_exists('KunenaForum', 'isDev') && KunenaForum::isDev()))
		{
			$app->enqueueMessage('Oops! You should not install Kunena over your Git reporitory!', 'notice');

			return false;
		}

		$db = JFactory::getDBO();

		// Check if Kunena can be found from the database
		$table = $db->getPrefix() . 'kunena_version';
		$db->setQuery("SHOW TABLES LIKE {$db->quote($table)}");

		if ($db->loadResult() != $table)
		{
			return true;
		}

		// Get installed Kunena version
		$db->setQuery("SELECT version FROM {$db->quoteName($table)} ORDER BY `id` DESC", 0, 1);
		$installed = $db->loadResult();

		if (!$installed)
		{
			return true;
		}

		// Always allow upgrade to the newer version
		if (version_compare($version, $installed, '>='))
		{
			return true;
		}

		// Check if we can downgrade to the current version
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

		$app->enqueueMessage(sprintf('Sorry, it is not possible to downgrade Kunena %s to version %s.', $installed, $version), 'notice');

		return false;
	}

	/**
	 * @param       $path
	 *
	 * @internal param array $ignore
	 */
	public function deleteFile($path)
	{
		if (JFile::exists($path))
		{
			JFile::delete($path);
		}
	}

	/**
	 * @param       $path
	 * @param   array $ignore
	 */
	public function deleteFiles($path, $ignore = array())
	{
		$ignore = array_merge($ignore, array('.git', '.svn', 'CVS', '.DS_Store', '__MACOSX'));

		if (JFolder::exists($path))
		{
			foreach (JFolder::files($path, '.', false, true, $ignore) as $file)
			{
				if (JFile::exists($file))
				{
					JFile::delete($file);
				}
			}
		}
	}

	/**
	 * @param       $path
	 * @param   array $ignore
	 */
	public function deleteFolders($path, $ignore = array())
	{
		$ignore = array_merge($ignore, array('.git', '.svn', 'CVS', '.DS_Store', '__MACOSX'));

		if (JFolder::exists($path))
		{
			foreach (JFolder::folders($path, '.', false, true, $ignore) as $folder)
			{
				if (JFolder::exists($folder))
				{
					JFolder::delete($folder);
				}
			}
		}
	}

	/**
	 * @param       $path
	 * @param   array $ignore
	 */
	public function deleteFolder($path, $ignore = array())
	{
		$this->deleteFiles($path, $ignore);
		$this->deleteFolders($path, $ignore);
	}

	/**
	 * @param       $path
	 *
	 * @internal param array $ignore
	 */
	public function deleteKfolder($path)
	{
		JFolder::delete($path);
	}

	/**
	 * @param $path
	 */
	public function createFile($path)
	{
		$ourFileHandle = fopen($path, 'w');
		$txt = "\n";
		fwrite($ourFileHandle, $txt);
		fclose($ourFileHandle);
	}
}
