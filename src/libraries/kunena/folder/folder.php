<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Folder
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

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
	 * @return void
	 * @since   Kunena 6.0
	 */
	public static function createIndex($folder)
	{
		// Make sure we have an index.html file in the current folder
		if (!File::exists($folder . '/index.html'))
		{
			$contents = '<html><body></body></html>';
			File::write($folder . '/index.html', $contents);
		}
	}
}
