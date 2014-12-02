<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Template
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Template Helper Class
 */
abstract class KunenaTemplateHelper {
	protected static $_instances = array ();

	public static function isDefault($template) {
		$config = KunenaFactory::getConfig ();
		$defaultemplate = $config->template;
		return $defaultemplate == $template ? 1 : 0;
	}

	public static function parseXmlFiles($templateBaseDir = null) {
		// Read the template folder to find templates
		if (!$templateBaseDir) $templateBaseDir = KPATH_SITE.'/template';
		jimport('joomla.filesystem.folder');

		$data = self::parseXmlFile('', $templateBaseDir);
		if ($data) {
			// Guess template folder.
			$data->directory = preg_replace('/[^a-z0-9_]/', '', preg_replace('/\s+/', '_', strtolower($data->name)));
			if (!$data->directory) return array();
			// Template found from the root (folder cannot contain more than one template)
			return array('' => $data);
		}
		$templateDirs = JFolder::folders($templateBaseDir);
		$rows = array();
		// Check that the directory contains an xml file
		foreach ($templateDirs as $templateDir)
		{
			$data = self::parseXmlFile($templateDir, $templateBaseDir);
			if($data) {
				$rows[$templateDir] = $data;
			}
		}
		ksort($rows);
		return $rows;
	}

	public static function parseXmlFile($templateDir, $templateBaseDir = null) {
		// Check if the xml file exists
		if (!$templateBaseDir) $templateBaseDir = KPATH_SITE.'/template';
		if(!is_file($templateBaseDir.'/'.$templateDir.'/template.xml')) {
			return false;
		}
		$data = self::parseKunenaInstallFile($templateBaseDir.'/'.$templateDir.'/template.xml');
		if (!$data || $data->type != 'kunena-template') {
			return false;
		}
		$data->sourcedir = basename($templateDir);
		$data->directory = basename($templateDir);
		return $data;
	}

	public static function parseKunenaInstallFile($path) {
		$xml = simplexml_load_file($path);
		if (!$xml || $xml->getName() != 'kinstall') {
			return false;
		}

		$data = new stdClass();
		$data->name = (string) $xml->name;
		$data->type = (string) $xml->attributes()->type;
		$data->creationdate = (string) $xml->creationDate;
		$data->author = (string) $xml->author;
		$data->copyright = (string) $xml->copyright;
		$data->authorEmail = (string) $xml->authorEmail;
		$data->authorUrl = (string) $xml->authorUrl;
		$data->version = (string) $xml->version;
		$data->description = (string) $xml->description;
		$data->thumbnail = (string) $xml->thumbnail;

		if ($data->version == '@kunenaversion@') $data->version = KunenaForum::version();
		if ($data->creationdate == '@kunenaversiondate@') $data->creationdate = KunenaForum::versionDate();

		if (!$data->version) $data->version = JText::_('Unknown');
		if (!$data->creationdate) $data->creationdate = JText::_('Unknown');
		if (!$data->author) $data->author = JText::_('Unknown');

		return $data;
	}
}
