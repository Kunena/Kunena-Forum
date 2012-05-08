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
jimport( 'joomla.filesystem.folder' );

class Kunena_Language_PackInstallerScript {

	public function install($parent) {
		// Install languages
		$lang = JFactory::getLanguage();
		$languages = $lang->getKnownLanguages();
		foreach ($languages as $language) {
			echo $this->installLanguage($parent, $language['tag'], $language['name']);
		}
	}

	public function discover_install($parent) {
		return self::install($parent);
	}

	public function update($parent) {
		self::install($parent);
	}

	public function uninstall($parent) {
	}

	public function preflight($type, $parent) {
		$app = JFactory::getApplication();

		// Do not install if Kunena doesn't exist
		if (!class_exists('KunenaForum') || !KunenaForum::isCompatible('2.0')) {
			$app->enqueueMessage(sprintf ( 'Kunena %s has not been installed, aborting!', '2.0' ), 'notice');
			return false;
		}
		if (KunenaForum::isDev()) {
			$app->enqueueMessage(sprintf ( 'You have installed Kunena from GitHub, aborting!' ), 'notice');
			return false;
		}
		return true;
	}

	public function postflight($type, $parent) {
	}

	public function installLanguage($parent, $tag, $name) {
		if ($tag == 'en-GB') return;
		$exists = false;
		$success = true;
		$source = $parent->getParent()->getPath('source').'/language';
		$destinations = array(
				'site'=>JPATH_SITE . '/components/com_kunena',
				'admin'=>JPATH_ADMINISTRATOR . '/components/com_kunena'
		);

		$version = KunenaForum::version();
		$file = "{$source}/com_kunena.{$tag}_v{$version}";
		if (file_exists("{$file}.zip")) {
			$file .= ".zip";
		} elseif (file_exists("{$file}.tar")) {
			$file .= ".tar";
		} elseif (file_exists("{$file}.tar.gz")) {
			$file .= ".tar.gz";
		} elseif (file_exists("{$file}.tar.bz2")) {
			$file .= ".tar.bz2";
		} else {
			// File was not found
			return sprintf('Language %s - %s ... ', $tag, $name) . sprintf('%s NOT FOUND %s', '<span style="color:#cf7f00">', '</span>') . '<br />';;
		}

		// First we need to unzip language file.
		$dir ="{$source}/{$tag}";
		if (!JFolder::exists($dir)) $success = JFolder::create($dir);
		if ($success) $success = JArchive::extract ( $file, $dir );

		foreach ($destinations as $key=>$dest) {
			if ($success != true) continue;

			$installdir = "{$dest}/language/{$tag}";
			if (!JFolder::exists($installdir)) $success = JFolder::create($installdir);
			if ($success) $success = JFolder::move("{$dir}/{$key}", $installdir);

			// Older versions installed language files into main folders
			// Those files need to be removed to bring language up to date!
			jimport('joomla.filesystem.folder');
			$files = JFolder::files($installdir, '\.ini$');
			foreach ($files as $filename) {
				if ($key=='site' && file_exists(JPATH_SITE."/language/{$tag}/{$filename}")) JFile::delete(JPATH_SITE."/language/{$tag}/{$filename}");
				if ($key=='admin' && file_exists(JPATH_ADMINISTRATOR."/language/{$tag}/{$filename}")) JFile::delete(JPATH_ADMINISTRATOR."/language/{$tag}/{$filename}");
			}
		}
		return sprintf('Language %s - %s ... ', $tag, $name) . ($success? sprintf('%s INSTALLED %s', '<span style="color:darkgreen">', '</span>') : sprintf('%s FAILED %s', '<span style="color:darkred">', '</span>')) . '<br />';
	}
}
