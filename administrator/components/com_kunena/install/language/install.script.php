<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined( '_JEXEC' ) or die();

jimport( 'joomla.filesystem.file' );

class Kunena_Language_PackInstallerScript {

	function install($parent) {
		// Install languages
		$lang = JFactory::getLanguage();
		$languages = $lang->getKnownLanguages();
		foreach ($languages as $language) {
			echo $this->installLanguage($parent, $language['tag'], $language['name']);
		}
	}

	function update($parent) {
		self::install($parent);
	}

	function uninstall($parent) {
	}

	function preflight($type, $parent) {
		// Do not install if Kunena doesn't exist
		if (!class_exists('KunenaForum') || version_compare(KunenaForum::version(), '2.0', '<')) {
			echo sprintf ( 'Kunena %s has not been installed, aborting!', '2.0' );
			return false;
		}
		if (KunenaForum::isDev()) {
			echo sprintf ( 'You have installed Kunena from GitHub, aborting!' );
			return false;
		}
		return true;
	}

	function postflight($type, $parent) {
	}

	function installLanguage($parent, $tag, $name) {
		$exists = false;
		$success = true;
		$source = $parent->getParent()->getPath('source').'/language';
		$destinations = array(
				'site'=>JPATH_SITE . '/components/com_kunena',
				'admin'=>JPATH_ADMINISTRATOR . '/components/com_kunena'
		);

		$version = KunenaForum::version();
		$file = "com_kunena.en-GB.site_v{$version}";
		if (file_exists("$source/$file.zip")) {
			$ext = "zip";
		} elseif (file_exists("$source/$file.tar")) {
			$ext = "tar";
		} elseif (file_exists("$source/$file.tar.gz")) {
			$ext = "tar.gz";
		} elseif (file_exists("$source/$file.tar.bz2")) {
			$ext = "tar.bz2";
		}

		foreach ($destinations as $key=>$dest) {
			if ($success != true) continue;

			// If we are installing Kunena from archive, we need to unzip language file
			$file = "{$source}/com_kunena.{$tag}.{$key}_v{$version}.{$ext}";
			$installdir = "{$dest}/language/{$tag}";

			if (file_exists($file)) {
				if (!JFolder::exists($installdir)) {
					$success = JFolder::create($installdir);
				}
				if ($success) $success = JArchive::extract ( $file, $installdir );
			}

			// Install language from dest/language/xx-XX
			if ($success == true && is_dir($installdir)) {
				$exists = true;

				// Older versions installed language files into main folders
				// Those files need to be removed to bring language up to date!
				jimport('joomla.filesystem.folder');
				$files = JFolder::files($installdir, '\.ini$');
				foreach ($files as $filename) {
					if (file_exists(JPATH_SITE."/language/{$tag}/{$filename}")) JFile::delete(JPATH_SITE."/language/{$tag}/{$filename}");
					if (file_exists(JPATH_ADMINISTRATOR."/language/{$tag}/{$filename}")) JFile::delete(JPATH_ADMINISTRATOR."/language/{$tag}/{$filename}");
				}
			}
		}
		if ($exists && $name) {
			return sprintf('Installing %s - %s ... ', $tag, $name) . ($success? sprintf('%s DONE %s', '<span style="color:darkgreen">', '</span>') : sprintf('%s FAILED %s', '<span style="color:darkred">', '</span>')) . '<br />';
		}
	}
}