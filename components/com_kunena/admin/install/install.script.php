<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

class Com_KunenaInstallerScript {
	protected $versions = array(
		'PHP' => array (
			'5.3' => '5.3.1',
			'0' => '5.4.23' // Preferred version
		),
		'MySQL' => array (
			'5.1' => '5.1',
			'0' => '5.5' // Preferred version
		),
		'Joomla!' => array (
			'3.3' => '3.3.1',
			'3.2' => '3.2.4',
			'3.1' => '3.1.5',
			'3.0' => '3.0.4',
			'2.5' => '2.5.9',
			'0' => '2.5.22' // Preferred version
		)
	);
	protected $extensions = array ('dom', 'gd', 'json', 'pcre', 'SimpleXML');

	public function install($parent) {
		// Delete all cached files.
		$cacheDir = JPATH_CACHE.'/kunena';
		if (is_dir($cacheDir)) JFolder::delete($cacheDir);
		JFolder::create($cacheDir);
		return true;
	}

	public function discover_install($parent) {
		return self::install($parent);
	}

	public function update($parent) {
		return self::install($parent);
	}

	public function uninstall($parent) {
		$adminpath = $parent->getParent()->getPath('extension_administrator');
		$model = "{$adminpath}/install/model.php";
		if (file_exists($model)) {
			require_once($model);
			$installer = new KunenaModelInstall();
			$installer->uninstall();
		}
		return true;
	}

	public function preflight($type, $parent) {
		$parent = $parent->getParent();
		$manifest = $parent->getManifest();

		// Prevent installation if requirements are not met.
		if (!$this->checkRequirements($manifest->version)) return false;

		$adminPath = $parent->getPath('extension_administrator');
		$sitePath = $parent->getPath('extension_site');

		if (is_file($adminPath.'/admin.kunena.php')) {
			// Kunena 2.0 or older release found, clean up the directories.
			static $ignoreAdmin = array('index.html', 'kunena.xml', 'archive');
			if (is_file($adminPath.'/install.script.php')) {
				// Kunena 1.7 or older release..
				$ignoreAdmin[] = 'install.script.php';
				$ignoreAdmin[] = 'admin.kunena.php';
			}
			static $ignoreSite = array('index.html', 'kunena.php', 'router.php', 'template', 'COPYRIGHT.php', 'CHANGELOG.php');
			$this->deleteFolder($adminPath, $ignoreAdmin);
			$this->deleteFolder($sitePath, $ignoreSite);
			$this->deleteFolder($sitePath.'/template/blue_eagle', array('params.ini'));
			// TODO: delete also en-GB files!
		}

		// Prepare installation.
		$model = "{$adminPath}/install/model.php";
		if (file_exists($model)) {
			require_once($model);
			$installer = new KunenaModelInstall();
			$installer->install();
		}

		return true;
	}

	public function postflight($type, $parent) {
		return true;
	}

	public function checkRequirements($version) {
		$db = JFactory::getDbo();
		$pass  = $this->checkVersion('PHP', phpversion());
		$pass &= $this->checkVersion('Joomla!', JVERSION);
		$pass &= $this->checkVersion('MySQL', $db->getVersion ());
		$pass &= $this->checkDbo($db->name, array('mysql', 'mysqli'));
		$pass &= $this->checkExtensions($this->extensions);
		$pass &= $this->checkKunena($version);
		return $pass;
	}

	// Internal functions

	protected function checkVersion($name, $version) {
		$app = JFactory::getApplication();

		$major = $minor = 0;
		foreach ($this->versions[$name] as $major=>$minor) {
			if (!$major || version_compare($version, $major, '<')) continue;
			if (version_compare($version, $minor, '>=')) return true;
			break;
		}
		if (!$major) $minor = reset($this->versions[$name]);
		$recommended = end($this->versions[$name]);
		$app->enqueueMessage(sprintf("%s %s is not supported. Minimum required version is %s %s, but it is higly recommended to use %s %s or later.", $name, $version, $name, $minor, $name, $recommended), 'notice');
		return false;
	}

	protected function checkDbo($name, $types) {
		$app = JFactory::getApplication();

		if (in_array($name, $types)) {
			return true;
		}
		$app->enqueueMessage(sprintf("Database driver '%s' is not supported. Please use MySQL instead.", $name), 'notice');
		return false;
	}

	protected function checkExtensions($extensions) {
		$app = JFactory::getApplication();

		$pass = 1;
		foreach ($extensions as $name) {
			if (!extension_loaded($name)) {
				$pass = 0;
				$app->enqueueMessage(sprintf("Required PHP extension '%s' is missing. Please install it into your system.", $name), 'notice');
			}
		}
		return $pass;
	}

	protected function checkKunena($version) {
		$app = JFactory::getApplication();

		// Always load Kunena API if it exists.
		$api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';
		if (file_exists($api)) require_once $api;

		// Do not install over Git repository (K1.6+).
		if ((class_exists('Kunena') && method_exists('Kunena', 'isSvn') && Kunena::isSvn())
				|| (class_exists('KunenaForum') && method_exists('KunenaForum', 'isDev') && KunenaForum::isDev())) {
			$app->enqueueMessage('Oops! You should not install Kunena over your Git reporitory!', 'notice');
			return false;
		}

		$db = JFactory::getDBO();

		// Check if Kunena can be found from the database
		$table = $db->getPrefix().'kunena_version';
		$db->setQuery("SHOW TABLES LIKE {$db->quote($table)}");
		if ($db->loadResult() != $table) return true;

		// Get installed Kunena version
		$db->setQuery("SELECT version FROM {$db->quoteName($table)} ORDER BY `id` DESC", 0, 1);
		$installed = $db->loadResult();
		if (!$installed) return true;

		// Always allow upgrade to the newer version
		if (version_compare($version, $installed, '>=')) return true;

		// Check if we can downgrade to the current version
		if (class_exists('KunenaInstaller')) {
			if (KunenaInstaller::canDowngrade($version)) return true;
		} else {
			// Workaround when Kunena files were removed to allow downgrade between bugfix versions.
			$major = preg_replace('/(\d+.\d+)\..*$/', '\\1', $version);
			if (version_compare($installed, $major, '>')) return true;
		}

		$app->enqueueMessage(sprintf('Sorry, it is not possible to downgrade Kunena %s to version %s.', $installed, $version), 'notice');
		return false;
	}

	public function deleteFiles($path, $ignore=array()) {
		$ignore = array_merge($ignore, array('.git', '.svn', 'CVS','.DS_Store','__MACOSX'));
		if ( JFolder::exists($path) ) foreach (JFolder::files($path, '.', false, true, $ignore) as $file) {
			if ( JFile::exists($file) ) {
				JFile::delete($file);
			}
		}
	}

	public function deleteFolders($path, $ignore=array()) {
		$ignore = array_merge($ignore, array('.git', '.svn', 'CVS','.DS_Store','__MACOSX'));
		if ( JFolder::exists($path) ) foreach (JFolder::folders($path, '.', false, true, $ignore) as $folder) {
			if ( JFolder::exists($folder) ) {
				JFolder::delete($folder);
			}
		}
	}

	public function deleteFolder($path, $ignore=array()) {
		$this->deleteFiles($path, $ignore);
		$this->deleteFolders($path, $ignore);
	}
}
