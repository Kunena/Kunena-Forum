<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Folder
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport('joomla.filesystem.folder');

/**
 * Class KunenaFolder
 *
 * @see JFolder
 */
class KunenaFolder extends JFolder
{
	/**
	 * Create index.html file into the given folder, if it doesn't exist.
	 *
	 * @param $folder
	 */
	static function createIndex($folder) {
		// Make sure we have an index.html file in the current folder
		if (!KunenaFile::exists($folder.'/index.html')) {
			$contents = '<html><body></body></html>';
			KunenaFile::write($folder.'/index.html', $contents);
		}
	}
}
