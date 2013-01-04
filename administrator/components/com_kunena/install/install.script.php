<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
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
			'0' => '5.4.9' // Preferred version
		),
		'MySQL' => array (
			'5.1' => '5.1',
			'0' => '5.5' // Preferred version
		),
		'Joomla!' => array (
			'2.5' => '2.5.6',
			'3.0' => '3.0.2',
			'0' => '2.5.8' // Preferred version
		)
	);
	protected $extensions = array ('dom', 'gd', 'json', 'pcre', 'SimpleXML');

	public function install($parent) {
		// Reset installer state.
		$app = JFactory::getApplication();
		$app->setUserState('kunena-old', 0);

		$this->md5 = md5_file(JPATH_ADMINISTRATOR . '/components/com_kunena/install/entrypoints/admin.kunena.php');
		$success = JFile::copy(
				JPATH_ADMINISTRATOR . '/components/com_kunena/install/entrypoints/admin.kunena.php',
				JPATH_ADMINISTRATOR . '/components/com_kunena/kunena.php');
		return $success;
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
		// Bugfix for "Can not build admin menus"
		if(in_array($type, array('install','discover_install'))) {
			$this->bugfixDBFunctionReturnedNoError();
		} else {
			$this->bugfixCantBuildAdminMenus();
		}

		$manifest = $parent->getParent()->getManifest();

		// Prevent installation if requirements are not met.
		if (!$this->checkRequirements($manifest->version)) return false;

// TODO: If we do not need to display the warning (but only after dropping J1.5 support)
//		if (version_compare($manifest->version, '2.0', '>')) return true;

		$adminpath = $parent->getParent()->getPath('extension_administrator');
		$sitepath = $parent->getParent()->getPath('extension_site');

		// If Kunena wasn't installed, there's nothing to do.
		if(!file_exists($adminpath)) return true;

		// Find the old manifest file.
		$tmpInstaller = new JInstaller;
		$tmpInstaller->setPath('source', $adminpath);
		$obj_manifest = $tmpInstaller->findManifest() ? $tmpInstaller->getManifest() : null;
		$old_manifest = basename($tmpInstaller->getPath('manifest'));
		if ($obj_manifest) {
			$this->oldAdminFiles = $this->findFilesFromManifest($obj_manifest->administration->files) + array($old_manifest=>$old_manifest);
			$this->oldAdminFiles += $this->findFilesFromManifest($manifest->administration->files);
			$this->oldFiles = $this->findFilesFromManifest($obj_manifest->files);
			$this->oldFiles += $this->findFilesFromManifest($manifest->files);
		}

		// Detect existing installation.
		if ($old_manifest && JFile::exists("{$adminpath}/kunena.php")) {
			$contents = file_get_contents("{$adminpath}/kunena.php");
		} elseif ($old_manifest && JFile::exists("{$adminpath}/admin.kunena.php")) {
			$contents = file_get_contents("{$adminpath}/admin.kunena.php");
			$rename = true;
		}

		if (!empty($contents) && !strstr($contents, '/* KUNENA FORUM INSTALLER */')) {

			// If we don't find Kunena 2.0 installer, backup existing files...
			$backuppath = "{$adminpath}/bak";
			if (JFolder::exists($backuppath)) JFolder::delete($backuppath);
			$this->backup($adminpath, $backuppath, $this->oldAdminFiles);

			$backuppath = "{$sitepath}/bak";
			if (JFolder::exists($backuppath)) JFolder::delete($backuppath);
			$this->backup($sitepath, $backuppath, $this->oldFiles);
		}

		// Remove old manifest files, excluding the current one.
		$manifests = array('manifest.xml', 'kunena.j16.xml', 'kunena.j25.xml', 'kunena.xml');
		foreach ($manifests as $filename) {
			if ($filename == $old_manifest) continue;
			if (JFile::exists("{$adminpath}/{$filename}")) JFile::delete("{$adminpath}/{$filename}");
		}

		clearstatcache();
		return true;
	}

	public function postflight($type, $parent) {
		$installer = $parent->getParent();
		$manifest = $installer->getManifest();
		$adminpath = $installer->getPath('extension_administrator');
		$sitepath = $installer->getPath('extension_site');

		if (isset($this->oldAdminFiles)) {
			// Backup the new installation.
			if (file_exists("{$adminpath}/new")) JFolder::delete("{$adminpath}/new");
			$this->backup($adminpath, "{$adminpath}/new", $this->findFilesFromManifest($manifest->administration->files) + array('kunena.xml'=>'kunena.xml'));
			$this->backup($sitepath, "{$sitepath}/new", $this->findFilesFromManifest($manifest->files));

			// Copy back files removed by Joomla installer (except for kunena.php).
			unset ($this->oldAdminFiles['kunena.php']);
			$this->backup("{$adminpath}/bak", $adminpath, $this->oldAdminFiles);
			$this->backup("{$sitepath}/bak", $sitepath, $this->oldFiles);
		}

		// Test if bootstrap file has been fully copied
		$this->waitFile("{$adminpath}/kunena.php", $this->md5);

		// Set redirect.
		$installer->set('redirect_url', JRoute::_('index.php?option=com_kunena', false));

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

		foreach ($this->versions[$name] as $major=>$minor) {
			if (!$major || version_compare ( $version, $major, "<" )) continue;
			if (version_compare ( $version, $minor, ">=" )) return true;
			break;
		}
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
		if (file_exists ( $api )) require_once $api;

		// Do not install over Git repository (K1.6+).
		if ((class_exists('Kunena') && method_exists('Kunena', 'isSvn') && Kunena::isSvn())
				|| (class_exists('KunenaForum') && method_exists('KunenaForum', 'isDev') && KunenaForum::isDev())) {
			$app->enqueueMessage('Oops! You should not install Kunena over your Git reporitory!', 'notice');
			return false;
		}

		$db = JFactory::getDBO();

		// Check if Kunena can be found from the database
		$table = $db->getPrefix().'kunena_version';
		$db->setQuery ( "SHOW TABLES LIKE {$db->quote($table)}" );
		if ($db->loadResult () != $table) return true;

		// Get installed Kunena version
		$db->setQuery("SELECT version FROM {$table} ORDER BY `id` DESC", 0, 1);
		$installed = $db->loadResult();
		if (!$installed) return true;

		// Always allow upgrade to the newer version
		if (version_compare($version, $installed, '>=')) return true;

		// Check if we can downgrade to the current version
		if (class_exists('KunenaInstaller') && KunenaInstaller::canDowngrade($version)) {
			return true;
		}

		$app->enqueueMessage(sprintf('Sorry, it is not possible to downgrade Kunena %s to version %s.', $installed, $version), 'notice');
		return false;
	}

	protected function backup($from, $to, $list, $replace = true) {
		if (!JFolder::exists($to)) JFolder::create($to);
		if ($replace) {
			// Delete all existing entries
			clearstatcache();
			foreach ($list as $filename) {
				if (is_dir("{$from}/{$filename}")) {
					if (file_exists("{$to}/{$filename}")) JFolder::delete("{$to}/{$filename}");
				} elseif (is_file("{$from}/{$filename}")) {
					if (file_exists("{$to}/{$filename}")) JFile::delete("{$to}/{$filename}");
				}
			}
		}
		clearstatcache();
		// Copy all entries
		foreach ($list as $filename) {
			if (file_exists("{$to}/{$filename}")) continue;
			if (is_dir("{$from}/{$filename}")) {
				JFolder::move("{$from}/{$filename}", "{$to}/{$filename}");
				JFolder::create("{$from}/{$filename}");
			} elseif (is_file("{$from}/{$filename}")) {
				JFile::copy("{$from}/{$filename}", "{$to}/{$filename}");
			}
		}
	}

	protected function findFilesFromManifest($files) {
		$list = array();
		if ($files && ($files instanceof SimpleXMLElement)) {
			$entries = $files->children();
			foreach($entries as $entry) {
				$list[(string) $entry] = (string) $entry;
			}
		}

		return $list;
	}

	/**
	 * Joomla! 1.6+ bugfix for "DB function returned no error"
	 * Taken from Akeeba components
	 */
	protected function bugfixDBFunctionReturnedNoError() {
		$db = JFactory::getDbo();

		// Fix broken #__assets records
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__assets')
			->where($db->qn('name').' = '.$db->q('com_kunena'));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__assets')
				->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}

		// Fix broken #__extensions records
		$query = $db->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where($db->qn('element').' = '.$db->q('com_kunena'));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__extensions')
				->where($db->qn('extension_id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}

		// Fix broken #__menu records
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__menu')
			->where($db->qn('type').' = '.$db->q('component'))
			->where($db->qn('menutype').' = '.$db->q('main'))
			->where($db->qn('link').' LIKE '.$db->q('index.php?option='.'com_kunena'));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__menu')
				->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Joomla! 1.6+ bugfix for "Can not build admin menus"
	 * Taken from Akeeba components
	 */
	protected function bugfixCantBuildAdminMenus() {
		$db = JFactory::getDbo();

		// If there are multiple #__extensions record, keep one of them
		$query = $db->getQuery(true);
		$query->select('extension_id')
		->from('#__extensions')
		->where($db->qn('element').' = '.$db->q('com_kunena'));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(count($ids) > 1) {
			asort($ids);
			$extension_id = array_shift($ids); // Keep the oldest id

			foreach($ids as $id) {
				$query = $db->getQuery(true);
				$query->delete('#__extensions')
				->where($db->qn('extension_id').' = '.$db->q($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// If there are multiple assets records, delete all except the oldest one
		$query = $db->getQuery(true);
		$query->select('id')
		->from('#__assets')
		->where($db->qn('name').' = '.$db->q('com_kunena'));
		$db->setQuery($query);
		$ids = $db->loadObjectList();
		if(count($ids) > 1) {
			asort($ids);
			$asset_id = array_shift($ids); // Keep the oldest id

			foreach($ids as $id) {
				$query = $db->getQuery(true);
				$query->delete('#__assets')
				->where($db->qn('id').' = '.$db->q($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// Remove #__menu records for good measure!
		$query = $db->getQuery(true);
		$query->select('id')
		->from('#__menu')
		->where($db->qn('type').' = '.$db->q('component'))
		->where($db->qn('menutype').' = '.$db->q('main'))
		->where($db->qn('link').' LIKE '.$db->q('index.php?option='.'com_kunena'));
		$db->setQuery($query);
		$ids1 = $db->loadColumn();
		if(empty($ids1)) $ids1 = array();
		$query = $db->getQuery(true);
		$query->select('id')
		->from('#__menu')
		->where($db->qn('type').' = '.$db->q('component'))
		->where($db->qn('menutype').' = '.$db->q('main'))
		->where($db->qn('link').' LIKE '.$db->q('index.php?option='.'com_kunena'.'&%'));
		$db->setQuery($query);
		$ids2 = $db->loadColumn();
		if(empty($ids2)) $ids2 = array();
		$ids = array_merge($ids1, $ids2);
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__menu')
			->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}
	}

	protected function waitFile($file, $md5) {
		// Test if file has been fully copied and wait if not
		for ($i=0; $i<10; $i++) {
			if (is_file($file) && md5_file($file) == $md5) return true;
			sleep(1);
			clearstatcache();
		}
		return false;
	}
}
