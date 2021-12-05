<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Image
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Image;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Image\Image;
use RuntimeException;

/**
 * Helper class for image manipulation.
 *
 * @since   Kunena 6.0
 */
class KunenaImage extends Image
{
	/**
	 * Correct Image Orientation
	 *
	 * @param   string  $filename  filename
	 *
	 * @return  void
	 *
	 * @since   Kunena 5.0
	 */
	public static function correctImageOrientation($filename)
	{
		$img = new Image;

		try
		{
			$img->loadFile($filename);
		}
		catch (Exception $e)
		{
			throw new RuntimeException($e->getMessage(), 500);
		}

		$angle  = 0;
		$flip   = 0;

		$exif = exif_read_data($filename);

		if ($exif && isset($exif['Orientation']))
		{
			$orientation = $exif['Orientation'];

			if ($orientation != 1)
			{

				switch ($orientation)
				{
					case 1: // Nothing
						$angle  = 0;
						$flip = 0;
						break;

					case 2: // Horizontal flip
						$angle  = 0;
						$flip = 1;
						break;

					case 3: // 180 rotate left
						$angle  = 180;
						$flip = 0;
						break;

					case 4: // Vertical flip
						$angle  = 0;
						$flip = 2;
						break;

					case 5: // Vertical flip + 90 rotate
						$angle  = 90;
						$flip = 2;
						break;

					case 6: // 270 rotate left
						$angle  = 270;
						$flip = 0;
						break;

					case 7: // Horizontal flip + 90 rotate
						$angle  = 90;
						$flip = 1;
						break;

					case 8: // 90 rotate left
						$angle  = 90;
						$flip = 0;
						break;
				}
			}
		}

		if ($angle > 0)
		{
			$img->rotate($angle, -1, false);
		}

		if ($flip != 0)
		{
			if ($flip == 1)
			{
				$img->flip(IMG_FLIP_HORIZONTAL, false);
			}
			else
			{
				$img->flip(IMG_FLIP_VERTICAL, false);
			}
		}
	}
}
