<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Template
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Template;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use stdClass;

/**
 * Kunena Template Helper Class
 *
 * @since  K2.0
 */
abstract class KunenaTemplateHelper
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected static $_instances = [];

	/**
	 * isDefault
	 *
	 * @param   string  $template  template
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public static function isDefault(string $template): int
	{
		$config         = KunenaFactory::getConfig();
		$defaultemplate = $config->template;

		return $defaultemplate == $template ? 1 : 0;
	}

	/**
	 * parseXmlFiles
	 *
	 * @param   null  $templateBaseDir  template
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public static function parseXmlFiles($templateBaseDir = null): array
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
				return [];
			}

			// Template found from the root (folder cannot contain more than one template)
			return ['' => $data];
		}

		$templateDirs = Folder::folders($templateBaseDir);
		$rows         = [];

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
	 * @param   string  $templateDir      template dir
	 * @param   string  $templateBaseDir  template basedir
	 *
	 * @return  boolean|stdClass
	 *
	 * @since   Kunena 6.0
	 */
	public static function parseXmlFile(string $templateDir, $templateBaseDir = null)
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
	 * @param   string  $path  path
	 *
	 * @return  boolean|stdClass
	 *
	 * @since   Kunena 6.0
	 */
	public static function parseKunenaInstallFile(string $path)
	{
		$xml = simplexml_load_file($path);

		if (!$xml || $xml->getName() != 'kinstall')
		{
			return false;
		}

		$data               = new stdClass;
		$data->name         = (string) $xml->name;

		if ($xml->targetversion->attributes() !== null)
		{
			$data->targetversion = (string) $xml->targetversion->attributes()->version;
		}

		$data->type         = (string) $xml->attributes()->type;
		$data->creationdate = (string) $xml->creationDate;
		$data->author       = (string) $xml->author;
		$data->copyright    = (string) $xml->copyright;
		$data->authorEmail  = (string) $xml->authorEmail;
		$data->authorUrl    = (string) $xml->authorUrl;
		$data->version      = (string) $xml->version;
		$data->description  = (string) $xml->description;
		$data->thumbnail    = (string) $xml->thumbnail;
		$data->kversion     = (string) $xml->attributes()->version;

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
			$data->version = Text::_('Unknown');
		}

		if (!$data->creationdate)
		{
			$data->creationdate = Text::_('Unknown');
		}

		if (!$data->author)
		{
			$data->author = Text::_('Unknown');
		}

		return $data;
	}

	/**
	 * Check with the field targetversion in xml file of template zip if it's compatible with kunena
	 *
	 * @param   string $targetversion The versions of Kunena compatible with the template
	 *
	 * @return boolean
	 * @since Kunena 5.2
	 */
	public static function templateIsKunenaCompatible($targetversion = null)
	{
		if ($targetversion === null)
		{
			return true;
		}

		// Get the Kunena version family (e.g. 6.0)
		$kVersion = KunenaForum::version();
		$kVersionParts = explode('.', $kVersion);
		$kVersionShort = $kVersionParts[0] . '.' . $kVersionParts[1];

		$targetKunenaVersion = $targetversion;
		$targetVersionParts = explode('.', $targetKunenaVersion);
		$targetVersionShort = $targetVersionParts[0] . '.' . $targetVersionParts[1];

		// The target version MUST be in the same Kunena branch
		if ($kVersionShort == $targetVersionShort)
		{
			return false;
		}

		// If the target version is major.minor.revision we must make sure our current Kunena version is AT LEAST equal to that.
		if (version_compare($targetKunenaVersion, KunenaForum::version(), 'gt'))
		{
			return false;
		}

		return true;
	}
}
