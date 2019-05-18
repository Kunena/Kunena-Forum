<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Upload
 *
 * @copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;

/**
 * Kunena Upload Backend Helper Class
 * @since Kunena
 */
class KunenaUploadHelper
{
	/**
	 * @var array
	 * @since Kunena
	 */
	protected static $_instances = array();

	/**
	 * @since Kunena
	 */
	private function __construct()
	{
	}

	/**
	 * @param   mixed  $file         file
	 * @param   string $uploadfolder upload
	 * @param   string $format       format
	 *
	 * @return mixed|void
	 * @since Kunena
	 */
	public static function upload($file, $uploadfolder, $format)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_media/helpers/media.php';

		$err = null;

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		Joomla\CMS\Client\ClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		$file['name'] = File::makeSafe($file['name']);

		if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || !empty($file['error']))
		{
			return false;
		}

		if (!Folder::exists($uploadfolder))
		{
			return false;
		}

		if (isset($file['name']))
		{
			$filepath = JPath::clean($uploadfolder . '/' . strtolower($file['name']));

			if (File::exists($filepath))
			{
				if ($format == 'json')
				{
					header('HTTP/1.0 409 Conflict');
					jexit('Error. File already exists');
				}
				else
				{
					$ext         = File::getExt($file['name']);
					$name        = File::stripExt($file['name']);
					$newFileName = '';

					for ($i = 2; file_exists("{$uploadfolder}/{$newFileName}"); $i++)
					{
						$newFileName = $name . "-$i." . $ext;
					}

					$filepath = $uploadfolder . '/' . $newFileName;
				}
			}

			if (!File::upload($file['tmp_name'], $filepath))
			{
				if ($format == 'json')
				{
					header('HTTP/1.0 400 Bad Request');
					jexit('Error. Unable to upload file');
				}
				else
				{
					return false;
				}
			}
			else
			{
				if ($format == 'json')
				{
					jexit('Upload complete');
				}
				else
				{
					return true;
				}
			}
		}
		else
		{
			return false;
		}
	}
}
