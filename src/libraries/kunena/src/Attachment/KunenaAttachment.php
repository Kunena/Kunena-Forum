<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Message.Attachment
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Attachment;

\defined('_JEXEC') or die();

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
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Image\KunenaImage;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Path\KunenaPath;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Upload\KunenaUpload;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;

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
 * @property string $comment
 * @property int    $inline
 * @property string $typeAlias
 * @property int    $width   Image width (0 for non-images).
 * @property int    $height  Image height (0 for non-images).
 * @property string $setLayout
 * @since   Kunena 4.0
 */
class KunenaAttachment extends KunenaDatabaseObject
{
	// Higher protection level means that the KunenaAttachment is visible to less people.
	// Protection level can be checked as bitmask: PROTECTION_ACL + PROTECTION_FRIENDS.
	// To filter out KunenaAttachments when doing a database query, you can use:
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
	public $setLayout = 'default';

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
	 * @return  KunenaAttachment
	 *
	 * @throws  Exception
	 * @since   Kunena 4.0
	 */
	public static function getInstance($identifier = null, $reload = false): KunenaAttachment
	{
		return KunenaAttachmentHelper::get($identifier, $reload);
	}

	/**
	 * Destructor deletes the files from the filesystem if KunenaAttachment isn't stored in database.
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
	 * @return  void
	 *
	 * @internal
	 *
	 * @since   Kunena 4.0
	 */
	protected function deleteFile(): void
	{
		if (self::$_directory != substr($this->folder, 0, \strlen(self::$_directory)))
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
	public function __get(string $property)
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
	 * @return  void
	 *
	 * @internal
	 *
	 * @since   Kunena 4.0
	 */
	protected function initialize(): void
	{
		$path = $this->getPath();

		if ($path && $this->isImage())
		{
			[$this->width, $this->height] = Image::getImageFileProperties($path);
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
	 * Check if KunenaAttachment is image.
	 *
	 * @return  boolean  True if KunenaAttachment is image.
	 *
	 * @since   Kunena 4.0
	 */
	public function isImage(): bool
	{
		return stripos($this->filetype, 'image/') !== false;
	}

	/**
	 * Check if KunenaAttachment is audio.
	 *
	 * @return  boolean  True if KunenaAttachment is image.
	 *
	 * @since  K5.1
	 */
	public function isAudio(): bool
	{
		return stripos($this->filetype, 'audio/') !== false;
	}

	/**
	 * Check if KunenaAttachment is audio.
	 *
	 * @return  boolean  True if KunenaAttachment is image.
	 *
	 * @since  K5.1
	 */
	public function isVideo(): bool
	{
		return stripos($this->filetype, 'video/') !== false;
	}

	/**
	 * Check if KunenaAttachment is inline.
	 *
	 * @return  boolean  True if KunenaAttachment is inline.
	 *
	 * @since  K5.1.9
	 */
	public function isInline(): bool
	{
		if ($this->inline)
		{
			return true;
		}

		return false;
	}

	/**
	 * Set inline to the KunenaAttachment object
	 *
	 * @param   int  $inline  inline
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function setInline(int $inline): bool
	{
		$this->inline = $inline;

		return $this->save();
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
	public function getExtension($escape = true): string
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
			$this->shortname = KunenaAttachmentHelper::shortenFileName($this->getFilename(false), (int) $front, (int) $back, $filler);
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
	public function getFilename($escape = true): string
	{
		$filename = $this->protected ? $this->filename_real : $this->filename;

		return $escape ? htmlspecialchars($filename, ENT_COMPAT, 'UTF-8') : $filename;
	}

	/**
	 * Get URL pointing to the KunenaAttachment.
	 *
	 * @param   bool  $thumb   thumb
	 * @param   bool  $inline  inline
	 * @param   bool  $escape  escape
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 4.0
	 */
	public function getUrl($thumb = false, $inline = true, $escape = true): string
	{
		$protect = (bool) KunenaConfig::getInstance()->attachmentProtection;

		// Use direct URLs to the KunenaAttachments if protection is turned off and file wasn't protected.
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

		// Route KunenaAttachment through Kunena.
		$thumb    = $thumb ? '&thumb=1' : '';
		$download = $inline ? '' : '&download=1';

		$url = KunenaRoute::_("index.php?option=com_kunena&view=attachment&id={$this->id}{$thumb}{$download}&format=raw", $escape);

		if (CMSApplication::getInstance('site')->get('sef_suffix'))
		{
			$url = preg_replace('/.html/', '', $url);
		}

		if ($protect && $inline && $this->isPdf())
		{
			$url = Uri::base() . $this->folder . '/' . $this->filename;
		}

		return $url;
	}

	/**
	 * Check if KunenaAttachment is pdf.
	 *
	 * @return  boolean  True if KunenaAttachment is pdf.
	 *
	 * @since  K5.1
	 */
	public function isPdf(): bool
	{
		return stripos($this->filetype, 'application/pdf') !== false;
	}

	/**
	 * Get KunenaAttachment layout.
	 *
	 * @return  KunenaLayout
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getLayout(): KunenaLayout
	{
		return KunenaLayout::factory('Attachment/Item')->set('attachment', $this);
	}

	/**
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 4.0
	 */
	public function getTextLink(): string
	{
		return (string) KunenaLayout::factory('Attachment/Item')->set('attachment', $this)->setLayout('textlink');
	}

	/**
	 * @return string|null
	 *
	 * @since   Kunena 4.0
	 * @throws \Exception
	 */
	public function getImageLink(): ?string
	{
		return $this->isImage()
			? (string) KunenaLayout::factory('Attachment/Item')->set('attachment', $this)->setLayout('image') : null;
	}

	/**
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 4.0
	 */
	public function getThumbnailLink(): string
	{
		return (string) KunenaLayout::factory('Attachment/Item')->set('attachment', $this)->setLayout('thumbnail');
	}

	/**
	 * Get author of the KunenaAttachment.
	 *
	 * @return  KunenaUser
	 *
	 * @throws  Exception
	 * @since   Kunena 4.0
	 */
	public function getAuthor(): KunenaUser
	{
		return KunenaUserHelper::get($this->userid);
	}

	/**
	 * Returns true if user is authorised to do the action.
	 *
	 * @param   string           $action  action
	 * @param   KunenaUser|null  $user    user
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 4.0
	 */
	public function isAuthorised($action = 'read', KunenaUser $user = null)
	{
		return !$this->tryAuthorise($action, $user, false);
	}

	/**
	 * Throws an exception if user isn't authorised to do the action.
	 *
	 * @param   string           $action  action
	 * @param   KunenaUser|null  $user    user
	 * @param   bool             $throw   throw
	 *
	 * @return  mixed|void
	 *
	 * @throws Exception
	 * @since   Kunena 4.0
	 */
	public function tryAuthorise($action = 'read', KunenaUser $user = null, $throw = true)
	{
		// Special case to ignore authorisation.
		if ($action == 'none')
		{
			return false;
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

		// Start by checking if KunenaAttachment is protected.
		$exception = !$this->protected
			? null : new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), $user->id ? 403 : 401);

		// TODO: Add support for PROTECTION_PUBLIC
		// Currently we only support ACL checks, not public KunenaAttachments.
		if ($exception && $this->mesid && $this->protected && (self::PROTECTION_PUBLIC + self::PROTECTION_ACL))
		{
			// Load message authorisation.
			$exception = $this->getMessage()->tryAuthorise('attachment.' . $action, $user, false);
		}

		// TODO: Add support for PROTECTION_FRIENDS
		// TODO: Add support for PROTECTION_MODERATORS
		// TODO: Add support for PROTECTION_ADMINS
		// Check if KunenaAttachment is private.
		if ($exception && $this->protected && self::PROTECTION_PRIVATE)
		{
			$exception = $this->authorisePrivate($action, $user);
		}

		// Check author access.
		if ($exception && $this->protected && self::PROTECTION_AUTHOR)
		{
			$exception = $user->exists() && $user->id == $this->userid
				? null : new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), $user->userid ? 403 : 401);
		}

		if ($exception)
		{
			// Hide original exception behind no access.
			$exception = new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), $user->userid ? 403 : 401, $exception);
		}
		else
		{
			// Check authorisation action.
			foreach (self::$actions[$action] as $function)
			{
				$authFunction = 'authorise' . $function;

				try
				{
					$this->$authFunction($user);
				}
				catch (Exception $e)
				{
					new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), $user->userid ? 403 : 401, $e->getMessage());
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
	 * Get message to which KunenaAttachment has been attached into.
	 *
	 * NOTE: Returns message object even if there isn't one. Please call $message->exists() to check if it exists.
	 *
	 * @return  KunenaMessage
	 *
	 * @throws  Exception
	 * @since   Kunena 4.0
	 */
	public function getMessage(): KunenaMessage
	{
		return KunenaMessageHelper::get($this->mesid);
	}

	/**
	 * Check is an KunenaAttachment is private
	 *
	 * @param   string      $action  action
	 * @param   KunenaUser  $user    user
	 *
	 * @return  KunenaExceptionAuthorise|NULL
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	protected function authorisePrivate(string $action, KunenaUser $user): ?KunenaExceptionAuthorise
	{
		if (!$user->exists())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 401);
		}

		if ($action == 'create')
		{
			return false;
		}

		// Need to load private message (for now allow only one private message per KunenaAttachment).
		$map = Table::getInstance('KunenaPrivateAttachmentMap', 'Table');
		$map->load(['attachment_id' => $this->id]);
		$finder  = new \Kunena\Forum\Libraries\KunenaPrivate\Message\KunenaFinder;
		$private = $finder->where('id', '=', $map->private_id)->firstOrNew();

		if (!$private->exists())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 403);
		}

		if (\in_array($user->userid, $private->users()->getMapped()))
		{
			// Yes, I have access..
			return true;
		}
		else
		{
			$messages = KunenaMessageHelper::getMessages($private->posts()->getMapped());

			foreach ($messages as $message)
			{
				if ($user->isModerator($message->getCategory()))
				{
					// Yes, I have access..
					return true;
				}
			}
		}

		return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 403);
	}

	/**
	 * @param   string    $key    key
	 * @param   null|int  $catid  catid
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 * @since   Kunena 4.0
	 */
	public function upload(string $key = 'kattachment', int $catid = null): bool
	{
		$config    = KunenaFactory::getConfig();
		$input     = Factory::getApplication()->input;
		$fileInput = $input->files->get($key, null, 'raw');

		$upload = KunenaUpload::getInstance(KunenaAttachmentHelper::getExtensions($catid, $this->userid));

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
			if (\extension_loaded('fileinfo'))
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

				if (number_format($file->size / 1024, 2) > $config->imageSize || $imageInfo->width > $config->imageWidth || $imageInfo->height > $config->imageHeight)
				{
					// Calculate quality for both JPG and PNG.
					$quality = $config->imageQuality;

					if ($quality < 1 || $quality > 100)
					{
						$quality = 70;
					}

					if ($imageInfo->type == IMAGETYPE_PNG)
					{
						$quality = \intval(($quality - 1) / 10);
					}

					$options = ['quality' => $quality];

					try
					{
						$image = new KunenaImage($uploadBasePath . $fileNameWithExt);
						$image = $image->resize($config->imageWidth, $config->imageHeight, false);
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

			$this->protected     = (bool) $config->attachmentProtection;
			$this->hash          = md5_file($uploadBasePath . $fileNameWithExt);
			$this->size          = $file->size;
			$this->folder        = 'media/kunena/attachments/' . $this->userid;
			$this->filename      = $fileInput['name'];
			$this->filename_real = $uploadBasePath . $fileNameWithExt;
			$this->caption       = '';
			$this->inline        = 0;

			return true;
		}

		return false;
	}

	/**
	 * Set KunenaAttachment file.
	 *
	 * Copies the KunenaAttachment into proper location and makes sure that all the unset fields get properly assigned.
	 *
	 * @param   string  $source     Absolute path to the upcoming KunenaAttachment.
	 * @param   null    $basename   Filename without extension.
	 * @param   null    $extension  File extension.
	 * @param   bool    $unlink     Whether to delete the original file or not.
	 * @param   bool    $overwrite  If not allowed, throw exception if the file exists.
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 4.0
	 */
	public function saveFile(string $source, $basename = null, $extension = null, $unlink = false, $overwrite = false): bool
	{
		if (!is_file($source))
		{
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '(): KunenaAttachment file not found.');
		}

		// Hash, size and MIME are set during saving, so let's deal with all other variables.
		$this->userid = \is_null($this->userid) ? KunenaUserHelper::getMyself() : $this->userid;
		$this->folder = \is_null($this->folder) ? "media/kunena/attachments/{$this->userid}" : $this->folder;

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
				$this->folder,
				$basename,
				$extension,
				$this->protected
			);
		}

		// Create target directory if it does not exist.
		if (!Folder::exists(JPATH_ROOT . "/{$this->folder}") && !Folder::create(JPATH_ROOT . "/{$this->folder}"))
		{
			throw new RuntimeException(Text::_('Failed to create KunenaAttachment directory.'));
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
	 * @param   string|null  $editor_text  editor text
	 *
	 * @return bool
	 *
	 * @since   Kunena 6.0
	 */
	public function removeBBCodeInMessage(string $editor_text = null): bool
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

		return preg_replace($find, $replace, $editor_text);
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  boolean|KunenaExceptionAuthorise
	 *
	 * @since   Kunena 4.0
	 */
	protected function authoriseExists(KunenaUser $user)
	{
		// Checks if KunenaAttachment exists
		if (!$this->exists())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 404);
		}

		return true;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  boolean|KunenaExceptionAuthorise
	 *
	 * @throws  Exception
	 * @since   Kunena 4.0
	 */
	protected function authoriseRead(KunenaUser $user)
	{
		// Checks if KunenaAttachment exists
		if (!$this->exists())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 404);
		}

		if (!$user->exists())
		{
			$config = KunenaConfig::getInstance();

			if ($this->isImage() && !$config->showImgForGuest)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG'), 401);
			}

			if (!$this->isImage() && !$config->showFileForGuest)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE'), 401);
			}
		}

		return true;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  boolean|KunenaExceptionAuthorise
	 *
	 * @throws  Exception
	 * @since   Kunena 4.0
	 */
	protected function authoriseOwn(KunenaUser $user)
	{
		// Checks if KunenaAttachment is users own or user is moderator in the category (or global)
		if ($this->userid != $user->userid && !$user->isModerator($this->getMessage()->getCategory()) || !$user->exists() || $user->isBanned())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 403);
		}

		return true;
	}
}
