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
			'2.5' => '2.5',
			'1.6' => '1.7.3',
			'1.5' => '1.6.25',
		)
	);
	protected $extensions = array ('dom', 'gd', 'json', 'pcre', 'SimpleXML');

	function install($parent) {
		$app = JFactory::getApplication();
		$app->setUserState('com_kunena.install.step', 0);

		// Install English and default language
		require_once(JPATH_ADMINISTRATOR . '/components/com_kunena/install/model.php');
		$installer = new KunenaModelInstall();
		$installer->installLanguage('en-GB');
		$lang = JFactory::getLanguage();
		$tag = $lang->getTag();
		if ($tag != 'en-GB') $installer->installLanguage($tag);
		return true;
	}

	function discover_install($parent) {
		return self::install($parent);
	}

	function update($parent) {
		return self::install($parent);
	}

	function uninstall($parent) {
		require_once (JPATH_ADMINISTRATOR . '/components/com_kunena/api.php');
		$lang = JFactory::getLanguage();
		$lang->load('com_kunena.install',JPATH_ADMINISTRATOR);

		require_once(KPATH_ADMIN . '/install/model.php');
		$installer = new KunenaModelInstall();
		$installer->uninstallPlugin('kunena', 'alphauserpoints');
		$installer->uninstallPlugin('kunena', 'community');
		$installer->uninstallPlugin('kunena', 'comprofiler');
		$installer->uninstallPlugin('kunena', 'gravatar');
		$installer->uninstallPlugin('kunena', 'joomla');
		$installer->uninstallPlugin('kunena', 'kunena');
		$installer->uninstallPlugin('kunena', 'uddeim');
		$installer->uninstallPlugin('system', 'kunena');
		$installer->deleteMenu();
		return true;
	}

	function preflight($type, $parent) {
		// TODO: Before install: we want so store files so that user can cancel action
		if (version_compare(JVERSION, '1.6', '>')) {

			// Prevent installation if requirements are not met.
			if (!$this->checkRequirements()) return false;

			// Do not install over Git repository.
			if (class_exists('Kunena') && Kunena::isSvn() || class_exists('KunenaForum') && KunenaForum::isDev()) {
				JFactory::getApplication()->enqueueMessage('Oops! You should not install Kunena over your Git reporitory!', 'notice');
				return false;
			}
			$installer = $parent->getParent();
			$adminpath = $installer->extension_administrator;
			if ( JFolder::exists($adminpath)) {
				// Delete old zip files
				$archive = "{$adminpath}/archive";
				if ( JFolder::exists($archive)) {
					JFolder::delete($archive);
					// We want to keep empty directory (it is defined in manifest file)
					JFolder::create($archive);
				}
			}
		}
		return true;
	}

	function postflight($type, $parent) {
		$redirect_url = JURI::base () . 'index.php?option=com_kunena&view=install&task=prepare&'.JUtility::getToken().'=1';
		if (version_compare(JVERSION, '1.6', '>')) {
			// Joomla 1.6+
			$installer = $parent->getParent();

			// Set redirect
			$installer->set('redirect_url', $redirect_url);

			// Rename kunena.j16.xml to kunena.xml
			$adminpath = $installer->extension_administrator;
			if (JFile::exists("{$adminpath}/kunena.j16.xml")) {
				if ( JFile::exists("{$adminpath}/kunena.xml")) JFile::delete("{$adminpath}/kunena.xml");
				JFile::move("{$adminpath}/kunena.j16.xml", "{$adminpath}/kunena.xml");
			}
		} else {
			// Joomla 1.5
			$manifest = JPATH_ADMINISTRATOR . '/components/com_kunena/manifest.xml';
			if (JFile::exists($manifest)) {
				// Remove deprecated manifest.xml (K1.5)
				JFile::delete($manifest);
			}

			return $redirect_url;
		}

		return true;
	}

	protected function checkRequirements() {
		$pass = $this->checkVersion('Joomla', JVERSION);
		$pass &= $this->checkVersion('MySQL', JFactory::getDbo()->getVersion ());
		$pass &= $this->checkVersion('PHP', phpversion());
		foreach (!$this->extensions as $name) {
			if (extension_loaded($name)) {
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
