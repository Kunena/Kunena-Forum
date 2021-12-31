<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      File
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\File;

\defined('_JEXEC') or die;

/**
 * Class KunenaFile
 *
 * @since   Kunena 6.0
 */
class KunenaFile
{
	/**
	 * @param   string  $file  file
	 *
	 * @return  false|string
	 *
	 * @since   Kunena 6.0
	 */
	public static function getMime(string $file)
	{
		if (\function_exists('finfo_open'))
		{
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$type  = finfo_file($finfo, $file);
			finfo_close($finfo);
		}
		elseif (\function_exists('mime_content_type'))
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
