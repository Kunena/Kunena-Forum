<?php
/**
 * Kunena Component
 * @package         Kunena.Framework
 * @subpackage      Image
 *
 * @copyright       Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

define('MIME_GIF', 'image/gif');
define('MIME_PNG', 'image/png');

use Joomla\Image\Image;

/**
 * Helper class for image manipulation.
 * @since Kunena
 */
class KunenaImage extends Joomla\Image\Image
{
	/**
	 * Correct Image Orientation
	 *
	 * @since  K5.0
	 *
	 * @param   string $filename filename
	 *
	 * @return void
	 */
	public static function correctImageOrientation($filename)
	{
		// TODO: need to check here if the file given is right an image ?

		if (function_exists('exif_read_data'))
		{
			$angle  = 0;
			$exif   = @exif_read_data($filename);
			$flip   = '';
			$img    = '';

			if ($exif && isset($exif['Orientation']))
			{
				$orientation = $exif['Orientation'];

				if ($orientation != 1)
				{
					$img = new Image();
					$img->loadFile($filename);

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
			
			$img->toFile($filename);
		}
	}
}
