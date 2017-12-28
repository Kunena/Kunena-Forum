<?php
/**
 * Kunena Component
 * @package     Kunena.Framework
 * @subpackage  Image
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Helper class for image manipulation.
 */
class KunenaImageHelper
{
	/**
	 * Create new re-sized version of the original image.
	 *
	 * @param   string  $file        Incoming file
	 * @param   string  $folder      Folder for the new image.
	 * @param   string  $filename    Filename for the new image.
	 * @param   int     $maxWidth    Maximum width for the image.
	 * @param   int     $maxHeight   Maximum height for the image.
	 * @param   int     $quality     Quality for the file (1-100).
	 * @param   int     $scale       See available KunenaImage constants.
	 * @param   int     $crop        Define if you want crop the image.
	 *
	 * @return bool    True on success.
	 */
	public static function version($file, $folder, $filename, $maxWidth = 800, $maxHeight = 800, $quality = 70, $scale = KunenaImage::SCALE_INSIDE, $crop = 0)
	{
		try
		{
			// Create target directory if it does not exist.
			if (!KunenaFolder::exists($folder) && !KunenaFolder::create($folder))
			{
				return false;
			}

			// Make sure that index.html exists in the folder.
			KunenaFolder::createIndex($folder);

			$info = KunenaImage::getImageFileProperties($file);

			if ($info->width > $maxWidth || $info->height > $maxHeight)
			{
				// Make sure that quality is in allowed range.
				if ($quality < 1 || $quality > 100)
				{
					$quality = 70;
				}

				// Calculate quality for PNG.
				if ($info->type == IMAGETYPE_PNG)
				{
					$quality = intval(($quality - 1) / 10);
				}

				$options = array('quality' => $quality);

				// Resize image and copy it to temporary file.
				$image = new KunenaImage($file);

				if ($crop && $info->width > $info->height)
				{
					$image = $image->resize($info->width * $maxHeight / $info->height, $maxHeight, false, $scale);
					$image = $image->crop($maxWidth, $maxHeight);
				}
				elseif ($crop && $info->width < $info->height)
				{
					$image = $image->resize($maxWidth, $info->height * $maxWidth / $info->width, false, $scale);
					$image = $image->crop($maxWidth, $maxHeight);
				}
				else
				{
					$image = $image->resize($maxWidth, $maxHeight, false, $scale);
				}

				$temp = KunenaPath::tmpdir() . '/kunena_' . md5(rand());
				$image->toFile($temp, $info->type, $options);
				unset($image);

				// Move new file to its proper location.
				if (!KunenaFile::move($temp, "{$folder}/{$filename}"))
				{
					unlink($temp);

					return false;
				}
			}
			else
			{
				// Copy original file to the new location.
				if (!KunenaFile::copy($file, "{$folder}/{$filename}"))
				{
					return false;
				}
			}
		}
		catch (Exception $e)
		{
			return false;
		}

		return true;
	}
}
