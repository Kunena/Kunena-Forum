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
			'5.2' => '5.2.4',
			'0' => '5.3.10' // Preferred version
		),
		'MySQL' => array (
			'5.0' => '5.0.4',
			'0' => '5.1' // Preferred version
		),
		'Joomla!' => array (
			'1.5' => '1.5.25',
			'1.6' => '2.5.3',
			'0' => '2.5.4' // Preferred version
		)
	);
	protected $extensions = array ('dom', 'gd', 'json', 'pcre', 'SimpleXML');

	public function install($parent) {
		require_once(JPATH_ADMINISTRATOR . '/components/com_kunena/install/model.php');
		$installer = new KunenaModelInstall();
		$installer->install();
		return true;
	}

	public function discover_install($parent) {
		return self::install($parent);
	}

	public function update($parent) {
		return self::install($parent);
	}

	public function uninstall($parent) {
		require_once(JPATH_ADMINISTRATOR . '/components/com_kunena/install/model.php');
		$installer = new KunenaModelInstall();
		$installer->uninstall();
		return true;
	}

	public function preflight($type, $parent) {
		// Prevent installation if requirements are not met.
		$version = $parent->getParent()->getManifest()->version;
		if (!$this->checkRequirements($version)) return false;

		// Remove deprecated manifest.xml (K1.5) and kunena.j16.xml (K1.7)
		$manifest = JPATH_ADMINISTRATOR . '/components/com_kunena/manifest.xml';
		if (JFile::exists($manifest)) JFile::delete($manifest);
		$manifest = JPATH_ADMINISTRATOR . '/components/com_kunena/kunena.j16.xml';
		if (JFile::exists($manifest)) JFile::delete($manifest);

		// TODO: Before install: we want so store files so that user can cancel action

		$adminpath = KPATH_ADMIN;
		if ( JFolder::exists($adminpath)) {
			// Delete old zip files
			$archive = "{$adminpath}/archive";
			if ( JFolder::exists($archive)) {
				JFolder::delete($archive);
				// We want to keep empty directory (it is defined in manifest file)
				JFolder::create($archive);
			}
		}
		return true;
	}

	public function postflight($type, $parent) {
		$installer = $parent->getParent();
		$adminpath = KPATH_ADMIN;

		// Rename kunena.j25.xml to kunena.xml
		if (JFile::exists("{$adminpath}/kunena.j25.xml")) {
			if ( JFile::exists("{$adminpath}/kunena.xml")) JFile::delete("{$adminpath}/kunena.xml");
			JFile::move("{$adminpath}/kunena.j25.xml", "{$adminpath}/kunena.xml");
		}

		// Set redirect
		$installer->set('redirect_url', JURI::base () . 'index.php?option=com_kunena&view=install');

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

		$app->enqueueMessage(sprintf('Sorry, it is not possible downgrade Kunena %s to version %s.', $installed, $version), 'notice');
		return false;
	}
}
