<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Folder
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Folder;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;

/**
 * Class KunenaFolder
 *
 * @since   Kunena 6.0
 */
class KunenaFolder
{
	/**
	 * Create index.html file into the given folder, if it doesn't exist.
	 *
	 * @param   string  $folder  folder
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 * @throws \Exception
	 */
	public static function createIndex(string $folder): void
	{
		// Make sure we have an index.html file in the current folder
		if (!File::exists($folder . '/index.html'))
		{
			$lang = Factory::getApplication()->getLanguage();
			$contents = '<html lang="' . str_replace('_', '-', $lang->getLocale()[2]) . '"><body></body></html>';
			File::write($folder . '/index.html', $contents);
		}
	}
}
