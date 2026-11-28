<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Message.Attachment
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Attachment;

\defined('_JEXEC') or die();

use Exception;
use finfo;
use InvalidArgumentException;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;
use Kunena\Forum\Libraries\Event\KunenaAfterAttachmentUploadEvent;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Image\KunenaImage;
use Kunena\Forum\Libraries\KunenaPrivate\Message\KunenaPrivateMessageFinder;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Path\KunenaPath;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Tables\TableKunenaPrivateAttachmentMap;
use Kunena\Forum\Libraries\Upload\KunenaUpload;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;
use Throwable;

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
 * @property int    $status
 * @property string $typeAlias
 * @property int    $width         Image width (0 for non-images).
 * @property int    $height        Image height (0 for non-images).
 * @property string $setLayout
 * @since   Kunena 4.0
 */
class KunenaAttachment extends KunenaDatabaseObject
{
    // Higher protection level means that the KunenaAttachment is visible to less people.
    // Protection level can be checked as bitmask: PROTECTION_ACL + PROTECTION_FRIENDS.
    // To filter out KunenaAttachments when doing a database query, you can use:
    // Visible for author = value < PROTECTION_AUTHOR * 2
    // tryAuthorise() checks them all (PROTECTION_FRIENDS: the users whom the author follows, KunenaProfile::isFriend()).

    /**
     * @since   Kunena 6.0
     */
    public const int PROTECTION_NONE = 0;

    /**
     * @since   Kunena 6.0
     */
    public const int PROTECTION_PUBLIC = 1;

    /**
     * @since   Kunena 6.0
     */
    public const int PROTECTION_ACL = 2;

    /**
     * @since   Kunena 6.0
     */
    public const int PROTECTION_FRIENDS = 4;

    /**
     * @since   Kunena 6.0
     */
    public const int PROTECTION_MODERATORS = 8;

    /**
     * @since   Kunena 6.0
     */
    public const int PROTECTION_ADMINS = 16;

    /**
     * @since   Kunena 6.0
     */
    public const int PROTECTION_PRIVATE = 32;

    /**
     * @since   Kunena 6.0
     */
    public const int PROTECTION_AUTHOR = 64;

    /**
     * @since   Kunena 6.0
     */
    public const int PROTECTION_UNPUBLISHED = 128;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected static mixed $_directory = 'media/kunena/attachments';

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected static mixed $actions = [
        'read'        => ['Read'],
        'createimage' => [],
        'createfile'  => [],
        'delete'      => ['Exists', 'Own'],
        'private'     => ['Private'],
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
    public mixed $disabled = false;

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    public mixed $width = null;

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    public mixed $height = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $folder = null;

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    public $userid;

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    public mixed $mesid = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $protected = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $hash = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $size = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $filetype = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $filename = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $filename_real = null;

    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    public mixed $inline = null;

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    public mixed $status = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $typeAlias = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $caption = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $setLayout = 'default';

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $_table = 'KunenaAttachments';

    /**
     * The answers of the check of a private attachment, by user: a page asks it more than once for one file (may he delete it,
     * may he read it), the answer is the same for every action (but creating) and does not change while the page is made.
     *
     * @var     array
     * @since   Kunena 8.0
     */
    protected array $_privateAuthorised = [];

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $path = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $shortname = null;

    /**
     * Destructor deletes the files from the filesystem if KunenaAttachment isn't stored in database.
     *
     * @since   Kunena 4.0
     */
    public function __destruct()
    {
        if (!$this->exists()) {
            $this->deleteFile();
        }
    }

    /**
     * Getter function.
     *
     * @param   string  $property  property
     *
     * @return  mixed
     *
     * @since   Kunena 4.0
     */
    public function __get(string $property)
    {
        if ($this->width == null) {
            $this->initialize();
        }

        switch ($property) {
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
            case 'inline':
                return $this->inline;
            case 'status':
                return $this->status;
            case 'typeAlias':
                return $this->typeAlias;
            case 'caption':
                return $this->caption;
        }

        throw new InvalidArgumentException(\sprintf('Property "%s" is not defined', $property));
    }

    /**
     * @param   KunenaUser  $user  user
     *
     * @return  KunenaExceptionAuthorise|void
     *
     * @since   Kunena 4.0
     */
    protected function authoriseExists(KunenaUser $user)
    {
        // Checks if KunenaAttachment exists
        if (!$this->exists()) {
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 404);
        }

        return;
    }

    /**
     * @param   KunenaUser  $user  user
     *
     * @return  KunenaExceptionAuthorise|void
     *
     * @throws  Exception
     * @since   Kunena 4.0
     */
    protected function authoriseOwn(KunenaUser $user)
    {
        // Checks if KunenaAttachment is users own or user is moderator in the category (or global)
        if ($this->userid != $user->userid && !$user->isModerator($this->getMessage()->getCategory()) || !$user->exists() || $user->isBanned()) {
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 403);
        }

        return;
    }

    /**
     * Check is an KunenaAttachment is private
     *
     * @param   KunenaUser  $user    user
     * @param   string      $action  action
     *
     * @return  KunenaExceptionAuthorise|null|bool
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    protected function authorisePrivate(KunenaUser $user, $action = '')
    {
        if (!$user->exists()) {
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 401);
        }

        if ($action == 'create') {
            return false;
        }

        $key = (int) $user->userid;

        if (!\array_key_exists($key, $this->_privateAuthorised)) {
            $this->_privateAuthorised[$key] = $this->loadPrivateAuthorisation($user);
        }

        return $this->_privateAuthorised[$key];
    }

    /**
     * May the user read the private attachment: a user of its private message, or a moderator of a message of it.
     *
     * @param   KunenaUser  $user  The user.
     *
     * @return  KunenaExceptionAuthorise|null  Null when the user may.
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    protected function loadPrivateAuthorisation(KunenaUser $user): ?KunenaExceptionAuthorise
    {
        // The private message of the attachment (an attachment belongs to one private message).
        $db  = Factory::getContainer()->get(DatabaseInterface::class);
        $map = new TableKunenaPrivateAttachmentMap($db);

        $map->load(['attachment_id' => $this->id]);
        $finder  = new KunenaPrivateMessageFinder();
        $private = $finder->where('id', '=', $map->private_id)->firstOrNew();

        if (!$private->exists()) {
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 403);
        }

        if (\in_array($user->userid, $private->users()->getMapped())) {
            // Yes, I have access..
            return null;
        }

        $messages = KunenaMessageHelper::getMessages($private->posts()->getMapped());

        foreach ($messages as $message) {
            if ($user->isModerator($message->getCategory())) {
                // Yes, I have access..
                return null;
            }
        }

        return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 403);
    }

    /**
     * @param   KunenaUser  $user  user
     *
     * @return  KunenaExceptionAuthorise|void
     *
     * @throws  Exception
     * @since   Kunena 4.0
     */
    protected function authoriseRead(KunenaUser $user)
    {
        // Checks if KunenaAttachment exists
        if (!$this->exists()) {
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), 404);
        }

        if (!$user->exists()) {
            $config = KunenaConfig::getInstance();

            if ($this->isImage() && !$config->showImgForGuest) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG'), 401);
            }

            if (!$this->isImage() && !$config->showFileForGuest) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE'), 401);
            }
        }

        // A file without protection ("Protect Attachments" off, the default) is for who may read its message, and a file which
        // waits for its message for its uploader: the right of the file did not look at the message, so the address of the file
        // (view=attachment&id=N, the ids follow each other) gave the files of the hidden categories, of the unapproved and the
        // deleted messages, and the uploads of the others.
        if (!$this->protected) {
            if ($this->mesid) {
                $exception = $this->getMessage()->tryAuthorise('attachment.read', $user, false);

                if ($exception) {
                    return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), $user->userid ? 403 : 401, $exception);
                }
            } elseif ($user->exists() ? (int) $user->userid !== (int) $this->userid : !\in_array((int) $this->id, KunenaPendingAttachment::guestUploads(), true)) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), $user->userid ? 403 : 401);
            }
        }

        return;
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
        if (empty($this->folder)) {
            return;
        }

        if (self::$_directory != substr($this->folder, 0, \strlen(self::$_directory))) {
            return;
        }

        $path     = JPATH_ROOT . "/{$this->folder}";
        $filename = $path . '/' . $this->filename;

        if (is_file($filename)) {
            File::delete($filename);
        }

        $filename = $path . '/raw/' . $this->filename;

        if (is_file($filename)) {
            File::delete($filename);
        }

        $filename = $path . '/thumb/' . $this->filename;

        if (is_file($filename)) {
            File::delete($filename);
        }
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
        $filename  = $this->shownName();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        return $escape ? htmlspecialchars($extension, ENT_COMPAT, 'UTF-8') : $extension;
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
        $filename = $this->shownName();

        return $escape ? htmlspecialchars($filename, ENT_COMPAT, 'UTF-8') : $filename;
    }

    /**
     * The name of the file for the users: the name of the upload for a protected file, and for a file with a random name on the
     * disk (the file of an anonymous message, see save()). The forum showed that random name without protection (the default):
     * 32 hexadecimal characters, and the download had no extension.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected function shownName(): string
    {
        if ($this->protected || ($this->filename_real && preg_match('/^[0-9a-f]{32}$/', (string) $this->filename))) {
            return (string) $this->filename_real;
        }

        return (string) $this->filename;
    }

    /**
     * @return string|null
     *
     * @since   Kunena 4.0
     * @throws Exception
     */
    public function getImageLink(): ?string
    {
        return $this->isImage()
            ? (string) KunenaLayout::factory('Attachment/Item')->set('attachment', $this)->setLayout('image') : null;
    }

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
        if ($thumb) {
            $path = JPATH_ROOT . "/{$this->folder}/thumb/{$this->filename}";
            $path = is_file($path) ? $path : false;
        } else {
            $path = JPATH_ROOT . "/{$this->folder}/{$this->filename}";
            $path = is_file($path) ? $path : false;
        }

        return $path;
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
        if ($this->shortname === null) {
            $this->shortname = KunenaAttachmentHelper::shortenFileName($this->getFilename(false), (int) $front, (int) $back, $filler);
        }

        return $escape ? htmlspecialchars($this->shortname, ENT_COMPAT, 'UTF-8') : $this->shortname;
    }

    /**
     * Get the table relevant properties. Override for your specific Object
     *
     * @return array    Assocative array with the propertie values of table
     *
     * @since   Kunena 6.4
     */
    protected function getTableProperties(): array
    {
        $properties = [
            'id'            => $this->id,
            'mesid'         => $this->mesid,
            'userid'        => $this->userid,
            'protected'     => $this->protected,
            'hash'          => $this->hash,
            'size'          => $this->size,
            'folder'        => $this->folder,
            'filetype'      => $this->filetype,
            'filename'      => $this->filename,
            'filename_real' => $this->filename_real,
            // 191 characters in the database (a longer caption was an error of the upload).
            'caption' => mb_substr((string) $this->caption, 0, 191),
            'inline'  => $this->inline,
            'status'  => $this->status,
        ];

        return $properties;
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
     * Get URL pointing to the KunenaAttachment.
     *
     * @param   bool  $thumb   thumb
     * @param   bool  $inline  inline
     * @param   bool  $escape  escape
     *
     * @return  string
     *
     * @throws  Exception
     * @since   Kunena 4.0
     */
    public function getUrl($thumb = false, $inline = true, $escape = true): string
    {
        $protect = (bool) KunenaConfig::getInstance()->attachmentProtection;

        // Use direct URLs to the KunenaAttachments if protection is turned off and file wasn't protected (a private file always
        // goes through the forum, which checks who may read it; the file of an anonymous message too: its folder names the user).
        if (!$protect && !((int) $this->protected & self::PROTECTION_PRIVATE) && !$this->isOfAnonymousMessage()) {
            $file      = $this->folder . '/' . $this->filename;
            $fileThumb = $this->folder . '/thumb/' . $this->filename;

            if (!is_file(JPATH_ROOT . '/' . $fileThumb)) {
                $fileThumb = $file;
            }

            $url = ($thumb ? $fileThumb : $file);

            if (!Factory::getApplication()->isClient('administrator')) {
                $url = Uri::base() . $url;
            }

            return $escape ? htmlspecialchars($url, ENT_COMPAT, 'UTF-8') : $url;
        }

        // Route KunenaAttachment through Kunena.
        $thumb    = $thumb ? '&thumb=1' : '';
        $download = $inline ? '' : '&download=1';

        $url = KunenaRoute::_("index.php?option=com_kunena&view=attachment&id={$this->id}{$thumb}{$download}&format=raw", $escape);

        if (Factory::getContainer()->get(SiteApplication::class)->get('sef_suffix')) {
            $url = preg_replace('/\.html/', '', $url);
        }

        return $url;
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

        if ($path && $this->isImage()) {
            $imageProperties = Image::getImageFileProperties($path);
            $this->width     = $imageProperties->width;
            $this->height    = $imageProperties->height;
        } else {
            $this->width = $this->height = 0;
        }
    }

    /**
     * Check if KunenaAttachment is audio.
     *
     * @return  boolean  True if KunenaAttachment is image.
     *
     * @since  Kunena 5.1
     */
    public function isAudio(): bool
    {
        return stripos((string) $this->filetype, 'audio/') !== false;
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
    public function isAuthorised($action = 'read', ?KunenaUser $user = null)
    {
        return !$this->tryAuthorise($action, $user, false);
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
        return stripos((string) $this->filetype, 'image/') !== false;
    }

    /**
     * Check if KunenaAttachment is inline.
     *
     * @return  boolean  True if KunenaAttachment is inline.
     *
     * @since  Kunena 5.1.9
     */
    public function isInline(): bool
    {
        if ($this->inline) {
            return true;
        }

        return false;
    }

    /**
     * Say if the attachment belong to the user
     *
     * @return boolean
     * @since   Kunena 6.3
     */
    public function isMyOwnPrivateAttachment()
    {
        if ($this->protected == 32) {
            $me       = KunenaUserHelper::getMyself();

            if ($this->userid == $me->userid) {
                return true;
            }
        }
    }

    /**
     * Check if KunenaAttachment is pdf.
     *
     * @return  boolean  True if KunenaAttachment is pdf.
     *
     * @since  Kunena 5.1
     */
    public function isPdf(): bool
    {
        return stripos((string) $this->filetype, 'application/pdf') !== false;
    }

    /**
     * Check if KunenaAttachment is audio.
     *
     * @return  boolean  True if KunenaAttachment is image.
     *
     * @since  Kunena 5.1
     */
    public function isVideo(): bool
    {
        return stripos((string) $this->filetype, 'video/') !== false;
    }

    /**
     * Remove the BBCode [attachment=attachmentID][/attachment] from text message
     *
     * @param   string|null  $editor_text  editor text
     *
     * @return string
     *
     * @since   Kunena 6.0
     */
    public function removeBBCodeInMessage(?string $editor_text = null): string
    {
        if (!$this->inline) {
            return false;
        }

        if (empty($editor_text)) {
            return false;
        }

        $find    = ['/\[attachment=' . $this->id . '\](.*?)\[\/attachment\]/su'];
        $replace = '';

        return preg_replace($find, $replace, $editor_text);
    }

    /**
     * Is the attachment a file of an anonymous message: the message has no user, the file keeps the user who uploaded it. Its
     * folder (media/kunena/attachments/<user>) and the name of the upload would tell who wrote the message.
     *
     * @return  boolean
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function isOfAnonymousMessage(): bool
    {
        if (!(int) $this->userid || !(int) $this->mesid) {
            return false;
        }

        $message = $this->getMessage();

        return $message->exists() && !(int) $message->userid;
    }

    /**
     * Save the attachment. The file of a private attachment gets a name which nobody can guess: without the protection of the
     * attachments the site serves their folder, and the name of the upload gave the private file to anybody who tried it. The
     * file of an anonymous message too: the name of the upload in the folder of a user told who wrote the message.
     *
     * @return  boolean
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function save()
    {
        $old    = null;
        $folder = JPATH_ROOT . '/' . $this->folder;

        if ((((int) $this->protected & self::PROTECTION_PRIVATE) || $this->isOfAnonymousMessage()) && $this->filename && !preg_match('/^[0-9a-f]{32}$/', (string) $this->filename) && is_file($folder . '/' . $this->filename)) {
            $new = KunenaAttachmentHelper::getAvailableFilename((string) $this->folder, '', '', true);

            if (rename($folder . '/' . $this->filename, $folder . '/' . $new)) {
                if (is_file($folder . '/thumb/' . $this->filename)) {
                    rename($folder . '/thumb/' . $this->filename, $folder . '/thumb/' . $new);
                }

                $old = (string) $this->filename;

                if (!$this->filename_real) {
                    $this->filename_real = $old;
                }

                $this->filename = $new;
            }
        }

        $error = null;

        try {
            $saved = parent::save();
        } catch (Exception $e) {
            $saved = false;
            $error = $e;
        }

        // The file keeps the name of the row when the row was not saved.
        if (!$saved && $old !== null) {
            rename($folder . '/' . $this->filename, $folder . '/' . $old);

            if (is_file($folder . '/thumb/' . $this->filename)) {
                rename($folder . '/thumb/' . $this->filename, $folder . '/thumb/' . $old);
            }

            $this->filename = $old;
        }

        if ($error) {
            throw $error;
        }

        return $saved;
    }

    /**
     * Set KunenaAttachment file.
     *
     * Copies the KunenaAttachment into proper location and makes sure that all the unset fields get properly assigned.
     *
     * @param   string          $source     Absolute path to the upcoming KunenaAttachment.
     * @param   string|null     $basename   Filename without extension.
     * @param   string|null     $extension  File extension.
     * @param   bool            $unlink     Whether to delete the original file or not.
     * @param   bool            $overwrite  If not allowed, throw exception if the file exists.
     *
     * @return  boolean
     *
     * @throws Exception
     * @since   Kunena 4.0
     */
    public function saveFile(string $source, $basename = null, $extension = null, $unlink = false, $overwrite = false): bool
    {
        if (!is_file($source)) {
            throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '(): KunenaAttachment file not found.');
        }

        // Hash, size and MIME are set during saving, so let's deal with all other variables.
        // The id of the user (a KunenaUser is no text: the folder below would fail on it).
        $this->userid    = \is_null($this->userid) ? (int) KunenaUserHelper::getMyself()->userid : (int) $this->userid;
        $this->folder    = \is_null($this->folder) ? "media/kunena/attachments/{$this->userid}" : $this->folder;
        $this->protected = KunenaConfig::getInstance()->attachmentProtection ? 1 : 0;
        $this->status    = \is_null($this->status) ? 0 : $this->status;

        if (!$this->filename_real) {
            $this->filename_real = $this->filename;
        }

        // The column of the real name has 190 characters: a longer name keeps its start and its extension.
        if (mb_strlen((string) $this->filename_real) > 190) {
            $realExtension       = pathinfo((string) $this->filename_real, PATHINFO_EXTENSION);
            $realExtension       = mb_strlen($realExtension) < 20 ? '.' . $realExtension : '';
            $this->filename_real = mb_substr((string) $this->filename_real, 0, 190 - mb_strlen($realExtension)) . $realExtension;
        }

        if (!$this->filename || $this->filename == $this->filename_real) {
            if (!$basename || !$extension) {
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
        if (!is_dir(JPATH_ROOT . "/{$this->folder}") && !Folder::create(JPATH_ROOT . "/{$this->folder}")) {
            throw new RuntimeException(Text::_('Failed to create KunenaAttachment directory.'));
        }

        $destination = JPATH_ROOT . "/{$this->folder}/{$this->filename}";

        // Move the file into the final location (if not already in there).
        if ($source != $destination) {
            // Create target directory if it does not exist.
            if (!$overwrite && is_file($destination)) {
                throw new RuntimeException(Text::sprintf('Attachment %s already exists.', $this->filename_real));
            }

            if ($unlink) {
                @chmod($source, 0644);
            }

            $success = File::copy($source, $destination);

            // Create index.hml into the user folder
            $content = '<html><body></body></html>';
            File::write(JPATH_ROOT . "/media/kunena/attachments/{$this->userid}/index.html", $content);

            if (!$success) {
                throw new RuntimeException(Text::sprintf('COM_KUNENA_UPLOAD_ERROR_NOT_MOVED', $destination));
            }

            KunenaPath::setPermissions($destination);

            if ($unlink) {
                unlink($source);
            }
        }

        return $this->save();
    }

    /**
     * Tell the plugins of the kunena group that this attachment was uploaded and saved (onKunenaAfterAttachmentUpload).
     *
     * Nothing is read back, and an exception or error of a listener is logged, never thrown: a listener cannot fail the upload.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public function dispatchAfterUpload(): void
    {
        if (!$this->exists() || !$this->id) {
            return;
        }

        try {
            PluginHelper::importPlugin('kunena');

            $event = new KunenaAfterAttachmentUploadEvent('onKunenaAfterAttachmentUpload', ['subject' => $this]);
            Factory::getApplication()->getDispatcher()->dispatch('onKunenaAfterAttachmentUpload', $event);
        } catch (Throwable $e) {
            Log::add($e->getMessage(), Log::WARNING, 'kunena');
        }
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
    public function tryAuthorise($action = 'read', ?KunenaUser $user = null, $throw = true)
    {
        // Special case to ignore authorisation.
        if ($action == 'none') {
            return false;
        }

        // Load user if not given.
        if ($user === null) {
            $user = KunenaUserHelper::getMyself();
        }

        // Unknown action - throw invalid argument exception.
        if (!isset(self::$actions[$action])) {
            throw new InvalidArgumentException(Text::sprintf('COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action), 500);
        }

        // Start by checking if KunenaAttachment is protected.
        $exception = !$this->protected
        ? null : new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), $user->id ? 403 : 401);

        // A public file and a file of the access of the message: who may read the message may read the file.
        // The levels are bits: "&", not "&&" (with "&&" every protected file, also a private one, was given to every reader of
        // the message).
        if ($exception && $this->mesid && ((int) $this->protected & (self::PROTECTION_PUBLIC | self::PROTECTION_ACL))) {
            // Load message authorisation.
            $exception = $this->getMessage()->tryAuthorise('attachment.' . $action, $user, false);
        }

        // A file for the friends of its author: the users whom the author follows (or the friends of the profile integration,
        // KunenaProfile::isFriend()), who may read its message.
        if ($exception && ((int) $this->protected & self::PROTECTION_FRIENDS) && $user->exists() && $this->userid) {
            $friend = KunenaFactory::getProfile()->isFriend((int) $this->userid, (int) $user->userid);

            if ($friend && (!$this->mesid || $this->getMessage()->isAuthorised('read', $user))) {
                $exception = null;
            }
        }

        // A file for the moderators or the administrators: the moderators (or administrators) of the category of its message, the
        // global ones for a file without a message.
        if ($exception && ((int) $this->protected & (self::PROTECTION_MODERATORS | self::PROTECTION_ADMINS))) {
            $category = $this->mesid ? $this->getMessage()->getCategory() : null;
            $allowed  = ((int) $this->protected & self::PROTECTION_MODERATORS) ? $user->isModerator($category) : $user->isAdmin($category);

            if ($user->exists() && $allowed) {
                $exception = null;
            }
        }

        // Check if KunenaAttachment is private.
        if ($exception && ((int) $this->protected & self::PROTECTION_PRIVATE)) {
            $exception = $this->authorisePrivate($user, $action);

            // A user of the private part who may read its message (as a file for the friends): the recipient got the file of a reply
            // which waited for approval, was deleted, or was moved into a category which he may not read.
            if (!$exception && $this->mesid && !$this->getMessage()->isAuthorised('read', $user)) {
                $exception = new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), $user->userid ? 403 : 401);
            }
        }

        // Check author access: the user who uploaded a protected file may always read it.
        if ($exception && $this->protected) {
            $exception = $user->exists() && $user->id == $this->userid
            ? null : new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), $user->userid ? 403 : 401);
        }

        if ($exception) {
            // Hide original exception behind no access.
            $exception = new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), $user->userid ? 403 : 401, $exception);
        } else {
            // Check authorisation action.
            foreach (self::$actions[$action] as $function) {
                $authFunction = 'authorise' . $function;

                try {
                    $exception = $this->$authFunction($user);

                    if ($exception) {
                        break;
                    }
                } catch (Exception $e) {
                    // A failing check must not grant access.
                    $exception = new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ATTACHMENT_NO_ACCESS'), $user->userid ? 403 : 401, $e);

                    break;
                }
            }
        }

        // Throw or return the exception.
        if ($throw && $exception) {
            throw $exception;
        }

        return $exception;
    }

    /**
     * @param   string    $key    key
     * @param   int|null  $catid  catid
     *
     * @return bool
     *
     * @throws Exception
     * @since   Kunena 4.0
     */
    public function upload(string $key = 'kattachment', ?int $catid = null): bool
    {
        $config    = KunenaFactory::getConfig();
        $input     = Factory::getApplication()->getInput();
        $fileInput = $input->files->get($key, null, 'raw');

        // No upload is allowed to this user here (a guest with the defaults): the list of the extensions is false, which the upload
        // did not take (a TypeError, the post was lost on an error page).
        $extensions = KunenaAttachmentHelper::getExtensions($catid, $this->userid);

        if ($extensions === false) {
            throw new RuntimeException(Text::_('COM_KUNENA_UPLOAD_ERROR_NOT_UPLOADED'), 403);
        }

        $upload = KunenaUpload::getInstance($extensions);

        // The folder of the files of the user: named by the id of the user, a number.
        $userFolder     = 'media/kunena/attachments/' . (int) $this->userid;
        $uploadBasePath = JPATH_ROOT . '/' . $userFolder . '/';

        if (!is_dir($uploadBasePath)) {
            mkdir($uploadBasePath);
        }

        $upload->splitFilename($fileInput['name']);

        // What the user has called the file: this is shown, and never a path of the server.
        $clientName = KunenaAttachmentHelper::getClientFilename($fileInput['name']);
        $protected  = (bool) $config->attachmentProtection;

        $fileInput['name'] = preg_replace('/[[:space:]]/', '', $fileInput['name']);

        $fileNameWithoutExt = File::stripExt($fileInput['name']);
        $fileNameWithoutExt = strtolower($fileNameWithoutExt);

        $fileExt = File::getExt($fileInput['name']);
        $fileExt = strtolower($fileExt);

        // Only the last dot starts the extension, as in the upload of the editor (getAvailableFilename()): a name as x.php.txt was
        // stored as it is, and some web servers run such a file as a script.
        $fileNameWithoutExt = str_replace('.', '_', $fileNameWithoutExt);
        $fileNameWithExt    = $fileNameWithoutExt . '.' . $fileExt;

        if ($protected) {
            // The file is served by Kunena after the permissions were checked, so nobody may be able to guess its address:
            // the name on the disk is random, as it is for the files of the ajax upload.
            $fileNameWithoutExt = KunenaAttachmentHelper::getAvailableFilename($userFolder, $fileNameWithoutExt, $fileExt, true);
            $fileNameWithExt    = $fileNameWithoutExt . '.' . $fileExt;
        } elseif (file_exists($uploadBasePath . $fileNameWithExt)) {
            // The name which is stored (lowercase, the dots of the name changed): the name of the request was checked, so a file of
            // the same stored name was overwritten (on a file system which tells the case apart, Photo.JPG overwrote photo.jpg).
            $base = $fileNameWithoutExt;

            for ($i = 2; file_exists($uploadBasePath . $base . "-$i." . $fileExt); $i++) {
            }

            $fileNameWithoutExt = $base . "-$i";
            $fileNameWithExt    = $fileNameWithoutExt . '.' . $fileExt;
        }

        $fileInput['name'] = $fileNameWithExt;

        $file = $upload->upload($fileInput, $uploadBasePath . $fileNameWithoutExt);

        if ($file->success) {
            if (\extension_loaded('fileinfo')) {
                $finfo = new finfo(FILEINFO_MIME);

                $type = $finfo->file($uploadBasePath . $fileNameWithExt);
            } else {
                throw new RuntimeException("Fileinfo extension not loaded.");
            }

            // A file which is refused below is taken away again. It stayed in the folder of the user: no attachment named it,
            // and with the protection of the attachments off the web server gave it to anybody who had its address.
            $stored = $uploadBasePath . $fileNameWithExt;

            // The options "Check MIME Types" and "Legal MIME Types", as in the upload of the editor: an image whose real type is
            // not in the list is refused (nothing read the two options).
            if (KunenaUpload::mimeRefused($fileNameWithExt, (string) $type, (bool) $config->checkMimeTypes, (string) $config->imageMimeTypes, (string) $config->imageTypes)) {
                @unlink($stored);

                return false;
            }

            if (stripos($type, 'image/') !== false) {
                try {
                    $imageInfo = KunenaImage::getImageFileProperties($stored);
                } catch (Exception $e) {
                    // $imageInfo is not set: go on and the next lines would read a property of nothing.
                    @unlink($stored);

                    return false;
                }

                // A picture whose pixels do not fit in the memory of PHP is no attachment (decoded, it ended the request).
                if (!KunenaImage::canDecode((int) $imageInfo->width, (int) $imageInfo->height)) {
                    @unlink($stored);

                    return false;
                }

                // A limit of 0 is no limit, as for the uploader of the editor: the picture was enlarged to the other limit, or the
                // upload ended in a division by zero (both 0).
                $maxWidth  = (int) $config->imageWidth > 0 ? (int) $config->imageWidth : (int) $imageInfo->width;
                $maxHeight = (int) $config->imageHeight > 0 ? (int) $config->imageHeight : (int) $imageInfo->height;

                if ($file->size / 1024 > (float) $config->imageSize || $imageInfo->width > $maxWidth || $imageInfo->height > $maxHeight) {
                    // Calculate quality for both JPG and PNG.
                    $quality = $config->imageQuality;

                    if ($quality < 1 || $quality > 100) {
                        $quality = 70;
                    }

                    if ($imageInfo->type == IMAGETYPE_PNG) {
                        $quality = \intval(($quality - 1) / 10);
                    }

                    $options = ['quality' => $quality];

                    try {
                        $image = new KunenaImage($uploadBasePath . $fileNameWithExt);
                        $image = $image->resize($maxWidth, $maxHeight, false);
                        $image->toFile($uploadBasePath . $fileNameWithExt, $imageInfo->type, $options);
                        unset($image);
                    } catch (Exception $e) {
                        @unlink($stored);

                        return false;
                    }
                }

                $this->filetype = $imageInfo->mime;
            }

            // A number, as the column: false was given to the query as '', and the post failed ("Incorrect integer value") whenever
            // the attachments were not protected (the default).
            $this->protected     = $protected ? 1 : 0;
            $this->hash          = md5_file($stored);
            // The size of the file as it is stored: a picture which was made smaller kept the size of the upload.
            clearstatcache(true, $stored);
            $this->size          = (int) (filesize($stored) ?: $file->size);
            $this->folder        = $userFolder;
            $this->filename      = $fileInput['name'];
            $this->filename_real = $clientName !== '' ? $clientName : $fileInput['name'];
            $this->caption       = '';
            $this->inline        = 0;
            $this->status        = 0;

            return true;
        }

        return false;
    }
}
