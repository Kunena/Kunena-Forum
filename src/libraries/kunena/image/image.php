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
		$testForJpg = @getimagesize($filename);

		if ($testForJpg[2] == 2)
		{
			if (function_exists('exif_read_data'))
			{
				$deg  = 0;
				$exif = @exif_read_data($filename);
				$flip = '';
				$img  = '';

				if ($exif && isset($exif['Orientation']))
				{
					$orientation = $exif['Orientation'];

					if ($orientation != 1)
					{
						$img = @imagecreatefromjpeg($filename);

						switch ($orientation)
						{
							case 1: // Nothing
								$deg  = 0;
								$flip = 0;
								break;

							case 2: // Horizontal flip
								$deg  = 0;
								$flip = 1;
								break;

							case 3: // 180 rotate left
								$deg  = 180;
								$flip = 0;
								break;

							case 4: // Vertical flip
								$deg  = 0;
								$flip = 2;
								break;

							case 5: // Vertical flip + 90 rotate
								$deg  = 90;
								$flip = 2;
								break;

							case 6: // 270 rotate left
								$deg  = 270;
								$flip = 0;
								break;

							case 7: // Horizontal flip + 90 rotate
								$deg  = 90;
								$flip = 1;
								break;

							case 8: // 90 rotate left
								$deg  = 90;
								$flip = 0;
								break;
						}
					}
				}

				if ($deg > 0)
				{
					$img = @imagerotate($img, $deg, 0);
				}

				if ($flip != 0)
				{
					if ($flip == 1)
					{
						@imageflip($img, IMG_FLIP_HORIZONTAL);
					}
					else
					{
						@imageflip($img, IMG_FLIP_VERTICAL);
					}
				}

				@imagejpeg($img, $filename, 95);
			}
		}
	}	
}
