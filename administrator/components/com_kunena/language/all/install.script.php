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

class pkg_kunena_languagesInstallerScript {

	public function uninstall($parent) {
		// Remove languages.
		$languages = JFactory::getLanguage()->getKnownLanguages();
		foreach ($languages as $language) {
			echo $this->uninstallLanguage($language['tag'], $language['name']);
		}
	}

	public function preflight($type, $parent) {
		if (!in_array($type, array('install', 'update'))) return true;

		$app = JFactory::getApplication();

		// Do not install if Kunena doesn't exist.
		if (!class_exists('KunenaForum') || !KunenaForum::isCompatible('3.0')) {
			$app->enqueueMessage(sprintf ( 'Kunena %s has not been installed, aborting!', '3.0' ), 'notice');
			return false;
		}
		if (KunenaForum::isDev()) {
			$app->enqueueMessage(sprintf ( 'You have installed Kunena from GitHub, aborting!' ), 'notice');
			return false;
		}

		// Get list of languages to be installed.
		$source = $parent->getParent()->getPath('source').'/language';
		$languages = JFactory::getLanguage()->getKnownLanguages();
		$files = $parent->manifest->files;
		foreach ($languages as $language) {
			$name = "com_kunena_{$language['tag']}";
			$search = JFolder::files($source, $name);
			if (empty($search)) continue;
			// Generate <file type="file" client="site" id="fi-FI">com_kunena_fi-FI_v2.0.0-BETA2-DEV2.zip</file>
			$file = $files->addChild('file', array_pop($search));
			$file->addAttribute('type', 'file');
			$file->addAttribute('client', 'site');
			$file->addAttribute('id', $name);
			echo sprintf('Installing language %s - %s ...', $language['tag'], $language['name']) . '<br />';
		}
		if (empty($files)) {
			$app->enqueueMessage(sprintf ( 'Your site is English only. There\'s no need to install Kunena language pack.' ), 'notice');
			return false;
		}

		// Remove old K1.7 style language pack.
		$table = JTable::getInstance('extension');
		$id = $table->find(array('type'=>'file', 'element'=>"kunena_language_pack"));
		if ($id) {
			$installer = new JInstaller();
			$installer->uninstall ( 'file', $id );
		}

		return true;
	}

	public function uninstallLanguage($tag, $name) {
		$table = JTable::getInstance('extension');
		$id = $table->find(array('type'=>'file', 'element'=>"com_kunena_{$tag}"));
		if (!$id) return;

		$installer = new JInstaller();
		$installer->uninstall ( 'file', $id );
	}
}
