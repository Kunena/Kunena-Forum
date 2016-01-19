<?php
/**
 * Kunena Component
 * @package     Kunena.Framework
 * @subpackage  Upload
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class to handle file uploads.
 *
 * @since  K4.0
 */
class KunenaUpload
{
	protected $validExtensions = array();

	protected $filename;

	/**
	 * Get new instance of upload class.
	 *
	 * @param  array  $extensions  List of allowed file extensions.
	 *
	 * @return KunenaUpload
	 */
	public static function getInstance(array $extensions = array())
	{
		$instance = new KunenaUpload;

		if ($extensions)
		{
			$instance->addExtensions($extensions);
		}

		return $instance;
	}

	/**
	 * Add file extensions to allowed list.
	 *
	 * @param array $extensions  List of file extensions, supported values are like: zip, .zip, tar.gz, .tar.gz.
	 *
	 * @return $this
	 */
	public function addExtensions(array $extensions)
	{
		foreach ($extensions as $ext)
		{
			$ext = trim((string) $ext, ". \t\n\r\0\x0B");

			if (!$ext)
			{
				continue;
			}

			$ext = '.' . $ext;
			$this->validExtensions[$ext] = $ext;
		}

		return $this;
	}

	/**
	 * Split filename by valid extension.
	 *
	 * @param  string  $filename  Name of the file.
	 *
	 * @return array  File parts: list($name, $extension).
	 * @throws RuntimeException
	 */
	public function splitFilename($filename = null)
	{
		$filename = $filename ? $filename : $this->filename;

		if (!$filename)
		{
			throw new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_NO_FILE'), 400);
		}

		// Check if file extension matches any allowed extensions (case insensitive)
		foreach ($this->validExtensions as $ext)
		{
			$extension = JString::substr($filename, -JString::strlen($ext));

			if (JString::strtolower($extension) == JString::strtolower($ext))
			{
				// File must contain one letter before extension
				$name = JString::substr($filename, 0, -JString::strlen($ext));
				$extension = JString::substr($extension, 1);

				if (!$name)
				{
					break;
				}

				return array($name, $extension);
			}
		}

		throw new RuntimeException(
			JText::sprintf('COM_KUNENA_UPLOAD_ERROR_EXTENSION_FILE', implode(', ', $this->validExtensions)),
			400
		);
	}

	/**
	 * @param  string  $filename  Original filename.
	 *
	 * @return string  Path pointing to the protected file.
	 */
	public function getProtectedFile($filename = null)
	{
		$filename = $filename ? $filename : $this->filename;

		return $this->getFolder() . '/' . $this->getProtectedFilename($filename);
	}

	/**
	 * @param  string  $filename  Original filename.
	 *
	 * @return string     Protected filename.
	 */
	public function getProtectedFilename($filename = null)
	{
		$filename = $filename ? $filename : $this->filename;

		$user = JFactory::getUser();
		$session = JFactory::getSession();
		$token = JFactory::getConfig()->get('secret') . $user->get('id', 0) . $session->getToken();
		list($name, $ext) = $this->splitFilename($filename);

		return md5("{$name}.{$token}.{$ext}");
	}

	/**
	 * Get upload folder.
	 *
	 * @return string  Absolute path.
	 */
	public function getFolder()
	{
		$dir = KunenaPath::tmpdir();

		return "{$dir}/uploads";
	}

	/**
	 * Convert value into bytes.
	 *
	 * @param  string  $value  Value, for example: 1G, 10M, 120k...
	 *
	 * @return int  Value in bytes.
	 */
	public static function toBytes($value)
	{
		$value = trim($value);

		if (empty($value))
		{
			return 0;
		}

		preg_match('#([0-9]+)[\s]*([a-z]+)#i', $value, $matches);

		$last = '';
		if (isset($matches[2]))
		{
			$last = $matches[2];
		}

		if (isset($matches[1]))
		{
			$value = (int) $matches[1];
		}

		switch (strtolower($last))
		{
			case 'g':
			case 'gb':
				$value *= 1024;
				// Continue.
			case 'm':
			case 'mb':
				$value *= 1024;
				// Continue.
			case 'k':
			case 'kb':
				$value *= 1024;
		}

		return (int) $value;
	}

	/**
	 * Upload a file via AJAX, supports chunks and fallback to regular file upload.
	 *
	 * @param  array $options Upload options.
	 *
	 * @return array Updated options.
	 * @throws null
	 */
	public function ajaxUpload(array $options)
	{
		static $defaults = array(
			'completed' => false,
			'filename' => null,
			'size' => 0,
			'mime' => null,
			'hash' => null,
			'chunkStart' => 0,
			'chunkEnd' => 0
		);

		$options += $defaults;

		$exception = null;
		$in = null;
		$out = null;
		$size = $bytes = 0;
		$outFile = null;
		$type = $options['mime'];

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
			if (!KunenaFolder::exists($folder) && !KunenaFolder::create($folder))
			{
				throw new RuntimeException(JText::_('Failed to create upload directory.'), 500);
			}

			// Calculate temporary filename.
			$outFile = $this->getProtectedFile();

			if ($options['chunkEnd'] > $options['size'] || $options['chunkStart'] > $options['chunkEnd'])
			{
				throw new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_EXTRA_CHUNK'), 400);
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
				throw new RuntimeException(JText::_('Failed to open upload input stream.'), 500);
			}

			// Open temporary file.
			$out = fopen($outFile, !$options['chunkStart'] ? 'wb' : 'r+b');

			if (!$out)
			{
				throw new RuntimeException(JText::_('Failed to open upload output stream.'), 500);
			}

			// Get current size for the file.
			$stat = fstat($out);

			if (!$stat) {
				throw new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_STAT', $options['filename']), 500);
			}

			$size = $stat['size'];

			if ($options['chunkStart'] > $size) {
				throw new RuntimeException(JText::sprintf('Missing data chunk at location %d.', $size), 500);
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
					throw new RuntimeException(JText::_('Failed to read from upload input stream.'), 500);
				}

				$bytes = fwrite($out, $buff);

				if ($bytes === false)
				{
					throw new RuntimeException(JText::_('Failed to write into upload output stream.'), 500);
				}

				$size += $bytes;

				if (stripos($type, 'image/') !== true)
				{
					if (!$this->checkFileSizeFileAttachment($size))
					{
						throw new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_FILE_EXCEED_LIMIT_IN_CONFIGURATION'), 500);
					}
				}

				if (stripos($type, 'image/') !== false)
				{
					if (!$this->checkFileSizeImageAttachment($size))
					{
						throw new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_IMAGE_EXCEED_LIMIT_IN_CONFIGURATION'), 500);
					}
				}
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
		if ((is_null($options['size']) && $size) || $size === $options['size'])
		{
			$options['size'] = (int) $size;
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
	 * Clean up temporary file if it exists.
	 *
	 * @return void
	 */
	public function cleanup()
	{
		if (!$this->filename || !is_file($this->filename))
		{
			return;
		}

		@unlink($this->filename);
	}

	/**
	 * Return AJAX response in JSON.
	 *
	 * @param mixed $content
	 *
	 * @return string
	 */
	public function ajaxResponse($content)
	{
		// TODO: Joomla 3.1+ uses JResponseJson (we just emulate it for now).
		$response = new StdClass;
		$response->success = true;
		$response->message = null;
		$response->messages = null;
		$response->data = null;

		if ($content instanceof Exception)
		{
			// Build data from exceptions.
			$exceptions = array();
			$e = $content;

			do
			{
				$exception = array(
					'code' => $e->getCode(),
					'message' => $e->getMessage()
				);

				if (JDEBUG)
				{
					$exception += array(
						'type' => get_class($e),
						'file' => $e->getFile(),
						'line' => $e->getLine()
					);
				}

				$exceptions[] = $exception;
				$e = $e->getPrevious();
			}
			while (JDEBUG && $e);

			// Create response.
			$response->success = false;
			$response->message = $content->getcode() . ' ' . $content->getMessage();
			$response->data = array('exceptions' => $exceptions);
		}
		else
		{
			$response->data = (array) $content;
		}

		return json_encode($response);
	}

	/**
	 * Check if filesize on file which on going to be uploaded doesn't exceed the limits set by Kunena configuration and Php configuration
	 *
	 * @param   int      $filesize  The size of file in bytes
	 *
	 * @return boolean
	 */
	protected function checkFileSizeAvatar($filesize)
	{
		if ($filesize > intval(KunenaConfig::getInstance()->avatarsize) * 1024)
		{
			return false;
		}

		return (int) max(
				0,
				min(
						$this->toBytes(ini_get('upload_max_filesize')) - 1024,
						$this->toBytes(ini_get('post_max_size')) - 1024,
						$this->toBytes(ini_get('memory_limit')) - 1024 * 1024
				)
		);
	}

	/**
	 * Check if filesize on file which on going to be uploaded doesn't exceed the limits set by Kunena configuration and PHP configuration
	 *
	 * @param   int      $filesize  The size of file in bytes
	 *
	 * @return boolean
	 */
	protected function checkFileSizeFileAttachment($filesize)
	{
		$file = $filesize > KunenaConfig::getInstance()->filesize * 1024;

		if ($file)
		{
			return false;
		}

		return (int) max(
				0,
				min(
						$this->toBytes(ini_get('upload_max_filesize')) - 1024,
						$this->toBytes(ini_get('post_max_size')) - 1024,
						$this->toBytes(ini_get('memory_limit')) - 1024 * 1024
				)
		);
	}

	/**
	 * Check if filesize on image file which on going to be uploaded doesn't exceed the limits set by Kunena configuration and PHP configuration
	 *
	 * @param   int      $filesize  The size of file in bytes
	 *
	 * @return boolean
	 */
	protected function checkFileSizeImageAttachment($filesize)
	{
		$image = $filesize > intval(KunenaConfig::getInstance()->imagesize * 1024);

		if ($image)
		{
			return false;
		}

		return (int) max(
				0,
				min(
						$this->toBytes(ini_get('upload_max_filesize')) - 1024,
						$this->toBytes(ini_get('post_max_size')) - 1024,
						$this->toBytes(ini_get('memory_limit')) - 1024 * 1024
				)
		);
	}

	/**
	 * Upload file by passing it by HTML input
	 *
	 * @param   array   $fileInput    The file object returned by JInput
	 * @param   string  $destination  The path of destination of file uploaded
	 * @param   string  $type         The type of file uploaded: attachment or avatar
	 *
	 * @return object
	 */
	public function upload($fileInput, $destination, $type = 'attachment')
	{
		$file = new stdClass;
		$file->ext = JFile::getExt($fileInput['name']);
		$file->size = $fileInput['size'];
		$file->tmp_name = $fileInput['tmp_name'];
		$file->error = $fileInput['error'];
		$file->destination = $destination . '.' . $file->ext;
		$file->success = false;
		$file->isAvatar = false;

		if ($type != 'attachment')
		{
			$file->isAvatar = true;
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
			throw new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_NOT_UPLOADED'), 500);
		}

		// Check if file extension matches any allowed extensions (case insensitive)
		foreach ($this->validExtensions as $ext)
		{
			$extension = JString::substr($file->tmp_name, -JString::strlen($ext));

			if (JString::strtolower($extension) == JString::strtolower($ext))
			{
				// File must contain one letter before extension
				$name = JString::substr($file->tmp_name, 0, -JString::strlen($ext));
				$extension = JString::substr($extension, 1);

				if (!$name)
				{
					throw new RuntimeException(
						JText::sprintf('COM_KUNENA_UPLOAD_ERROR_EXTENSION_FILE', implode(', ', $this->validExtensions)),
						400
					);
				}
			}

			if (extension_loaded('fileinfo'))
			{
				$finfo = new finfo(FILEINFO_MIME);
				$type = $finfo->file($file->tmp_name);
			}
			else
			{
				$info = getimagesize($file->tmp_name);
				$type = $info['mime'];
			}

			if ($file->isAvatar)
			{
				if (!$this->checkFileSizeAvatar($file->size))
				{
					throw new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_AVATAR_EXCEED_LIMIT_IN_CONFIGURATION'), 500);
				}
			}

			if (!$file->isAvatar && stripos($type, 'image/') !== false)
			{
				if (!$this->checkFileSizeImageAttachment($file->size))
				{
					throw new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_IMAGE_EXCEED_LIMIT_IN_CONFIGURATION'), 500);
				}
			}

			if (!$file->isAvatar && stripos($type, 'image/') !== true)
			{
				if (!$this->checkFileSizeFileAttachment($file->size))
				{
					throw new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_FILE_EXCEED_LIMIT_IN_CONFIGURATION'), 500);
				}
			}
		}


		if (!KunenaFile::copy($file->tmp_name, $file->destination))
		{
			throw new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_FILE_RIGHT_MEDIA_DIR'), 500);
		}

		unlink($file->tmp_name);

		KunenaPath::setPermissions($file->destination);

		$file->success = true;

		return $file;
	}

	/**
	 * Check for upload errors.
	 *
	 * @param  array  $file  Entry from $_FILES array.
	 *
	 * @return RuntimeException
	 */
	protected function checkUpload($file)
	{
		$exception = null;

		switch ($file['error'])
		{
			case UPLOAD_ERR_OK :
				break;

			case UPLOAD_ERR_INI_SIZE :
			case UPLOAD_ERR_FORM_SIZE :
				$exception = new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_SIZE'), 400);
				break;

			case UPLOAD_ERR_PARTIAL :
				$exception = new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_PARTIAL'), 400);
				break;

			case UPLOAD_ERR_NO_FILE :
				$exception = new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_NO_FILE'), 400);
				break;

			case UPLOAD_ERR_NO_TMP_DIR :
				$exception = new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_NO_TMP_DIR'), 500);
				break;

			case UPLOAD_ERR_CANT_WRITE :
				$exception = new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_CANT_WRITE'), 500);
				break;

			case UPLOAD_ERR_EXTENSION :
				$exception = new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_PHP_EXTENSION'), 500);
				break;

			default :
				$exception = new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_UNKNOWN'), 500);
		}

		if (!$exception && (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])))
		{
			$exception = new RuntimeException(JText::_('COM_KUNENA_UPLOAD_ERROR_NOT_UPLOADED'), 400);
		}

		return $exception;
	}

	/**
	 * Convert into human readable format bytes to kB, MB, GB
	 *
	 * @param   integer  $bytes       size in bytes
	 * @param   string   $force_unit  a definitive unit
	 * @param   string   $format      the return string format
	 * @param   boolean  $si          whether to use SI prefixes or IEC
	 *
	 * @return string
	 */
	public function bytes($bytes, $force_unit = null, $format = null, $si = true)
	{
		// Format string
		$format = ($format === null) ? '%01.2f %s' : (string) $format;

		$units = array(JText::_('COM_KUNENA_UPLOAD_ERROR_FILE_WEIGHT_BYTES'), JText::_('COM_KUNENA_UPLOAD_ERROR_FILE_WEIGHT_KB'), JText::_('COM_KUNENA_UPLOAD_ERROR_FILE_WEIGHT_MB'), JText::_('COM_KUNENA_UPLOAD_ERROR_FILE_WEIGHT_GB'));
		$mod   = 1024;

		// Determine unit to use
		if (($power = array_search((string) $force_unit, $units)) === false)
		{
			$power = ($bytes > 0) ? floor(log($bytes, $mod)) : 0;
		}

		return sprintf($format, $bytes / pow($mod, $power), $units[$power]);
	}
}
