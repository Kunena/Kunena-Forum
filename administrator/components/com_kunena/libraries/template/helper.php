<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Template
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
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
		$templateDirs = JFolder::folders($templateBaseDir);
		$rows = array();
		// Check that the directory contains an xml file
		foreach ($templateDirs as $templateDir)
		{
			if(!$data = self::parseXmlFile($templateDir, $templateBaseDir)){
				continue;
			} else {
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
		if ($data->type != 'kunena-template') {
			return false;
		}
		$data->directory = basename($templateDir);
		return $data;
	}

	public static function parseKunenaInstallFile($path) {
		// FIXME : deprecated under Joomla! 1.6
		$xml = JFactory::getXMLParser ( 'Simple' );
		if (! $xml->loadFile ( $path )) {
			unset ( $xml );
			return false;
		}
		if (! is_object ( $xml->document ) || ($xml->document->name () != 'kinstall')) {
			unset ( $xml );
			return false;
		}

		$data = new stdClass ();
		$element = & $xml->document->name [0];
		$data->name = $element ? $element->data () : '';
		$data->type = $element ? $xml->document->attributes ( "type" ) : '';

		$element = & $xml->document->creationDate [0];
		$data->creationdate = $element ? $element->data () : JText::_ ( 'Unknown' );

		$element = & $xml->document->author [0];
		$data->author = $element ? $element->data () : JText::_ ( 'Unknown' );

		$element = & $xml->document->copyright [0];
		$data->copyright = $element ? $element->data () : '';

		$element = & $xml->document->authorEmail [0];
		$data->authorEmail = $element ? $element->data () : '';

		$element = & $xml->document->authorUrl [0];
		$data->authorUrl = $element ? $element->data () : '';

		$element = & $xml->document->version [0];
		$data->version = $element ? $element->data () : '';

		$element = & $xml->document->description [0];
		$data->description = $element ? $element->data () : '';

		$element = & $xml->document->thumbnail [0];
		$data->thumbnail = $element ? $element->data () : '';

		return $data;
	}
}