<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Message.Attachment
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Attachment;

defined('_JEXEC') or die();

use Exception;
use finfo;
use InvalidArgumentException;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;
use Kunena\Forum\Libraries\Exception\Authorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Message\Message;
use Kunena\Forum\Libraries\Forum\Message\MessageHelper;
use Kunena\Forum\Libraries\Image\KunenaImage;
use Kunena\Forum\Libraries\Layout\Layout;
use Kunena\Forum\Libraries\Path\KunenaPath;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Upload\Upload;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;
use function defined;

/**
 * Class KunenaAttachment
 *
 * @since   Kunena 4.0
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
 * @property string $comment
 * @property int    $inline
 * @property string $typeAlias
 * @property int    $width   Image width (0 for non-images).
 * @property int    $height  Image height (0 for non-images).
 *
 */
class Attachment extends KunenaDatabaseObject
{
	// Higher protection level means that the attachment is visible to less people.
	// Protection level can be checked as bitmask: PROTECTION_ACL + PROTECTION_FRIENDS.
	// To filter out attachments when doing a database query, you can use:
	// Visible for author = value < PROTECTION_AUTHOR * 2
	// TODO: Implement these

	/**
	 * @since   Kunena 6.0
	 */
	const PROTECTION_NONE = 0;

	/**
	 * @since   Kunena 6.0
	 */
	const PROTECTION_PUBLIC = 1;

	/**
	 * @since   Kunena 6.0
	 */
	const PROTECTION_ACL = 2;

	/**
	 * @since   Kunena 6.0
	 */
	const PROTECTION_FRIENDS = 4;

	/**
	 * @since   Kunena 6.0
	 */
	const PROTECTION_MODERATORS = 8;

	/**
	 * @since   Kunena 6.0
	 */
	const PROTECTION_ADMINS = 16;

	/**
	 * @since   Kunena 6.0
	 */
	const PROTECTION_PRIVATE = 32;

	/**
	 * @since   Kunena 6.0
	 */
	const PROTECTION_AUTHOR = 64;

	/**
	 * @since   Kunena 6.0
	 */
	const PROTECTION_UNPUBLISHED = 128;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected static $_directory = 'media/kunena/attachments';

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected static $actions = [
		'read'        => ['Read'],
		'createimage' => [],
		'createfile'  => [],
		'delete'      => ['Exists', 'Own'],
	];

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $id = null;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public $disabled = false;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $width;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $height;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $folder;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $userid;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $mesid;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $protected;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $hash;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $size;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $filetype;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $filename;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $filename_real;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $comment;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public $inline;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $typeAlias;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $caption;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $_table = 'KunenaAttachments';

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $path;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $shortname;

	/**
	 * @param   mixed  $identifier  identifier
	 * @param   bool   $reload      reload
	 *
	 * @return  Attachment
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 */
	public static function getInstance($identifier = null, $reload = false)
	{
		return AttachmentHelper::get($identifier, $reload);
	}

	/**
	 * Destructor deletes the files from the filesystem if attachment isn't stored in database.
	 *
	 * @since   Kunena 4.0
	 */
	public function __destruct()
	{
		if (!$this->exists())
		{
			$this->deleteFile();
		}
	}

	/**
	 * @internal
	 *
	 * @return  void
	 *
	 * @since   Kunena 4.0
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
			File::delete($filename);
		}

		$filename = $path . '/raw/' . $this->filename;

		if (is_file($filename))
		{
			File::delete($filename);
		}

		$filename = $path . '/thumb/' . $this->filename;

		if (is_file($filename))
		{
			File::delete($filename);
		}
	}

	/**
	 * Getter function.
	 *
	 * @param   string  $property  property
	 *
	 * @return  integer
	 *
	 * @since   Kunena 4.0
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
			case 'filename':
				return $this->filename;
			case 'folder':
				return $this->folder;
			case 'userid':
				return $this->userid;
			case 'mesid':
				return $this->mesid;
			case 'protected':
				return $this->protected;
			case 'hash':
				return $this->hash;
			case 'size':
				return $this->size;
			case 'filetype':
				return $this->filetype;
			case 'filename_real':
				return $this->filename_real;
			case 'comment':
				return $this->comment;
			case 'inline':
				return $this->inline;
			case 'typeAlias':
				return $this->typeAlias;
			case 'caption':
				return $this->caption;
		}

		throw new InvalidArgumentException(sprintf('Property "%s" is not defined', $property));
	}

	/**
	 * @internal
	 *
	 * @return  void
	 *
	 * @since   Kunena 4.0
	 */
	protected function initialize()
	{
		$path = $this->getPath();

		if ($path && $this->isImage())
		{
			list($this->width, $this->height) = Image::getImageFileProperties($path);
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
	 * @return  string|false  Path to the file or false if file doesn't exist.
	 *
	 * @since   Kunena 4.0
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
	 * @since   Kunena 4.0
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
	 * Set inline to the attachment object
	 *
	 * @param   int  $inline  inline
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function setInline($inline)
	{
		$this->inline = $inline;

		$success = $this->save();

		return $success;
	}

	/**
	 * Get extension of file for output.
	 *
	 * @param   bool  $escape  escape
	 *
	 * @return  string
	 *
	 * @since   Kunena 4.0
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
	 * @return  string
	 *
	 * @since   Kunena 4.0
	 */
	public function getShortName($front = 10, $back = 8, $filler = '...', $escape = true)
	{
		if ($this->shortname === null)
		{
			$this->shortname = AttachmentHelper::shortenFileName($this->getFilename(false), $front, $back, $filler);
		}

		return $escape ? htmlspecialchars($this->shortname, ENT_COMPAT, 'UTF-8') : $this->shortname;
	}

	/**
	 * Get filename for output.
	 *
	 * @param   bool  $escape  escape
	 *
	 * @return  string
	 *
	 * @since   Kunena 4.0
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
	 * @return  string
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 * @throws  null
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

		if (CMSApplication::getInstance('site')->get('sef_suffix'))
		{
			$url = preg_replace('/.html/', '', $url);
		}

		if ($protect && $inline && $this->isPdf())
		{
			$url = Uri::base() . $this->folder . '/' . $this->filename_real;
		}

		return $url;
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
	 * Get attachment layout.
	 *
	 * @return  Layout
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getLayout()
	{
		return Layout::factory('Attachment/Item')->set('attachment', $this);
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 */
	public function getTextLink()
	{
		return (string) Layout::factory('Attachment/Item')->set('attachment', $this)->setLayout('textlink');
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 */
	public function getImageLink()
	{
		return $this->isImage()
			? (string) Layout::factory('Attachment/Item')->set('attachment', $this)->setLayout('image') : null;
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 */
	public function getThumbnailLink()
	{
		return (string) Layout::factory('Attachment/Item')->set('attachment', $this)->setLayout('thumbnail');
	}

	/**
	 * Get author of the attachment.
	 *
	 * @return  KunenaUser
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
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
	 * @return  boolean
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  null
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
	 * @return  mixed
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  null
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

		// Start by checking if attachment is protected.
		$exception = !$this->protected
			? null : new Authorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), $user->id ? 403 : 401);

		// TODO: Add support for PROTECTION_PUBLIC
		// Currently we only support ACL checks, not public attachments.
		if ($exception && $this->mesid && $this->protected & (self::PROTECTION_PUBLIC + self::PROTECTION_ACL))
		{
			// Load message authorisation.
			$exception = $this->getMessage()->tryAuthorise('attachment.' . $action, $user, false);
		}

		// TODO: Add support for PROTECTION_FRIENDS
		// TODO: Add support for PROTECTION_MODERATORS
		// TODO: Add support for PROTECTION_ADMINS
		// Check if attachment is private.
		if ($exception && $this->protected & self::PROTECTION_PRIVATE)
		{
			$exception = $this->authorisePrivate($action, $user);
		}

		// Check author access.
		if ($exception && $this->protected & self::PROTECTION_AUTHOR)
		{
			$exception = $user->exists() && $user->id == $this->userid
				? null : new Authorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), $user->userid ? 403 : 401);
		}

		if ($exception)
		{
			// Hide original exception behind no access.
			$exception = new Authorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), $user->userid ? 403 : 401, $exception);
		}
		else
		{
			// Check authorisation action.
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
	 * @return  Message
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 */
	public function getMessage()
	{
		return MessageHelper::get($this->mesid);
	}

	/**
	 * Check is an attachment is private
	 *
	 * @param   string      $action  action
	 * @param   KunenaUser  $user    user
	 *
	 * @return  Authorise|NULL
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function authorisePrivate($action, KunenaUser $user)
	{
		if (!$user->exists())
		{
			return new Authorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 401);
		}

		if ($action == 'create')
		{
			return null;
		}

		// Need to load private message (for now allow only one private message per attachment).
		$map = Table::getInstance('KunenaPrivateAttachmentMap', 'Table');
		$map->load(['attachment_id' => $this->id]);
		$finder  = new \Kunena\Forum\Libraries\KunenaPrivate\Message\Finder;
		$private = $finder->where('id', '=', $map->private_id)->firstOrNew();

		if (!$private->exists())
		{
			return new Authorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 403);
		}

		if (in_array($user->userid, $private->users()->getMapped()))
		{
			// Yes, I have access..
			return null;
		}
		else
		{
			$messages = MessageHelper::getMessages($private->posts()->getMapped());

			foreach ($messages as $message)
			{
				if ($user->isModerator($message->getCategory()))
				{
					// Yes, I have access..
					return null;
				}
			}
		}

		return new Authorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 403);
	}

	/**
	 * @param   string    $key    key
	 * @param   null|int  $catid  catid
	 *
	 * @return  boolean|void
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 */
	public function upload($key = 'kattachment', $catid = null)
	{
		$config    = KunenaFactory::getConfig();
		$input     = Factory::getApplication()->input;
		$fileInput = $input->files->get($key, null, 'raw');

		$upload = Upload::getInstance(AttachmentHelper::getExtensions($catid, $this->userid));

		$uploadBasePath = JPATH_ROOT . '/media/kunena/attachments/' . $this->userid . '/';

		if (!Folder::exists($uploadBasePath))
		{
			mkdir(JPATH_ROOT . '/media/kunena/attachments/' . $this->userid . '/');
		}

		$upload->splitFilename($fileInput['name']);

		$fileInput['name'] = preg_replace('/[[:space:]]/', '', $fileInput['name']);

		$fileNameWithoutExt = File::stripExt($fileInput['name']);
		$fileNameWithoutExt = strtolower($fileNameWithoutExt);

		$fileExt = File::getExt($fileInput['name']);
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
				throw new RuntimeException("Fileinfo extension not loaded.");
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

					$options = ['quality' => $quality];

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
	 * @return  boolean
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 */
	public function saveFile($source, $basename = null, $extension = null, $unlink = false, $overwrite = false)
	{
		if (!is_file($source))
		{
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '(): Attachment file not found.');
		}

		// Hash, size and MIME are set during saving, so let's deal with all other variables.
		$this->userid = is_null($this->userid) ? KunenaUserHelper::getMyself() : $this->userid;
		$this->folder = is_null($this->folder) ? "media/kunena/attachments/{$this->userid}" : $this->folder;

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
			$this->filename = AttachmentHelper::getAvailableFilename(
				$this->folder, $basename, $extension, $this->protected
			);
		}

		// Create target directory if it does not exist.
		if (!Folder::exists(JPATH_ROOT . "/{$this->folder}") && !Folder::create(JPATH_ROOT . "/{$this->folder}"))
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

			$success = File::copy($source, $destination);

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
	 * Remove the BBCode [attachment=attachmentID][/attachment] from text message
	 *
	 * @param   string  $editor_text  editor text
	 *
	 * @return  boolean|void
	 *
	 * @since   Kunena 6.0
	 */
	public function removeBBCodeInMessage($editor_text = null)
	{
		if (!$this->inline)
		{
			return false;
		}

		if (empty($editor_text))
		{
			return false;
		}

		$find    = ['/\[attachment=' . $this->id . '\](.*?)\[\/attachment\]/su'];
		$replace = '';
		$text    = preg_replace($find, $replace, $editor_text);

		return $text;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  mixed|void
	 *
	 * @since   Kunena 4.0
	 */
	protected function authoriseExists(KunenaUser $user)
	{
		// Checks if attachment exists
		if (!$this->exists())
		{
			return new Authorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 404);
		}

		return;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  mixed|void
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 */
	protected function authoriseRead(KunenaUser $user)
	{
		// Checks if attachment exists
		if (!$this->exists())
		{
			return new Authorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 404);
		}

		if (!$user->exists())
		{
			$config = KunenaConfig::getInstance();

			if ($this->isImage() && !$config->showimgforguest)
			{
				return new Authorise(Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG'), 401);
			}

			if (!$this->isImage() && !$config->showfileforguest)
			{
				return new Authorise(Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE'), 401);
			}
		}

		return;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  mixed|void
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 */
	protected function authoriseOwn(KunenaUser $user)
	{
		// Checks if attachment is users own or user is moderator in the category (or global)
		if ($this->userid != $user->userid && !$user->isModerator($this->getMessage()->getCategory()) || !$user->exists() || $user->isBanned())
		{
			return new Authorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 403);
		}

		return;
	}
}
