<?php
/**
 * Kunena Component
 * @package         Kunena.Framework
 * @subpackage      Forum.Message.Attachment
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Class KunenaAttachment
 *
 * @property int    $id
 * @property int    $userid
 * @property int    $mesid
 * @property int    $protected
 * @property string $hash
 * @property int    $size
 * @property string $folder
 * @property string $filetype
 * @property string $filename
 * @property string $filename_real
 * @property string $caption
 * @property int    $inline
 *
 * @property int    $width   Image width (0 for non-images).
 * @property int    $height  Image height (0 for non-images).
 *
 * @since  K4.0
 */
class KunenaAttachment extends KunenaDatabaseObject
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected static $_directory = 'media/kunena/attachments';

	/**
	 * @var array
	 * @since Kunena
	 */
	protected static $actions = array(
		'read'        => array('Read'),
		'createimage' => array(),
		'createfile'  => array(),
		'delete'      => array('Exists', 'Own'),
	);

	/**
	 * @var integer
	 * @since Kunena
	 */
	public $id = null;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public $disabled = false;

	/**
	 * @var string
	 * @since Kunena
	 */
	protected $_table = 'KunenaAttachments';

	/**
	 * @var string
	 * @since Kunena
	 */
	protected $path;

	/**
	 * @var integer
	 * @since Kunena
	 */
	protected $width;

	/**
	 * @var integer
	 * @since Kunena
	 */
	protected $height;

	/**
	 * @var string
	 * @since Kunena
	 */
	protected $shortname;

	/**
	 * @param   mixed  $identifier  identifier
	 * @param   bool   $reload      reload
	 *
	 * @return KunenaAttachment
	 *
	 * @since  K4.0
	 */
	public static function getInstance($identifier = null, $reload = false)
	{
		return KunenaAttachmentHelper::get($identifier, $reload);
	}

	/**
	 * Destructor deletes the files from the filesystem if attachment isn't stored in database.
	 *
	 * @since  K4.0
	 */
	public function __destruct()
	{
		if (!$this->exists())
		{
			$this->deleteFile();
		}
	}

	/**
	 * @return void
	 * @since  K4.0
	 * @internal
	 */
	protected function deleteFile()
	{
		if (self::$_directory != substr($this->folder, 0, strlen(self::$_directory)))
		{
			return;
		}

		$path     = JPATH_ROOT . "/{$this->folder}";
		$filename = $path . '/' . $this->filename;

		if (is_file($filename))
		{
			KunenaFile::delete($filename);
		}

		$filename = $path . '/raw/' . $this->filename;

		if (is_file($filename))
		{
			KunenaFile::delete($filename);
		}

		$filename = $path . '/thumb/' . $this->filename;

		if (is_file($filename))
		{
			KunenaFile::delete($filename);
		}
	}

	/**
	 * Getter function.
	 *
	 * @param   string  $property  property
	 *
	 * @return integer
	 * @since  K4.0
	 */
	public function __get($property)
	{
		if ($this->width == null)
		{
			$this->initialize();
		}

		switch ($property)
		{
			case 'width':
				return $this->width;
			case 'height':
				return $this->height;
		}

		throw new InvalidArgumentException(sprintf('Property "%s" is not defined', $property));
	}

	/**
	 * @return void
	 * @since  K4.0
	 * @internal
	 */
	protected function initialize()
	{
		$path = $this->getPath();

		if ($path && $this->isImage())
		{
			list($this->width, $this->height) = getimagesize($path);
		}
		else
		{
			$this->width = $this->height = 0;
		}
	}

	/**
	 * Get path for the file.
	 *
	 * @param   bool  $thumb  thumb
	 *
	 * @return string|false  Path to the file or false if file doesn't exist.
	 *
	 * @since  K4.0
	 */
	public function getPath($thumb = false)
	{
		if ($thumb)
		{
			$path = JPATH_ROOT . "/{$this->folder}/thumb/{$this->filename}";
			$path = is_file($path) ? $path : false;
		}
		else
		{
			$path = JPATH_ROOT . "/{$this->folder}/{$this->filename}";
			$path = is_file($path) ? $path : false;
		}

		return $path;
	}

	/**
	 * Check if attachment is image.
	 *
	 * @return  boolean  True if attachment is image.
	 *
	 * @since  K4.0
	 */
	public function isImage()
	{
		return stripos($this->filetype, 'image/') !== false;
	}

	/**
	 * Check if attachment is audio.
	 *
	 * @return  boolean  True if attachment is image.
	 *
	 * @since  K5.1
	 */
	public function isAudio()
	{
		return stripos($this->filetype, 'audio/') !== false;
	}

	/**
	 * Check if attachment is audio.
	 *
	 * @return  boolean  True if attachment is image.
	 *
	 * @since  K5.1
	 */
	public function isVideo()
	{
		return stripos($this->filetype, 'video/') !== false;
	}

	/**
	 * Check if attachment is pdf.
	 *
	 * @return  boolean  True if attachment is pdf.
	 *
	 * @since  K5.1
	 */
	public function isPdf()
	{
		return stripos($this->filetype, 'application/pdf') !== false;
	}

	/**
	 * Check if attachment is inline.
	 *
	 * @return  boolean  True if attachment is inline.
	 *
	 * @since  K5.1.9
	 */
	public function isInline()
	{
		if ($this->inline)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get extension of file for output.
	 *
	 * @param   bool  $escape  escape
	 *
	 * @return string
	 *
	 * @since  K4.0
	 */
	public function getExtension($escape = true)
	{
		$filename  = $this->protected ? $this->filename_real : $this->filename;
		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		return $escape ? htmlspecialchars($extension, ENT_COMPAT, 'UTF-8') : $extension;
	}

	/**
	 * This function shortens long filenames for display purposes.
	 *
	 * The first 8 characters of the filename, followed by three dots and the last 5 character of the filename.
	 *
	 * @param   int     $front   front
	 * @param   int     $back    back
	 * @param   string  $filler  filler
	 * @param   bool    $escape  escape
	 *
	 * @return string
	 *
	 * @since  K4.0
	 */
	public function getShortName($front = 10, $back = 8, $filler = '...', $escape = true)
	{
		if ($this->shortname === null)
		{
			$this->shortname = KunenaAttachmentHelper::shortenFileName($this->getFilename(false), $front, $back, $filler);
		}

		return $escape ? htmlspecialchars($this->shortname, ENT_COMPAT, 'UTF-8') : $this->shortname;
	}

	/**
	 * Get filename for output.
	 *
	 * @param   bool  $escape  escape
	 *
	 * @return string
	 *
	 * @since  K4.0
	 */
	public function getFilename($escape = true)
	{
	    $filename = $this->protected ? $this->filename_real : $this->filename;

		return $escape ? htmlspecialchars($filename, ENT_COMPAT, 'UTF-8') : $filename;
	}

	/**
	 * Get URL pointing to the attachment.
	 *
	 * @param   bool  $thumb   thumb
	 * @param   bool  $inline  inline
	 * @param   bool  $escape  escape
	 *
	 * @return string
	 *
	 * @throws Exception
	 * @throws null
	 * @since  K4.0
	 */
	public function getUrl($thumb = false, $inline = true, $escape = true)
	{
		$protect = (bool) KunenaConfig::getInstance()->attachment_protection;

		// Use direct URLs to the attachments if protection is turned off and file wasn't protected.
		if (!$protect)
		{
			$file      = $this->folder . '/' . $this->filename;
			$fileThumb = $this->folder . '/thumb/' . $this->filename;

			if (!is_file(JPATH_ROOT . '/' . $fileThumb))
			{
				$fileThumb = $file;
			}

			$url = ($thumb ? $fileThumb : $file);

			if (!Factory::getApplication()->isClient('administrator'))
			{
				$url = Uri::base() . $url;
			}

			return $escape ? htmlspecialchars($url, ENT_COMPAT, 'UTF-8') : $url;
		}

		// Route attachment through Kunena.
		$thumb    = $thumb ? '&thumb=1' : '';
		$download = $inline ? '' : '&download=1';

		$url = KunenaRoute::_("index.php?option=com_kunena&view=attachment&id={$this->id}{$thumb}{$download}&format=raw", $escape);

		if (\Joomla\CMS\Application\CMSApplication::getInstance('site')->get('sef_suffix'))
		{
			$url = preg_replace('/.html/', '', $url);
		}

		$app    = Factory::getApplication();
		$format = $app->input->getCmd('format');

		if ($protect && $inline && $this->isPdf())
		{
			$url = Uri::base() . $this->folder . '/' . $this->filename;
		}
		elseif ($protect && $format == 'feed')
		{
			$url = Uri::base() . $this->folder;
		}

		return $url;
	}

	/**
	 * Get attachment layout.
	 *
	 * @return KunenaLayout
	 * @throws Exception
	 * @since Kunena
	 */
	public function getLayout()
	{
		return KunenaLayout::factory('Attachment/Item')->set('attachment', $this);
	}

	/**
	 * @return string
	 * @throws Exception
	 * @since K4.0
	 */
	public function getTextLink()
	{
		return (string) KunenaLayout::factory('Attachment/Item')->set('attachment', $this)->setLayout('textlink');
	}

	/**
	 * @return string
	 * @throws Exception
	 * @since K4.0
	 */
	public function getImageLink()
	{
		return $this->isImage()
			? (string) KunenaLayout::factory('Attachment/Item')->set('attachment', $this)->setLayout('image') : null;
	}

	/**
	 * @return string
	 * @throws Exception
	 * @since K4.0
	 */
	public function getThumbnailLink()
	{
		return (string) KunenaLayout::factory('Attachment/Item')->set('attachment', $this)->setLayout('thumbnail');
	}

	/**
	 * Get author of the attachment.
	 *
	 * @return KunenaUser
	 *
	 * @throws Exception
	 * @since  K4.0
	 */
	public function getAuthor()
	{
		return KunenaUserHelper::get($this->userid);
	}

	/**
	 * Returns true if user is authorised to do the action.
	 *
	 * @param   string      $action  action
	 * @param   KunenaUser  $user    user
	 *
	 * @return boolean
	 *
	 * @throws null
	 * @since  K4.0
	 */
	public function isAuthorised($action = 'read', KunenaUser $user = null)
	{
		return !$this->tryAuthorise($action, $user, false);
	}

	/**
	 * Throws an exception if user isn't authorised to do the action.
	 *
	 * @param   string      $action  action
	 * @param   KunenaUser  $user    user
	 * @param   bool        $throw   throw
	 *
	 * @return mixed
	 * @throws null
	 * @since  K4.0
	 */
	public function tryAuthorise($action = 'read', KunenaUser $user = null, $throw = true)
	{
		// Special case to ignore authorisation.
		if ($action == 'none')
		{
			return;
		}

		// Load user if not given.
		if ($user === null)
		{
			$user = KunenaUserHelper::getMyself();
		}

		// Unknown action - throw invalid argument exception.
		if (!isset(self::$actions[$action]))
		{
			throw new InvalidArgumentException(Text::sprintf('COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action), 500);
		}

		// Load message authorisation.
		$exception = $this->getMessage()->tryAuthorise('attachment.' . $action, $user, false);

		// Check authorisation.
		if (!$exception)
		{
			foreach (self::$actions[$action] as $function)
			{
				$authFunction = 'authorise' . $function;
				$exception    = $this->$authFunction($user);

				if ($exception)
				{
					break;
				}
			}
		}

		// Throw or return the exception.
		if ($throw && $exception)
		{
			throw $exception;
		}

		return $exception;
	}

	/**
	 * Get message to which attachment has been attached into.
	 *
	 * NOTE: Returns message object even if there isn't one. Please call $message->exists() to check if it exists.
	 *
	 * @return KunenaForumMessage
	 *
	 * @since  K4.0
	 */
	public function getMessage()
	{
		return KunenaForumMessageHelper::get($this->mesid);
	}

	/**
	 * @param   string  $action  action
	 * @param   mixed   $user    user
	 * @param   bool    $silent  silent
	 *
	 * @return boolean
	 * @throws null
	 * @deprecated K4.0
	 * @since      Kunena
	 */
	public function authorise($action = 'read', $user = null, $silent = false)
	{
		if ($user === null)
		{
			$user = KunenaUserHelper::getMyself();
		}
		elseif (!($user instanceof KunenaUser))
		{
			$user = KunenaUserHelper::get($user);
		}

		$exception = $this->tryAuthorise($action, $user, false);

		if ($silent === false && $exception)
		{
			$this->setError($exception->getMessage());
		}

		if ($silent !== null)
		{
			return !$exception;
		}

		return $exception ? $exception->getMessage() : null;
	}

	/**
	 * @param   string    $key    key
	 * @param   null|int  $catid  catid
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since  K4.0
	 */
	public function upload($key = 'kattachment', $catid = null)
	{
		jimport('joomla.filesystem.folder');
		$config    = KunenaFactory::getConfig();
		$input     = Factory::getApplication()->input;
		$fileInput = $input->files->get($key, null, 'raw');

		$upload = KunenaUpload::getInstance(KunenaAttachmentHelper::getExtensions($catid, $this->userid));

		$uploadBasePath = JPATH_ROOT . '/media/kunena/attachments/' . $this->userid . '/';

		if (!JFolder::exists($uploadBasePath))
		{
			mkdir(JPATH_ROOT . '/media/kunena/attachments/' . $this->userid . '/');
		}

		$upload->splitFilename($fileInput['name']);

		$fileInput['name'] = preg_replace('/[[:space:]]/', '', $fileInput['name']);

		$fileNameWithoutExt = JFile::stripExt($fileInput['name']);
		$fileNameWithoutExt = strtolower($fileNameWithoutExt);

		$fileExt = JFile::getExt($fileInput['name']);
		$fileExt = strtolower($fileExt);

		$fileNameWithExt = $fileInput['name'];
		$fileNameWithExt = strtolower($fileNameWithExt);

		if (file_exists($uploadBasePath . $fileInput['name']))
		{
			for ($i = 2; file_exists($uploadBasePath . $fileNameWithoutExt . '.' . $fileExt); $i++)
			{
				$fileNameWithoutExt = $fileNameWithoutExt . "-$i";
				$fileNameWithExt    = $fileNameWithoutExt . '.' . $fileExt;
			}
		}

		$fileInput['name'] = $fileNameWithExt;

		$file = $upload->upload($fileInput, $uploadBasePath . $fileNameWithoutExt);

		if ($file->success)
		{
			if (extension_loaded('fileinfo'))
			{
				$finfo = new finfo(FILEINFO_MIME);

				$type = $finfo->file($uploadBasePath . $fileNameWithExt);
			}
			else
			{
				$info = getimagesize($uploadBasePath . $fileNameWithExt);

				$type = $info['mime'];
			}

			if (stripos($type, 'image/') !== false)
			{
				$imageInfo = KunenaImage::getImageFileProperties($uploadBasePath . $fileNameWithExt);

				if (number_format($file->size / 1024, 2) > $config->imagesize || $imageInfo->width > $config->imagewidth || $imageInfo->height > $config->imageheight)
				{
					// Calculate quality for both JPG and PNG.
					$quality = $config->imagequality;

					if ($quality < 1 || $quality > 100)
					{
						$quality = 70;
					}

					if ($imageInfo->type == IMAGETYPE_PNG)
					{
						$quality = intval(($quality - 1) / 10);
					}

					$options = array('quality' => $quality);

					try
					{
						$image = new KunenaImage($uploadBasePath . $fileNameWithExt);
						$image = $image->resize($config->imagewidth, $config->imagewidth, false);
						$image->toFile($uploadBasePath . $fileNameWithExt, $imageInfo->type, $options);
						unset($image);
					}
					catch (Exception $e)
					{
						echo $e->getMessage();

						return false;
					}
				}

				$this->filetype = $imageInfo->mime;
			}

			$this->protected     = (bool) $config->attachment_protection;
			$this->hash          = md5_file($uploadBasePath . $fileNameWithExt);
			$this->size          = $file->size;
			$this->folder        = 'media/kunena/attachments/' . $this->userid;
			$this->filename      = $fileInput['name'];
			$this->filename_real = $uploadBasePath . $fileNameWithExt;
			$this->caption       = '';
			$this->inline        = 0;

			return true;
		}
	}

	/**
	 * Set attachment file.
	 *
	 * Copies the attachment into proper location and makes sure that all the unset fields get properly assigned.
	 *
	 * @param   string  $source     Absolute path to the upcoming attachment.
	 * @param   string  $basename   Filename without extension.
	 * @param   string  $extension  File extension.
	 * @param   bool    $unlink     Whether to delete the original file or not.
	 * @param   bool    $overwrite  If not allowed, throw exception if the file exists.
	 *
	 * @return boolean
	 * @throws Exception
	 * @since  K4.0
	 */
	public function saveFile($source, $basename = null, $extension = null, $unlink = false, $overwrite = false)
	{
		if (!is_file($source))
		{
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '(): Attachment file not found.');
		}

		// Hash, size and MIME are set during saving, so let's deal with all other variables.
		$this->userid    = is_null($this->userid) ? KunenaUserHelper::getMyself() : $this->userid;
		$this->folder    = is_null($this->folder) ? "media/kunena/attachments/{$this->userid}" : $this->folder;
		$this->protected = is_null($this->protected) ? (bool) KunenaConfig::getInstance()->attachment_protection : $this->protected;

		if (!$this->filename_real)
		{
			$this->filename_real = $this->filename;
		}

		if (!$this->filename || $this->filename == $this->filename_real)
		{
			if (!$basename || !$extension)
			{
				throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '(): Parameters $basename or $extension not provided.');
			}

			// Find available filename.
			$this->filename = KunenaAttachmentHelper::getAvailableFilename(
				$this->folder, $basename, $extension, $this->protected
			);
		}

		// Create target directory if it does not exist.
		if (!KunenaFolder::exists(JPATH_ROOT . "/{$this->folder}") && !KunenaFolder::create(JPATH_ROOT . "/{$this->folder}"))
		{
			throw new RuntimeException(Text::_('Failed to create attachment directory.'));
		}

		$destination = JPATH_ROOT . "/{$this->folder}/{$this->filename}";

		// Move the file into the final location (if not already in there).
		if ($source != $destination)
		{
			// Create target directory if it does not exist.
			if (!$overwrite && is_file($destination))
			{
				throw new RuntimeException(Text::sprintf('Attachment %s already exists.'), $this->filename_real);
			}

			if ($unlink)
			{
				@chmod($source, 0644);
			}

			$success = KunenaFile::copy($source, $destination);

			if (!$success)
			{
				throw new RuntimeException(Text::sprintf('COM_KUNENA_UPLOAD_ERROR_NOT_MOVED', $destination));
			}

			KunenaPath::setPermissions($destination);

			if ($unlink)
			{
				unlink($source);
			}
		}

		return $this->save();
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return mixed
	 *
	 * @since  K4.0
	 */
	protected function authoriseExists(KunenaUser $user)
	{
		// Checks if attachment exists
		if (!$this->exists())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 404);
		}

		return;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return mixed
	 *
	 * @throws Exception
	 * @since  K4.0
	 */
	protected function authoriseRead(KunenaUser $user)
	{
		// Checks if attachment exists
		if (!$this->exists())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 404);
		}

		if (!$user->exists())
		{
			$config = KunenaConfig::getInstance();

			if ($this->isImage() && !$config->showimgforguest)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG'), 401);
			}

			if (!$this->isImage() && !$config->showfileforguest)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE'), 401);
			}
		}

		return;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return mixed
	 *
	 * @throws Exception
	 * @since  K4.0
	 */
	protected function authoriseOwn(KunenaUser $user)
	{
		// Checks if attachment is users own or user is moderator in the category (or global)
		if ($this->userid != $user->userid && !$user->isModerator($this->getMessage()->getCategory()) || !$user->exists() || $user->isBanned())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 403);
		}

		return;
	}

	/**
	 * Set inline to the attachment object
	 *
	 * @param   int  $inline  inline
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function setInline($inline)
	{
		$this->inline = $inline;

		$success = $this->save();

		return $success;
	}
}
