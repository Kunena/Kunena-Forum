<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
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
		),
		'MySQL' => array (
			'5.0' => '5.0.4',
		),
		'Joomla' => array (
			'2.5' => '2.5.3',
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
		if (!$this->checkRequirements()) return false;

		// Do not install over Git repository.
		if ((class_exists('Kunena') && method_exists('Kunena', 'isSvn') && Kunena::isSvn())
			|| (class_exists('KunenaForum') && method_exists('KunenaForum', 'isDev') && KunenaForum::isDev())) {
			JFactory::getApplication()->enqueueMessage('Oops! You should not install Kunena over your Git reporitory!', 'notice');
			return false;
		}

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

	// Internal functions

	protected function checkRequirements() {
		$pass = $this->checkVersion('Joomla', JVERSION);
		$pass &= $this->checkVersion('MySQL', JFactory::getDbo()->getVersion ());
		$pass &= $this->checkVersion('PHP', phpversion());
		foreach ($this->extensions as $name) {
			if (!extension_loaded($name)) {
				JFactory::getApplication()->enqueueMessage(sprintf('Missing PHP extension: %s.', $name), 'notice');
				$pass = false;
			}
		}
		return $pass;
	}

	protected function checkVersion($name, $version) {
		foreach ($this->versions[$name] as $major=>$minor) {
			if (version_compare ( $version, $major, "<" )) continue;
			if (version_compare ( $version, $minor, ">=" )) return true;
			break;
		}
		JFactory::getApplication()->enqueueMessage(sprintf('%s %s required (you have %s %s).', $name, $minor, $name, $version), 'notice');
		return false;
	}
}
