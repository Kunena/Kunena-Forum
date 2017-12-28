<?php
/**
 * Kunena Component
 * @package     Kunena.Framework
 * @subpackage  Template
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Kunena Template Helper Class
 *
 * @since  K2.0
 */
abstract class KunenaTemplateHelper
{
	protected static $_instances = array ();

	/**
	 * isDefault
	 *
	 * @param $template
	 *
	 * @return integer
	 */
	public static function isDefault($template)
	{
		$config = KunenaFactory::getConfig();
		$defaultemplate = $config->template;

		return $defaultemplate == $template ? 1 : 0;
	}

	/**
	 *  parseXmlFiles
	 *
	 * @param   null $templateBaseDir
	 *
	 * @return array
	 */
	public static function parseXmlFiles($templateBaseDir = null)
	{
		// Read the template folder to find templates
		if (!$templateBaseDir)
		{
			$templateBaseDir = KPATH_SITE . '/template';
		}

		$data = self::parseXmlFile('', $templateBaseDir);

		if ($data)
		{
			// Guess template folder.
			$data->directory = preg_replace('/[^a-z0-9_]/', '', preg_replace('/\s+/', '_', strtolower($data->name)));

			if (!$data->directory)
			{
				return array();
			}

			// Template found from the root (folder cannot contain more than one template)
			return array('' => $data);
		}

		$templateDirs = KunenaFolder::folders($templateBaseDir);
		$rows = array();

		// Check that the directory contains an xml file
		foreach ($templateDirs as $templateDir)
		{
			$data = self::parseXmlFile($templateDir, $templateBaseDir);

			if ($data)
			{
				$rows[$templateDir] = $data;
			}
		}

		ksort($rows);

		return $rows;
	}

	/**
	 * @param      $templateDir
	 * @param   null $templateBaseDir
	 *
	 * @return boolean|stdClass
	 */
	public static function parseXmlFile($templateDir, $templateBaseDir = null)
	{
		// Check if the xml file exists
		if (!$templateBaseDir)
		{
			$templateBaseDir = KPATH_SITE . '/template';
		}

		if (!is_file($templateBaseDir . '/' . $templateDir . '/config/template.xml'))
		{
			return false;
		}

		$data = self::parseKunenaInstallFile($templateBaseDir . '/' . $templateDir . '/config/template.xml');

		if (!$data || $data->type != 'kunena-template')
		{
			return false;
		}

		$data->sourcedir = basename($templateDir);
		$data->directory = basename($templateDir);

		return $data;
	}

	/**
	 * parseKunenaInstallFile
	 *
	 * @param   $path
	 *
	 * @return bool|stdClass
	 */
	public static function parseKunenaInstallFile($path)
	{
		$xml = simplexml_load_file($path);

		if (!$xml || $xml->getName() != 'kinstall')
		{
			return false;
		}

		$data = new stdClass;
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
		$data->kversion = (string) $xml->attributes()->version;

		if ($data->version == '@kunenaversion@')
		{
			$data->version = KunenaForum::version();
		}

		if ($data->creationdate == '@kunenaversiondate@')
		{
			$data->creationdate = KunenaForum::versionDate();
		}

		if (!$data->version)
		{
			$data->version = JText::_('Unknown');
		}

		if (!$data->creationdate)
		{
			$data->creationdate = JText::_('Unknown');
		}

		if (!$data->author)
		{
			$data->author = JText::_('Unknown');
		}

		return $data;
	}

	/**
	 * Check if crypsis template can be used on Joomla! version used
	 *
	 * @param   string  $templatename  The name of template which needs to be checked
	 *
	 * @return boolean
	 */
	public static function templateCanBeUsed($templatename)
	{
		if ($templatename == 'Crypsis' || $templatename == 'Crypsisb3' )
		{
			return false;
		}

		return true;
	}
}
