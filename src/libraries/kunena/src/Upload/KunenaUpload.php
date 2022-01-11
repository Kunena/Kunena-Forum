<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Upload
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Upload;

\defined('_JEXEC') or die;

use Exception;
use finfo;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\String\StringHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\File\KunenaFile;
use Kunena\Forum\Libraries\Image\KunenaImage;
use Kunena\Forum\Libraries\Path\KunenaPath;
use RuntimeException;
use StdClass;

/**
 * Class to handle file uploads.
 *
 * @since   Kunena 4.0
 */
class KunenaUpload
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $validExtensions = [];

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $filename;

	/**
	 * Get new instance of upload class.
	 *
	 * @param   array  $extensions  List of allowed file extensions.
	 *
	 * @return  KunenaUpload
	 *
	 * @since   Kunena 6.0
	 */
	public static function getInstance(array $extensions = []): KunenaUpload
	{
		$instance = new self;

		if ($extensions)
		{
			$instance->addExtensions($extensions);
		}

		return $instance;
	}

	/**
	 * Add file extensions to allowed list.
	 *
	 * @param   array  $extensions  List of file extensions, supported values are like: zip, .zip, tar.gz, .tar.gz.
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function addExtensions(array $extensions): KunenaUpload
	{
		foreach ($extensions as $ext)
		{
			$ext = trim((string) $ext, ". \t\n\r\0\x0B");

			if (!$ext)
			{
				continue;
			}

			$ext                         = '.' . $ext;
			$this->validExtensions[$ext] = $ext;
		}

		return $this;
	}

	/**
	 * Upload a file via AJAX, supports chunks and fallback to regular file upload.
	 *
	 * @param   array  $options  Upload options.
	 *
	 * @return  array Updated options.
	 *
	 * @throws  null
	 * @since   Kunena 6.0
	 */
	public function ajaxUpload(array $options): array
	{
		static $defaults = [
			'completed'  => false,
			'filename'   => null,
			'size'       => 0,
			'mime'       => null,
			'hash'       => null,
			'chunkStart' => 0,
			'chunkEnd'   => 0,
			'image_type' => null,
		];

		$options += $defaults;

		$exception = null;
		$in        = null;
		$out       = null;
		$size      = 0;
		$outFile   = null;
		$type      = $options['mime'];

		// Look for the content type header
		if (isset($_SERVER['HTTP_CONTENT_TYPE']))
		{
			$contentType = $_SERVER['HTTP_CONTENT_TYPE'];
		}
		elseif (isset($_SERVER['CONTENT_TYPE']))
		{
			$contentType = $_SERVER['CONTENT_TYPE'];
		}
		else
		{
			$contentType = '';
		}

		try
		{
			// Set filename for future queries.
			$this->filename = $options['filename'];

			$folder = $this->getFolder();

			// Create target directory if it does not exist.
			if (!Folder::exists($folder) && !Folder::create($folder))
			{
				throw new RuntimeException(Text::_('Failed to create upload directory.'), 500);
			}

			// Calculate temporary filename.
			$outFile = $this->getProtectedFile();

			if ($options['chunkEnd'] > $options['size'] || $options['chunkStart'] > $options['chunkEnd'])
			{
				throw new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_EXTRA_CHUNK'), 400);
			}

			if (strpos($contentType, 'multipart') !== false)
			{
				// Older WebKit browsers didn't support multi-part in HTML5.
				$exception = $this->checkUpload($_FILES['file']);

				if ($exception)
				{
					throw $exception;
				}

				$in = fopen($_FILES['file']['tmp_name'], 'rb');
			}
			else
			{
				// Multi-part upload.
				$in = fopen('php://input', 'rb');
			}

			if (!$in)
			{
				throw new RuntimeException(Text::_('Failed to open upload input stream.'), 500);
			}

			// Open temporary file.
			$out = fopen($outFile, !$options['chunkStart'] ? 'wb' : 'r+b');

			if (!$out)
			{
				throw new RuntimeException(Text::_('Failed to open upload output stream.'), 500);
			}

			// Get current size for the file.
			$stat = fstat($out);

			if (!$stat)
			{
				throw new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_STAT', $options['filename']), 500);
			}

			$size = $stat['size'];

			if ($options['chunkStart'] > $size)
			{
				throw new RuntimeException(Text::sprintf('Missing data chunk at location %d.', $size), 500);
			}

			fseek($out, $options['chunkStart']);

			while (!feof($in))
			{
				// Set script execution time to 8 seconds in order to interrupt stalled file transfers (< 1kb/sec).
				// Not sure if it works, though, needs some testing. :)
				@set_time_limit(8);

				$buff = fread($in, 8192);

				if ($buff === false)
				{
					throw new RuntimeException(Text::_('Failed to read from upload input stream.'), 500);
				}

				$bytes = fwrite($out, $buff);

				if ($bytes === false)
				{
					throw new RuntimeException(Text::_('Failed to write into upload output stream.'), 500);
				}

				$size += $bytes;

				if ($options['image_type'] == 'avatar')
				{
					if (!$this->checkFileSizeAvatar($size))
					{
						throw new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_AVATAR_EXCEED_LIMIT_IN_CONFIGURATION'), 500);
					}
				}
				else
				{
					if (stripos($type, 'image/') === false && stripos($type, 'image/') <= 0 && stripos($type, 'audio/') === false && stripos($type, 'video/') === false)
					{
						if (!$this->checkFileSizeFileAttachment($size))
						{
							throw new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_FILE_EXCEED_LIMIT_IN_CONFIGURATION'), 500);
						}
					}

					if (stripos($type, 'image/') !== false && stripos($type, 'image/') >= 0)
					{
						if (!$this->checkFileSizeImageAttachment($size))
						{
							throw new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_IMAGE_EXCEED_LIMIT_IN_CONFIGURATION'), 500);
						}
					}
				}
			}

			if (stripos($type, 'image/') !== false && stripos($type, 'image/') >= 0)
			{
				// Get filename from stream
				$meta_data = stream_get_meta_data($out);
				$filename  = $meta_data['uri'];
				KunenaImage::correctImageOrientation($filename);
			}
		}
		catch (Exception $exception)
		{
		}

		// Reset script execution time.
		@set_time_limit(25);

		if ($in)
		{
			fclose($in);
		}

		if ($out)
		{
			fclose($out);
		}

		if ($exception instanceof Exception)
		{
			$this->cleanup();

			throw $exception;
		}

		// Generate response.
		if ((\is_null($options['size']) && $size) || $size === $options['size'])
		{
			$options['size']      = (int) $size;
			$options['completed'] = true;
		}

		$options['chunkStart'] = (int) $size;

		if ($options['completed'])
		{
			$options['mime'] = KunenaFile::getMime($outFile);
			$options['hash'] = md5_file($outFile);
		}
		else
		{
			if ($size)
			{
				$options['mime'] = KunenaFile::getMime($outFile);
			}
		}

		return $options;
	}

	/**
	 * Get upload folder.
	 *
	 * @return  string  Absolute path.
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function getFolder(): string
	{
		$dir = KunenaPath::tmpdir();

		return "{$dir}/uploads";
	}

	/**
	 * @param   string  $filename  Original filename.
	 *
	 * @return  string  Path pointing to the protected file.
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getProtectedFile($filename = null): string
	{
		$filename = $filename ? $filename : $this->filename;

		return $this->getFolder() . '/' . $this->getProtectedFilename($filename);
	}

	/**
	 * @param   string  $filename  Original filename.
	 *
	 * @return  string  Protected filename.
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getProtectedFilename($filename = null): string
	{
		$filename = $filename ? $filename : $this->filename;

		$user    = Factory::getApplication()->getIdentity();
		$session = Factory::getSession();
		$token   = Factory::getApplication()->get('secret') . $user->get('id', 0) . $session->getToken();
		list($name, $ext) = $this->splitFilename($filename);

		return md5("{$name}.{$token}.{$ext}");
	}

	/**
	 * Split filename by valid extension.
	 *
	 * @param   string  $filename  Name of the file.
	 *
	 * @return  array  File parts: list($name, $extension).
	 *
	 * @throws  RuntimeException
	 * @since   Kunena 6.0
	 */
	public function splitFilename($filename = null): array
	{
		$filename = $filename ? $filename : $this->filename;

		if (!$filename)
		{
			throw new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_NO_FILE'), 400);
		}

		// Check if file extension matches any allowed extensions (case insensitive)
		foreach ($this->validExtensions as $ext)
		{
			$extension = StringHelper::substr($filename, -StringHelper::strlen($ext));

			if (StringHelper::strtolower($extension) == StringHelper::strtolower($ext))
			{
				// File must contain one letter before extension
				$name      = StringHelper::substr($filename, 0, -StringHelper::strlen($ext));
				$extension = StringHelper::substr($extension, 1);

				if (!$name)
				{
					break;
				}

				return [$name, $extension];
			}
		}

		throw new RuntimeException(
			Text::sprintf('COM_KUNENA_UPLOAD_ERROR_EXTENSION_FILE', implode(', ', $this->validExtensions)),
			400
		);
	}

	/**
	 * Check for upload errors.
	 *
	 * @param   array  $file  Entry from $_FILES array.
	 *
	 * @return \RuntimeException|null
	 *
	 * @since   Kunena 6.0
	 */
	protected function checkUpload(array $file): ?RuntimeException
	{
		$exception = null;

		switch ($file['error'])
		{
			case UPLOAD_ERR_OK :
				break;

			case UPLOAD_ERR_INI_SIZE :
			case UPLOAD_ERR_FORM_SIZE :
				$exception = new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_SIZE'), 400);
				break;

			case UPLOAD_ERR_PARTIAL :
				$exception = new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_PARTIAL'), 400);
				break;

			case UPLOAD_ERR_NO_FILE :
				$exception = new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_NO_FILE'), 400);
				break;

			case UPLOAD_ERR_NO_TMP_DIR :
				$exception = new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_NO_TMP_DIR'), 500);
				break;

			case UPLOAD_ERR_CANT_WRITE :
				$exception = new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_CANT_WRITE'), 500);
				break;

			case UPLOAD_ERR_EXTENSION :
				$exception = new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_PHP_EXTENSION'), 500);
				break;

			default :
				$exception = new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_UNKNOWN'), 500);
		}

		if (!$exception && (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])))
		{
			$exception = new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_NOT_UPLOADED'), 400);
		}

		return $exception;
	}

	/**
	 * Check if filesize on avatar which on going to be uploaded doesn't exceed the limits set by Kunena configuration
	 * and Php configuration
	 *
	 * @param   int  $fileSize  filesize
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	protected function checkFileSizeAvatar(int $fileSize)
	{
		if ($fileSize > \intval(KunenaConfig::getInstance()->avatarSize * 1024))
		{
			return false;
		}

		return (int) max(
			0,
			min(
				$this->toBytes(ini_get('upload_max_filesize')),
				$this->toBytes(ini_get('post_max_size')),
				$this->toBytes(ini_get('memory_limit'))
			)
		);
	}

	/**
	 * Convert value into bytes.
	 *
	 * @param   string  $value  Value, for example: 1G, 10M, 120k...
	 *
	 * @return  float  Value in bytes.
	 *
	 * @since   Kunena 6.0
	 */
	public static function toBytes(string $value)
	{
		$base = log((int) $value, 1024);

		return round(pow(1024, $base - floor($base)));
	}

	/**
	 * Check if fileSize on file which on going to be uploaded doesn't exceed the limits set by Kunena configuration
	 * and PHP configuration
	 *
	 * @param   int  $fileSize  The size of file in bytes
	 *
	 * @return  integer
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	protected function checkFileSizeFileAttachment(int $fileSize)
	{
		$file = $fileSize > KunenaConfig::getInstance()->fileSize * 1024;

		if ($file)
		{
			return false;
		}

		return (int) max(
			0,
			min(
				$this->toBytes(ini_get('upload_max_filesize')),
				$this->toBytes(ini_get('post_max_size')),
				$this->toBytes(ini_get('memory_limit'))
			)
		);
	}

	/**
	 * Check if filesize on image file which on going to be uploaded doesn't exceed the limits set by Kunena
	 * configuration and PHP configuration
	 *
	 * @param   int  $filesize  The size of file in bytes
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	protected function checkFileSizeImageAttachment($filesize)
	{
		$image = $filesize > \intval(KunenaConfig::getInstance()->imageSize * 1024);

		if ($image)
		{
			return false;
		}

		return (int) max(
			0,
			min(
				$this->toBytes(ini_get('upload_max_filesize')),
				$this->toBytes(ini_get('post_max_size')),
				$this->toBytes(ini_get('memory_limit'))
			)
		);
	}

	/**
	 * Clean up temporary file if it exists.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function cleanup(): void
	{
		if (!$this->filename || !is_file($this->filename))
		{
			return;
		}

		@unlink($this->filename);
	}

	/**
	 * Upload file by passing it by HTML input
	 *
	 * @param   array   $fileInput    The file object returned by Joomla\CMS\Input\Input
	 * @param   string  $destination  The path of destination of file uploaded
	 * @param   string  $type         The type of file uploaded: attachment or avatar
	 *
	 * @return  object
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function upload(array $fileInput, string $destination, $type = 'attachment')
	{
		$file       = new stdClass;
		$file->ext  = File::getExt($fileInput['name']);
		$file->ext  = strtolower($file->ext);
		$file->size = $fileInput['size'];
		$config     = KunenaFactory::getConfig();

		if ($type != 'attachment' && $config->attachmentUtf8)
		{
			$file->tmp_name = $fileInput['tmp_name'];
		}
		else
		{
			$pathInfo       = pathinfo($fileInput['tmp_name']);
			$file->tmp_name = $pathInfo['dirname'] . '/' . File::makeSafe($pathInfo['basename']);
		}

		$file->error       = $fileInput['error'];
		$file->destination = $destination . '.' . $file->ext;
		$file->success     = false;
		$file->isAvatar    = false;

		if ($type == 'avatar')
		{
			$file->isAvatar = true;
		}

		if ($file->isAvatar)
		{
			if (!$this->checkFileSizeImage($file->size, 'avatar'))
			{
				throw new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_AVATAR_EXCEED_LIMIT_IN_CONFIGURATION'), 500);
			}

			$avatarTypes = strtolower(KunenaConfig::getInstance()->avatarTypes);
			$a           = explode(', ', $avatarTypes);

			if (!\in_array($file->ext, $a, true))
			{
				throw new RuntimeException(Text::sprintf('COM_KUNENA_UPLOAD_ERROR_EXTENSION_FILE', implode(', ', $a)), 500);
			}
		}

		if (!is_uploaded_file($file->tmp_name))
		{
			$exception = $this->checkUpload($fileInput);

			if ($exception)
			{
				throw $exception;
			}
		}
		elseif ($file->error != 0)
		{
			throw new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_NOT_UPLOADED'), 500);
		}

		// Check if file extension matches any allowed extensions (case insensitive)
		foreach ($this->validExtensions as $ext)
		{
			$extension = StringHelper::substr($file->tmp_name, -StringHelper::strlen($ext));

			if (StringHelper::strtolower($extension) == StringHelper::strtolower($ext))
			{
				// File must contain one letter before extension
				$name      = StringHelper::substr($file->tmp_name, 0, -StringHelper::strlen($ext));

				if (!$name)
				{
					throw new RuntimeException(
						Text::sprintf('COM_KUNENA_UPLOAD_ERROR_EXTENSION_FILE', implode(', ', $this->validExtensions)),
						400
					);
				}
			}

			if (\extension_loaded('fileinfo'))
			{
				$finfo = new finfo(FILEINFO_MIME);
				$type  = $finfo->file($file->tmp_name);
			}
			else
			{
				throw new RuntimeException("Fileinfo extension not loaded.");
			}

			if (!$file->isAvatar && stripos($type, 'image/') !== false)
			{
				if (!$this->checkFileSizeImageAttachment($file->size))
				{
					throw new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_IMAGE_EXCEED_LIMIT_IN_CONFIGURATION'), 500);
				}
			}

			if (!$file->isAvatar && stripos($type, 'image/') !== true)
			{
				if (!$this->checkFileSizeFileAttachment($file->size))
				{
					throw new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_FILE_EXCEED_LIMIT_IN_CONFIGURATION'), 500);
				}
			}
		}

		KunenaImage::correctImageOrientation($file->tmp_name);

		if (!File::copy($file->tmp_name, $file->destination))
		{
			throw new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_FILE_RIGHT_MEDIA_DIR'), 500);
		}

		unlink($file->tmp_name);

		KunenaPath::setPermissions($file->destination);

		$file->success = true;

		return $file;
	}

	/**
	 * Check if fileSize on image file which on going to be uploaded doesn't exceed the limits set by Kunena
	 * configuration and PHP configuration
	 *
	 * @param   int     $fileSize    The size of file in bytes
	 * @param   string  $image_type  The type of image : avatar or attachment image
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	protected function checkFileSizeImage(int $fileSize, string $image_type)
	{
		if ($image_type == 'avatar')
		{
			$config_size = KunenaConfig::getInstance()->avatarSize;
		}
		else
		{
			$config_size = KunenaConfig::getInstance()->imageSize;
		}

		$image = $fileSize > \intval($config_size * 1024);

		if ($image)
		{
			return false;
		}

		return (int) max(
			0,
			min(
				$this->toBytes(ini_get('upload_max_fileSize')),
				$this->toBytes(ini_get('post_max_size')),
				$this->toBytes(ini_get('memory_limit'))
			)
		);
	}

	/**
	 * Convert into human readable format bytes to kB, MB, GB
	 *
	 * @param   integer  $bytes       size in bytes
	 * @param   null     $force_unit  a definitive unit
	 * @param   null     $format      the return string format
	 * @param   boolean  $si          whether to use SI prefixes or IEC
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function bytes(int $bytes, $force_unit = null, $format = null, $si = true): string
	{
		// Format string
		$format = ($format === null) ? '%01.2f %s' : (string) $format;

		$units = [Text::_('COM_KUNENA_UPLOAD_ERROR_FILE_WEIGHT_BYTES'),
			Text::_('COM_KUNENA_UPLOAD_ERROR_FILE_WEIGHT_KB'),
			Text::_('COM_KUNENA_UPLOAD_ERROR_FILE_WEIGHT_MB'),
			Text::_('COM_KUNENA_UPLOAD_ERROR_FILE_WEIGHT_GB'),
		];
		$mod   = 1024;

		// Determine unit to use
		if (($power = array_search((string) $force_unit, $units)) === false)
		{
			$power = ($bytes > 0) ? floor(log($bytes, $mod)) : 0;
		}

		return sprintf($format, $bytes / pow($mod, $power), $units[$power]);
	}
}
