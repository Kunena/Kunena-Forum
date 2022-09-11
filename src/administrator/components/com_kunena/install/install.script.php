<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;

/**
 * Class Com_KunenaInstallerScript
 * @since Kunena
 */
class Com_KunenaInstallerScript
{
	/**
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
	 * @var array
	 * @since Kunena
	 */
	protected $extensions = array('dom', 'gd', 'json', 'pcre', 'SimpleXML', 'mbstring');

	/**
	 * @param   string $parent parent
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function install($parent)
	{
		// Delete all cached files.
		$cacheDir = JPATH_CACHE . '/kunena';

		if (is_dir($cacheDir))
		{
			Folder::delete($cacheDir);
		}

		Folder::create($cacheDir);

		return true;
	}

	/**
	 * @param   string $parent parent
	 *
	 * @return boolean
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
	 * @since Kunena
	 */
	public function update($parent)
	{
		return self::install($parent);
	}

	/**
	 * @param   string $parent parent
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
	 * @param   string $type   type
	 * @param   string $parent parent
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

		// Delete the directories of plugins of CKeditor not present anymore
		$this->deleteFolder(JPATH_SITE . '/media/kunena/core/js/plugins/copyformatting');
		$this->deleteFolder(JPATH_SITE . '/media/kunena/core/js/plugins/div');
		$this->deleteFolder(JPATH_SITE . '/media/kunena/core/js/plugins/flashs');
		$this->deleteFolder(JPATH_SITE . '/media/kunena/core/js/plugins/forms');
		$this->deleteFolder(JPATH_SITE . '/media/kunena/core/js/plugins/pagebreak');
		$this->deleteFolder(JPATH_SITE . '/media/kunena/core/js/plugins/pastetools');
		$this->deleteFolder(JPATH_SITE . '/media/kunena/core/js/plugins/preview');
		$this->deleteFolder(JPATH_SITE . '/media/kunena/core/js/plugins/showblocks');
		$this->deleteFolder(JPATH_SITE . '/media/kunena/core/js/plugins/table');
		$this->deleteFolder(JPATH_SITE . '/media/kunena/core/js/plugins/tableselection');
		$this->deleteFolder(JPATH_SITE . '/media/kunena/core/js/plugins/tabletools');
		$this->deleteFolder(JPATH_SITE . '/media/kunena/core/js/plugins/templates');
		$this->deleteFolder(JPATH_SITE . '/media/kunena/core/js/plugins/wsc');

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

		// Copy files to new dir for Crypsisb3
		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/less/custom.less'))
		{
			$file    = is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/less/custom.less');
			$filenew = is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/assets/less/custom.less');

			if (!empty($file) && !$filenew)
			{
				Folder::create($sitePath . '/template/crypsisb3/assets/less');
				$src  = $sitePath . '/template/crypsisb3/less/custom.less';
				$dest = $sitePath . '/template/crypsisb3/assets/less/custom.less';
				File::copy($src, $dest);
			}

			$this->deleteFile(JPATH_SITE . '/components/com_kunena/template/crypsisb3/less/custom.less');
		}

		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/css/custom.css'))
		{
			$file    = is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/css/custom.css');
			$filenew = is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/assets/css/custom.css');

			if (!empty($file) && !$filenew)
			{
				Folder::create($sitePath . '/template/crypsisb3/assets/css');
				$src  = $sitePath . '/template/crypsisb3/css/custom.css';
				$dest = $sitePath . '/template/crypsisb3/assets/css/custom.css';
				File::copy($src, $dest);
			}

			$this->deleteFile(JPATH_SITE . '/components/com_kunena/template/crypsisb3/css/custom.css');
		}

		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/params.ini'))
		{
			$file    = is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/params.ini');
			$filenew = is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/config/params.ini');

			if (!empty($file) && !$filenew)
			{
				Folder::create($sitePath . '/template/crypsisb3/config');
				$src  = $sitePath . '/template/crypsisb3/params.ini';
				$dest = $sitePath . '/template/crypsisb3/config/params.ini';
				File::copy($src, $dest);
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

		// Delete completly the directory assets/js of Crypsis to don't keep old JS files
		if (is_file(JPATH_ROOT . '/components/com_kunena/template/crypsis/assets/js/jssocials.js'))
		{
			$this->deleteKfolder($sitePath . '/template/crypsis/assets/js');
		}

		// Delete completly the directory assets/js of Crypsisb3 to don't keep old JS files
		if (is_file(JPATH_ROOT . '/components/com_kunena/template/crypsisb3/assets/js/jssocials.js'))
		{
			$this->deleteKfolder($sitePath . '/template/crypsisb3/assets/js');
		}

		// Delete completly the directory assets/js of Crypsisb4 to don't keep old JS files
		if (is_file(JPATH_ROOT . '/components/com_kunena/template/crypsisb4/assets/js/jssocials.js'))
		{
			$this->deleteKfolder($sitePath . '/template/crypsisb4/assets/js');
		}

		// Delete some CSS files under old locations in /assets/css
		$this->deleteFiles($sitePath . '/template/crypsis/assets/css', array('custom.css', 'jssocials.css', 'jssocials-theme-classic.css', 'jssocials-theme-flat.css', 'jssocials-theme-minima.css', 'jssocials-theme-plain.css', 'wbbtheme.css', 'fancybox.black-min.css', 'fancybox.black.css', 'fancybox.white-min.css', 'fancybox.white.css'));
		$this->deleteFiles($sitePath . '/template/crypsisb3/assets/css', array('custom.css', 'jssocials.css', 'jssocials-theme-classic.css', 'jssocials-theme-flat.css', 'jssocials-theme-minima.css', 'jssocials-theme-plain.css', 'wbbtheme.css', 'fancybox.black-min.css', 'fancybox.black.css', 'fancybox.white-min.css', 'fancybox.white.css'));
		$this->deleteFiles($sitePath . '/template/crypsisb4/assets/css', array('custom.css', 'jssocials.css', 'jssocials-theme-classic.css', 'jssocials-theme-flat.css', 'jssocials-theme-minima.css', 'jssocials-theme-plain.css', 'wbbtheme.css'));

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
			$query = "UPDATE `#__kunena_categories` SET iconset='default' WHERE iconset='system'";
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
			$this->deleteKfolder(JPATH_ROOT . '/components/com_kunena/template/crypsisb3/layouts/topic/edit/editor');
			$this->deleteFile(JPATH_ROOT . '/components/com_kunena/layout/topic/edit/editor.php');
		}

		// Copy files to new dir for Crypsis
		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsis/assets/js/markitup.editor-min.js'))
		{
			$this->deleteFile(JPATH_SITE . '/components/com_kunena/template/crypsis/assets/js/markitup.editor-min.js');
		}

		if (is_file(JPATH_SITE . '/components/com_kunena/template/crypsisb3/assets/js/markitup.editor-min.js'))
		{
			$this->deleteFile(JPATH_SITE . '/components/com_kunena/template/crypsisb3/assets/js/markitup.editor-min.js');
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
	 * @param   string $type   type
	 * @param   string $parent parent
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function postflight($type, $parent)
	{
		if (version_compare(JVERSION, '4.0', '<'))
		{
			$this->convertTablesToUtf8mb4();
		}

		return true;
	}

	/**
	 * @param   string $version version
	 *
	 * @return boolean|integer
	 * @since Kunena
	 * @throws Exception
	 */
	public function checkRequirements($version)
	{
		$db   = Factory::getDbo();
		$pass = $this->checkVersion('PHP', $this->getCleanPhpVersion());
		$pass &= $this->checkVersion('Joomla!', JVERSION);
		$pass &= $this->checkVersion('MySQL', $db->getVersion());
		$pass &= $this->checkDbo($db->name, array('mysql', 'mysqli', 'pdomysql'));
		$pass &= $this->checkPhpExtensions($this->extensions);
		$pass &= $this->checkKunena($version);

		return $pass;
	}

	// Internal functions

	/**
	 * On some hosting the PHP version given with the version of the packet in the distribution
	 * @internal param string $version The PHP version to clean
	 * @return string
	 * @since    Kunena
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
	 * @since Kunena
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
		$app->enqueueMessage(sprintf("%s %s is not supported. Minimum required version is %s %s, but it is highly recommended to use %s %s or later.", $name, $version, $name, $minor, $name, $recommended), 'notice');

		return false;
	}

	/**
	 * @param   string $name  name
	 * @param   array  $types types
	 *
	 * @return boolean
	 *
	 * @since Kunena
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
	 * Check that the Php extensions needed for Kunena are right enabled
	 * 
	 * @param   array $extensions extensions
	 *
	 * @return integer
	 *
	 * @since Kunena
	 * @throws Exception
	 */
	protected function checkPhpExtensions($extensions)
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
	 * @since Kunena
	 * @throws Exception
	 */
	protected function checkKunena($version)
	{
		$app = Factory::getApplication();

		// Always load Kunena API if it exists.
		$api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

		if (file_exists($api))
		{
			require_once $api;
		}

		// Do not install over Git repository (K1.6+).
		if ((class_exists('KunenaForum') && method_exists('KunenaForum', 'isDev') && KunenaForum::isDev()))
		{
			$app->enqueueMessage('Oops! You should not install Kunena over your Git repository!', 'notice');

			return false;
		}

		$db = Factory::getDBO();

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
	 * @param   string $path   path
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
	 * @param   string $path   path
	 * @param   array  $ignore ignore
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
	 * @param   string $path   path
	 * @param   array  $ignore ignore
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
	 * @param   string $path   path
	 * @param   array  $ignore ignore
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
	 * @internal param array $ignore
	 *
	 * @param   string $path path
	 *
	 * @return void
	 * @since    Kunena
	 */
	public function deleteKfolder($path)
	{
		Folder::delete($path);
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

		$db->setQuery('SELECT default_character_set_name FROM ' . $db->quoteName('#__kunena_version'));

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
