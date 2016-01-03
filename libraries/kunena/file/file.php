<?php
/**
 * Kunena Component
 * @package     Kunena.Framework
 * @subpackage  File
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

/**
 * Class KunenaFile
 *
 * @see JFile
 */
class KunenaFile extends JFile
{
	public static function getMime($file)
	{
		// Check if file is an image.
		$info = @getimagesize($file);

		if ($info)
		{
			$type = $info['mime'];
		}
		elseif (function_exists('finfo_open'))
		{
			// We have fileinfo.
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$type  = finfo_file($finfo, $file);
			finfo_close($finfo);
		}
		elseif (function_exists('mime_content_type'))
		{
			// We have mime magic.
			$type = mime_content_type($file);
		}
		else
		{
			$type = false;
		}

		return $type;
	}
}
