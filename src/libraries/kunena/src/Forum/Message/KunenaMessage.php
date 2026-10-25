<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Message
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Message;

\defined('_JEXEC') or die();

use Exception;
use InvalidArgumentException;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\Mail;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserHelper;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Event\DispatcherInterface;
use Joomla\Filesystem\File;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Email\KunenaEmail;
use Kunena\Forum\Libraries\Email\KunenaEmailUnsubscribe;
use Kunena\Forum\Libraries\Email\KunenaMailTemplate;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Event\KunenaDeleteEvent;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Category\User\KunenaCategoryUserHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\KunenaMessageThankyouHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\KunenaTopicUser;
use Kunena\Forum\Libraries\Forum\Topic\User\Read\KunenaTopicUserReadHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Integration\KunenaPrivate;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserFollow;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserIgnore;
use Kunena\Forum\Libraries\User\KunenaUserName;
use StdClass;

/**
 * Class \Kunena\Forum\Libraries\Forum\Message\Message
 *
 * @property int    $parent
 * @property int    $thread
 * @property int    $catid
 * @property string $name
 * @property int    $userid
 * @property string $email
 * @property string $subject
 * @property int    $time
 * @property string $ip
 * @property int    $topic_emoticon
 * @property int    $locked
 * @property int    $hold
 * @property int    $ordering
 * @property int    $hits
 * @property int    $moved
 * @property int    $deleted_time
 * @property int    $modified_by
 * @property string $modified_time
 * @property string $modified_reason
 * @property string $message
 * @since   Kunena 6.0
 */
class KunenaMessage extends KunenaDatabaseObject
{
    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected static mixed $actions = [
        'none'                   => [],
        'read'                   => ['Read'],
        'reply'                  => ['Read', 'NotHold', 'GuestWrite'],
        'edit'                   => ['Read', 'Own', 'EditTime'],
        'move'                   => ['Read'],
        'approve'                => ['Read'],
        'delete'                 => ['Read', 'Own', 'Delete'],
        'thankyou'               => ['Read', 'Thankyou'],
        'unthankyou'             => ['Read'],
        'undelete'               => ['Read'],
        'permdelete'             => ['Read', 'Permdelete'],
        'attachment.read'        => ['Read'],
        'attachment.createimage' => ['Read', 'AttachmentsImage'],
        'attachment.createfile'  => ['Read', 'AttachmentsFile'],
        'attachment.delete'      => [],
        'attachment.private'     => [],
    ];

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    public $id = null;

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    public mixed $thankyou = [];

    public mixed $replynum = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $typeAlias = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $parent = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $thread = null;

    /**
     * @var     integer
     * @since   Kunena 6.4
     */
    public mixed $catid = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $name = null;

    /**
     * @var     integer
     * @since   Kunena 6.4
     */
    public $userid;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $email = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $subject = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $time = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $ip = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $topic_emoticon = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $locked = null;

    /**
     * @var     integer
     * @since   Kunena 6.4
     */
    public mixed $hold = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $ordering = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $hits = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $moved = null;

    /**
     * @var     integer
     * @since   Kunena 7.0
     */
    public mixed $deleted_time = null;

    /**
     * @var     integer
     * @since   Kunena 6.4
     */
    public mixed $modified_by = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $modified_time = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $modified_reason = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $params = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public $message;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $pm = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $_table = 'KunenaMessages';

    /**
     * @var     DatabaseDriver|null
     * @since   Kunena 6.0
     */
    protected mixed $_db = null;

    /**
     * @var     KunenaConfig|null
     * @since   Kunena 7.1
     */
    protected mixed $_config = null;

    /**
     * @var     KunenaAttachment[]
     * @since   Kunena 6.0
     */
    protected $_attachments_add = [];

    /**
     * @var     KunenaAttachment[]
     * @since   Kunena 6.0
     */
    protected mixed $_attachments_del = [];

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    protected $_topic = null;

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    protected mixed $_hold = 1;

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    protected mixed $_thread = 0;

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $_authcache = [];

    /**
     * @var boolean
     * @since Kunena 5.2
     */
    protected mixed $_approved = false;

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $_authtcache = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $_authfcache = [];

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $urlNotification = null;

    /**
     * @param   mixed  $properties  properties
     *
     * @throws  Exception
     * @since   Kunena 6.0
     *
     * @internal
     */
    public function __construct($properties = null)
    {
        $this->_db     = Factory::getContainer()->get('DatabaseDriver');
        $this->_config = KunenaFactory::getConfig();

        parent::__construct($properties);
    }

    /**
     * Destruct
     *
     * @since   Kunena 6.0
     */
    public function __destruct()
    {
        unset($this->_db, $this->_topic);

    }

    /**
     * Add listed attachments to the message.
     *
     * If attachment is already pointing to the message, this function has no effect.
     * Currently only orphan attachments can be added.
     *
     * @param   array  $ids  ids
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 4.0
     */
    public function addAttachments(array $ids): void
    {
        $attachments = $this->getAttachments($ids, 'none');

        // A file which is in no message yet is the upload of a user: only his own uploads go into his message (the numbers come
        // from the form, and an upload of somebody else would be taken away from him).
        $me = KunenaUserHelper::getMyself();

        foreach ($attachments as $id => $attachment) {
            if (!$attachment->mesid && (int) $attachment->userid !== (int) $me->userid) {
                unset($attachments[$id]);
            }
        }

        $this->_attachments_add += $attachments;
    }

    /**
     * Add the headers which describe the message (X-Forum-*) and the ones which let the mail program thread the e-mails
     * of a topic (Message-ID, In-Reply-To and References), so filters and threads can be made.
     *
     * @param   Mail  $mail  The mail.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    protected function addForumHeaders(Mail $mail): void
    {
        // On by default: the option is not in the stored configuration until it has been saved once.
        if (isset($this->_config->emailForumHeaders) && !$this->_config->emailForumHeaders) {
            return;
        }

        $topic    = $this->getTopic();
        $category = $this->getCategory();

        // A header is one line, and a value with other characters than ASCII is encoded.
        $clean = static function ($value) use ($mail): string {
            $value = trim(preg_replace('/\s+/', ' ', strip_tags((string) $value)));

            return $mail->encodeHeader(MailHelper::cleanLine($value));
        };

        $headers = [
            'X-Forum'             => 'kunena',
            'X-Forum-URL'         => Uri::root(),
            'X-Forum-Name'        => $this->_config->boardTitle,
            'X-Forum-Category'    => $category->name,
            'X-Forum-Category-Id' => (int) $category->id,
            'X-Forum-Topic'       => $topic->subject,
            'X-Forum-Topic-Id'    => (int) $topic->id,
            'X-Forum-Post-Id'     => (int) $this->id,
            'X-Forum-Type'        => $this->parent ? 'reply' : 'new-topic',
            'X-Forum-Who'         => $this->getAuthor()->getName($this->name),
        ];

        foreach ($headers as $name => $value) {
            $mail->addCustomHeader($name, $clean($value));
        }

        // The threading: the first message of a topic starts the thread, a reply points to the message it answers and to the start.
        $firstPostId     = (int) $topic->first_post_id;
        $mail->MessageID = $this->getEmailMessageId((int) $this->id, (int) $topic->id, $firstPostId);

        if ($this->parent && $firstPostId && (int) $this->id !== $firstPostId) {
            $start  = $this->getEmailMessageId($firstPostId, (int) $topic->id, $firstPostId);
            $parent = $this->getEmailMessageId((int) $this->parent, (int) $topic->id, $firstPostId);

            $mail->addCustomHeader('In-Reply-To', $parent);
            $mail->addCustomHeader('References', $parent === $start ? $start : $start . ' ' . $parent);
        }
    }

    /**
     * The headers of the e-mail of a notification (the unsubscribe headers and the headers of the forum).
     *
     * @param   Mail   $mail         The mail.
     * @param   int    $subscription 1 for a subscriber, 0 for a moderator.
     * @param   int    $recipientId  The user who gets the mail.
     * @param   bool   $follower     The recipient follows the author.
     *
     * @return  string  The address to unsubscribe (empty when the mail has none).
     *
     * @since   Kunena 8.0
     */
    protected function addNotificationHeaders(Mail $mail, int $subscription, int $recipientId, bool $follower): string
    {
        // Subscribers can unsubscribe from the email itself: a reply is a topic subscription, a new topic a category subscription.
        $unsubscribeUrl = '';

        if ($subscription && $recipientId > 0) {
            $type = $this->parent ? KunenaEmailUnsubscribe::TOPIC : KunenaEmailUnsubscribe::CATEGORY;
            $id   = $this->parent ? (int) $this->thread : (int) $this->catid;

            if ($follower) {
                // The recipient gets this e-mail because he follows the author.
                $type = KunenaEmailUnsubscribe::FOLLOW;
                $id   = (int) $this->userid;
            }

            $unsubscribeUrl = KunenaEmailUnsubscribe::getUrl($type, $id, $recipientId);

            // One-click unsubscribe of mail programs (RFC 8058).
            $mail->addCustomHeader('List-Unsubscribe', '<' . KunenaEmailUnsubscribe::getPostUrl($type, $id, $recipientId) . '>');
            $mail->addCustomHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        }

        $this->addForumHeaders($mail);

        return $unsubscribeUrl;
    }

    /**
     * @param   Mail    $mail          mail
     * @param   int     $subscription  subscription
     * @param   string  $subject       subject
     * @param   string  $url           url
     * @param   bool    $once          once
     * @param   int     $recipientId   The user who gets the mail.
     * @param   bool    $follower      The recipient follows the author.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function attachEmailBody(Mail $mail, int $subscription, string $subject, string $url, bool $once, int $recipientId = 0, bool $follower = false): void
    {
        $unsubscribeUrl = $this->addNotificationHeaders($mail, $subscription, $recipientId, $follower);

        $layout = KunenaLayout::factory('Email/Subscription')->debug(false)
            ->set('mail', $mail)
            ->set('message', $this)
            ->set('messageUrl', $url)
            ->set('once', $once)
            ->set('unsubscribeUrl', $unsubscribeUrl);

        $msg = '';

        try {
            $msg = trim($layout->render($subscription ? 'default' : 'moderator'));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $mail->setBody($msg);
    }

    /**
     * Check if user has the right to upload file attachment
     *
     * @param   KunenaUser  $user  user
     *
     * @return  KunenaExceptionAuthorise|void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function authoriseAttachmentsFile(KunenaUser $user)
    {
        if (empty($this->_config->fileUpload)) {
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_NOT_ALLOWED'), 403);
        }

        if ($this->_config->fileUpload == 'admin') {
            if (!$user->isAdmin()) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_FILE_ONLY_FOR_ADMINISTRATORS'), 403);
            }
        }

        if ($this->_config->fileUpload == 'registered') {
            if (!$user->userid) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_FILE_ONLY_FOR_REGISTERED_USERS'), 403);
            }
        }

        if ($this->_config->fileUpload == 'moderator') {
            $category = $this->getCategory();

            if (!$user->isModerator($category)) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_FILE_ONLY_FOR_MODERATORS'), 403);
            }
        }

        return;
    }

    /**
     * Check if user has the right to upload image attachment
     *
     * @param   KunenaUser  $user  user
     *
     * @return  KunenaExceptionAuthorise|void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function authoriseAttachmentsImage(KunenaUser $user)
    {
        if (empty($this->_config->imageUpload)) {
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_NOT_ALLOWED'), 403);
        }

        if ($this->_config->imageUpload == 'admin') {
            if (!$user->isAdmin()) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_IMAGE_ONLY_FOR_ADMINISTRATORS'), 403);
            }
        }

        if ($this->_config->imageUpload == 'registered') {
            if (!$user->userid) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_IMAGE_ONLY_FOR_REGISTERED_USERS'), 403);
            }
        }

        if ($this->_config->imageUpload == 'moderator') {
            $category = $this->getCategory();

            if (!$user->isModerator($category)) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_IMAGE_ONLY_FOR_MODERATORS'), 403);
            }
        }

        return;
    }

    /**
     * @param   KunenaUser  $user  user
     *
     * @return  KunenaExceptionAuthorise|void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function authoriseDelete(KunenaUser $user)
    {

        if (!$user->isModerator($this->getCategory()) && $this->_config->userDeleteMessage != '2') {
            // Never
            if ($this->_config->userDeleteMessage == '0') {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
            }

            // When no replies
            if ($this->_config->userDeleteMessage == '1' && ($this->getTopic()->first_post_id != $this->id || $this->getTopic()->getReplies())) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
            }

            // All except the first message of the topic
            if ($this->_config->userDeleteMessage == '3' && $this->id == $this->getTopic()->first_post_id) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_ONLY_FIRST_MESSAGE'), 403);
            }

            // Only the last message
            if ($this->_config->userDeleteMessage == '4' && $this->id != $this->getTopic()->last_post_id) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_ONLY_LAST_MESSAGE'), 403);
            }
        }
    }

    /**
     * This function check the edit time from the author of the post and return if the user is allowed to edit his post.
     *
     * @param   KunenaUser  $user  user
     *
     * @return  KunenaExceptionAuthorise|boolean|void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function authoriseEditTime(KunenaUser $user)
    {
        // Do not perform rest of the checks to moderators and admins
        if ($user->isModerator($this->getCategory())) {
            return false;
        }

        // User is only allowed to edit post within time specified in the configuration

        if (\intval($this->_config->userEdit) != 1) {
            // Edit never allowed
            if (\intval($this->_config->userEdit) == 0) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
            }

            // Edit allowed if replies
            if (\intval($this->_config->userEdit) == 2 && $this->getTopic()->getReplies()) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_ALLOWED_IF_REPLIES'), 403);
            }

            // Edit allowed for the first message of the topic
            if (\intval($this->_config->userEdit) == 4 && $this->id != $this->getTopic()->first_post_id) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_ALLOWED_ONLY_FIRST_MESSAGE_OF_TOPIC'), 403);
            }

            // Edit allowed for the last message of the topic
            if (\intval($this->_config->userEdit) == 3 && $this->id != $this->getTopic()->last_post_id) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_ALLOWED_ONLY_LAST_MESSAGE_OF_TOPIC'), 403);
            }
        }

        if (\intval($this->_config->userEditTime) != 0) {
            // Check whether edit is in time
            $modtime = $this->modified_time ? $this->modified_time : $this->time;

            if ($modtime + \intval($this->_config->userEditTime) < Factory::getDate()->toUnix() && \intval($this->_config->userEditTimeGrace) == 0) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
            } elseif (\intval($this->_config->userEditTimeGrace) != 0 && $modtime + \intval($this->_config->userEditTime) + \intval($this->_config->userEditTimeGrace) < Factory::getDate()->toUnix()) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
            }
        }

        return;
    }

    /**
     * @param   KunenaUser  $user  user
     *
     * @return  KunenaExceptionAuthorise|void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function authoriseGuestWrite(KunenaUser $user)
    {
        // Check if user is guest and they can create or reply topics
        if ($user->userid == 0 && !$this->_config->pubWrite) {
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN'), 401);
        }

        return;
    }

    /**
     * @param   KunenaUser  $user  user
     *
     * @return  KunenaExceptionAuthorise|void
     *
     * @since   Kunena 6.0
     */
    protected function authoriseNotHold(KunenaUser $user)
    {
        if ($this->hold) {
            // Nobody can reply to unapproved or deleted post
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 403);
        }

        return;
    }

    /**
     * @param   KunenaUser  $user  user
     *
     * @return  KunenaExceptionAuthorise|void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function authoriseOwn(KunenaUser $user)
    {
        // Guests cannot own posts.
        if (!$user->exists()) {
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 401);
        }

        // Check that topic owned by the user or user is a moderator
        // TODO: check #__kunena_user_topics
        if ($this->userid != $user->userid && !$user->isModerator($this->getCategory())) {
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
        }

        return;
    }

    /**
     * Check if user has the right to perm delete the message
     *
     * @param   KunenaUser  $user  user
     *
     * @return  KunenaExceptionAuthorise|bool|void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function authorisePermdelete(KunenaUser $user)
    {

        if ($user->isAdmin() || $user->isModerator()) {
            return false;
        }

        if ($user->isModerator($this->getTopic()->getCategory()) && !$this->_config->moderatorPermDelete || !$user->isModerator($this->getTopic()->getCategory())) {
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
        }

        return;
    }

    /**
     * @param   KunenaUser  $user  user
     *
     * @return  KunenaExceptionAuthorise|void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function authoriseRead(KunenaUser $user)
    {
        // A message of a topic the user can not read, in a category where the users only see their own topics.
        if ($this->getCategory()->params->get('ownTopicsOnly')) {
            $topic = $this->getTopic();

            if ($topic && $topic->exists() && !$topic->isAuthorised('read', $user)) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), $user->exists() ? 403 : 401);
            }
        }

        if ($this->hold && !$user->exists()) {
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 401);
        }

        // Check that user has the right to see the post (user can see his own unapproved posts)
        if ($this->hold > 1 || ($this->hold == 1 && $this->userid != $user->userid)) {
            $access = KunenaAccess::getInstance();
            $hold   = $access->getAllowedHold($user->userid, $this->catid, false);

            if (!\in_array($this->hold, $hold)) {
                return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 403);
            }
        }

        return;
    }

    /**
     * @param   KunenaUser  $user  user
     *
     * @return  KunenaExceptionAuthorise|void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function authoriseThankyou(KunenaUser $user)
    {
        // Check that message is not your own
        if (!$this->_config->showThankYou) {
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_THANKYOU_DISABLED'), 403);
        }

        if (!$user->userid) {
            // TODO: better error message
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_THANKYOU_DISABLED'), 401);
        }

        if (!$this->userid || $this->userid == $user->userid) {
            // TODO: better error message
            return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_THANKYOU_DISABLED'), 403);
        }

        return;
    }

    /**
     * @return  boolean
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function check(): bool
    {
        $author = KunenaUserHelper::get($this->userid);

        // Check username
        if (!$this->userid) {
            $this->name = trim($this->name);

            // Unregistered or anonymous users: Do not allow existing username, nor the real name of a user (the forum can show
            // the real names instead of the usernames, and the post of a guest would look like a post of that user).
            $nicktaken = UserHelper::getUserId($this->name);

            if (!$nicktaken && $this->name !== '') {
                $query = $this->_db->createQuery()
                    ->select($this->_db->quoteName('id'))
                    ->from($this->_db->quoteName('#__users'))
                    ->where('LOWER(' . $this->_db->quoteName('name') . ') = LOWER(' . $this->_db->quote($this->name) . ')');
                $this->_db->setQuery($query, 0, 1);
                $nicktaken = (int) $this->_db->loadResult();
            }

            // Nor the nickname of a user (when the forum shows the nicknames): the name of an anonymous post of a logged-in
            // user is his own name by default, which is his nickname then.
            $nicknameField = KunenaUserName::getDisplayNameField();

            if (!$nicktaken && $this->name !== '' && $nicknameField) {
                $query = $this->_db->createQuery()
                    ->select($this->_db->quoteName('item_id'))
                    ->from($this->_db->quoteName('#__fields_values'))
                    ->where($this->_db->quoteName('field_id') . ' = ' . (int) $nicknameField)
                    ->where('LOWER(' . $this->_db->quoteName('value') . ') = LOWER(' . $this->_db->quote($this->name) . ')');
                $this->_db->setQuery($query, 0, 1);
                $nicktaken = (int) $this->_db->loadResult();
            }

            if (empty($this->name) || $nicktaken) {
                $this->name = Text::_('COM_KUNENA_USERNAME_ANONYMOUS');
            }
        } else {
            $this->name = $author->getName();
        }

        if (!empty($this->email)) {
            // Check email address
            $this->email = trim($this->email);

            // Email address must be valid
            if (!MailHelper::isEmailAddress($this->email)) {
                throw new Exception(Text::sprintf('COM_KUNENA_LIB_MESSAGE_ERROR_EMAIL_INVALID'));
            }
        } elseif (!KunenaUserHelper::getMyself()->exists() && $this->_config->askEmail) {
            throw new Exception(Text::_('COM_KUNENA_LIB_MESSAGE_ERROR_EMAIL_EMPTY'));
        }

        // Do not allow no posting date or dates from the future
        $now = Factory::getDate()->toUnix();

        if (!$this->time || $this->time > $now) {
            $this->time = $now;
        }

        // Do not allow identical posting times inside topic (simplifies logic)
        $topic = $this->getTopic();

        if (!$this->exists() && $topic->exists() && $this->time <= $topic->last_post_time) {
            $this->time = $topic->last_post_time + 1;
        }

        if ($this->modified_time > $now) {
            $this->modified_time = $now;
        }

        if ($this->modified_time && $this->modified_time < $this->time) {
            $this->modified_time = $this->time;
        }

        if ($this->hold < 0 || $this->hold > 3) {
            throw new Exception(Text::_('COM_KUNENA_LIB_MESSAGE_ERROR_HOLD_INVALID'));
        }

        if ($this->modified_by !== null) {
            if (!$this->modified_by) {
                $this->modified_time   = 0;
                $this->modified_reason = '';
            } elseif (!$this->modified_time) {
                $this->modified_time = Factory::getDate()->toUnix();
            }
        }

        // When the message is set to be deleted, set the deleted time to the deleted time
        if ($this->hold === 2) {
            $this->deleted_time = Factory::getDate()->toUnix();
        } else {
            $this->deleted_time = 0;
        }

        // Flood protection

        if ($this->_config->floodProtection && !$this->getCategory()->isAuthorised('moderate') && !$this->exists()) {
            $query = $this->_db->createQuery();
            $query->select('MAX(time)')
                ->from($this->_db->quoteName('#__kunena_messages'))
                ->where($this->_db->quoteName('ip') . ' = ' . $this->_db->quote($this->ip));
            $this->_db->setQuery($query);

            try {
                $lastPostTime = $this->_db->loadResult();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);

                return false;
            }

            if ($lastPostTime + $this->_config->floodProtection > Factory::getDate()->toUnix()) {
                throw new Exception(Text::sprintf('COM_KUNENA_LIB_MESSAGE_ERROR_FLOOD', (int) $this->_config->floodProtection));
            }
        }

        if (!$this->exists() && !$this->getCategory()->isAuthorised('moderate')) {
            // Ignore identical messages (posted within 5 minutes)
            $duplicatetimewindow = Factory::getDate()->toUnix() - 5 * 60;
            $query               = $this->_db->createQuery();
            $query->select('m.id')
                ->from($this->_db->quoteName('#__kunena_messages', 'm'))
                ->innerJoin($this->_db->quoteName('#__kunena_messages_text', 't') . ' ON m.id = t.mesid')
                ->where($this->_db->quoteName('m.userid') . ' = ' . $this->_db->quote($this->userid))
                ->andWhere($this->_db->quoteName('m.ip') . ' = ' . $this->_db->quote($this->ip))
                ->andWhere($this->_db->quoteName('t.message') . ' = ' . $this->_db->quote($this->message))
                ->andWhere($this->_db->quoteName('m.time') . ' >= ' . $this->_db->quote($duplicatetimewindow));
            $this->_db->setQuery($query);

            try {
                $id = $this->_db->loadResult();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);

                return false;
            }

            if ($id) {
                throw new Exception(Text::_('COM_KUNENA_POST_DUPLICATE_IGNORED'));
            }
        }

        return true;
    }

    /**
     * Method to delete the \Kunena\Forum\Libraries\Forum\Message\Message object from the database.
     *
     * @return  boolean  True on success
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function delete(): bool
    {
        if (!$this->exists()) {
            return true;
        }

        if (!parent::delete()) {
            return false;
        }

        $this->hold = 1;

        $attachments = $this->getAttachments();

        foreach ($attachments as $attachment) {
            $file = JPATH_SITE . '/media/kunena/attachments/' . $attachment->userid . '/' . $attachment->filename;
            File::delete($file);

            try {
                $attachment->delete();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        // Delete thank yous
        $query = $this->_db->createQuery();
        $query->delete($this->_db->quoteName('#__kunena_thankyou'))
            ->where($this->_db->quoteName('postid') . ' = ' . $this->_db->quote($this->id));
        $queries[] = $query;

        // Delete message
        $query = $this->_db->createQuery();
        $query->delete($this->_db->quoteName('#__kunena_messages_text'))
            ->where($this->_db->quoteName('mesid') . ' = ' . $this->_db->quote($this->id));
        $queries[] = $query;

        // Cascade changes into other tables
        $this->update();

        foreach ($queries as $query) {
            $this->_db->setQuery($query);

            try {
                $this->_db->execute();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }
        }

        KunenaMessageThankyouHelper::recount();

        return true;
    }

    /**
     * Send the e-mail of a notification to one address: from the mail template of Joomla (the setting of the forum), or from the
     * layout of the template of the forum.
     *
     * @param   Mail    $mail          The mail (the sender is set).
     * @param   int     $subscription  1 for a subscriber, 0 for a moderator.
     * @param   string  $subject       The subject (for the layout).
     * @param   string  $url           The address of the message.
     * @param   bool    $once          Only the first message is mailed.
     * @param   int     $recipientId   The user who gets the mail.
     * @param   bool    $follower      The recipient follows the author.
     * @param   string  $email         The address.
     * @param   string  $language      The language of the recipient.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    protected function deliverNotification(Mail $mail, int $subscription, string $subject, string $url, bool $once, int $recipientId, bool $follower, string $email, string $language): bool
    {
        $templateId = $subscription ? 'com_kunena.subscription' : 'com_kunena.moderator';

        if (KunenaMailTemplate::isUsed($templateId)) {
            $unsubscribeUrl = $this->addNotificationHeaders($mail, $subscription, $recipientId, $follower);
            $data           = $this->getMailTemplateData($subscription, $url, $once, $unsubscribeUrl);

            return KunenaMailTemplate::send($templateId, $language, $mail, $data['html'], $data['plain'], ['sitename', 'boardtitle', 'heading', 'subject', 'topic', 'category', 'author', 'url'], $email);
        }

        $this->attachEmailBody($mail, $subscription, $subject, $url, $once, $recipientId, $follower);

        return KunenaEmail::send($mail, [$email]);
    }

    /**
     * @return  integer
     *
     * @since   Kunena 6.0
     */
    protected function delta(): int
    {
        if (!$this->hold && ($this->_hold || $this->thread != $this->_thread)) {
            // Publish message or move it into new topic
            return 1;
        }

        if (!$this->_hold && $this->hold) {
            // Unpublish message
            return -1;
        }

        return 0;
    }

    /**
     * Display required field from message table
     *
     * @param   string   $field    field
     * @param   boolean  $html     html
     * @param   string   $context  context
     *
     * @return  integer|string
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public function displayField(string $field, $html = true, $context = '')
    {
        switch ($field) {
            case 'id':
                return \intval($this->id);
            case 'subject':
                return KunenaParser::parseText($this->subject);
            case 'message':
                return $html ? KunenaParser::parseBBCode($this->message, $this, 0, $context) : KunenaParser::stripBBCode($this->message, 0, $html);
        }

        return '';
    }

    /**
     * @param   array  $fields  fields
     * @param   mixed  $user    user
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function edit($fields = [], $user = null): void
    {
        $user = KunenaUserHelper::get($user);

        $this->bind($fields, ['name', 'email', 'subject', 'message', 'modified_reason'], true);

        // Update rest of the information
        $category            = $this->getCategory();
        $this->hold          = $category->review && !$category->isAuthorised('moderate', $user) ? 1 : $this->hold;
        $this->modified_by   = $user->userid;
        $this->modified_time = Factory::getDate()->toUnix();
    }

    /**
     * Gather necessary data to be able to send the notification mail
     *
     * @return  array
     *
     * @throws  Exception
     * @since   Kunena 7.1
     */
    private function gatherNotificationData()
    {
        if ($this->hold > 1) {
            return [[], false, []];
        } elseif ($this->hold == 1) {
            $mailsubs   = 0;
            $mailmods   = $this->_config->mailModerators >= 0;
            $mailadmins = $this->_config->mailAdministrators >= 0;
        } else {
            $mailsubs   = (bool) $this->_config->allowSubscriptions;
            $mailmods   = $this->_config->mailModerators >= 1;
            $mailadmins = $this->_config->mailAdministrators >= 1;
        }

        $once = false;

        if ($mailsubs) {
            if (!$this->parent) {
                // New topic: Send email only to category subscribers
                $mailsubs = $this->_config->categorySubscriptions != 'disabled' ? KunenaAccess::CATEGORY_SUBSCRIPTION : 0;
                $once     = $this->_config->categorySubscriptions == 'topic';
            } elseif ($this->_config->categorySubscriptions != 'post') {
                // Existing topic: Send email only to topic subscribers
                $mailsubs = $this->_config->topicSubscriptions != 'disabled' ? KunenaAccess::TOPIC_SUBSCRIPTION : 0;
                $once     = $this->_config->topicSubscriptions == 'first';
            } else {
                // Existing topic: Send email to both category and topic subscribers
                $mailsubs = $this->_config->topicSubscriptions == 'disabled'
                    ? KunenaAccess::CATEGORY_SUBSCRIPTION
                    : KunenaAccess::CATEGORY_SUBSCRIPTION | KunenaAccess::TOPIC_SUBSCRIPTION;

                // FIXME: category subscription can override topic
                $once = $this->_config->topicSubscriptions == 'first';
            }
        }

        // Get all subscribers, moderators and admins who should get the email.
        $emailToList = KunenaAccess::getInstance()->getSubscribers(
            $this->catid,
            $this->thread,
            $mailsubs,
            $mailmods,
            $mailadmins,
            KunenaUserHelper::getMyself()->userid
        );

        // The followers of the author get an e-mail about a new topic of him (if they may read the category and do not
        // get the e-mail already as subscribers of the category).
        if (!$this->parent && !$this->hold && KunenaUserFollow::isEnabled()) {
            $known    = array_map(static fn ($recipient) => (int) $recipient->id, (array) $emailToList);
            $category = $this->getCategory();

            foreach (KunenaUserFollow::getRecipients((int) $this->userid) as $follower) {
                $followerId = (int) $follower->id;

                if (
                    $followerId === (int) $this->userid || \in_array($followerId, $known, true)
                    || !$category->isAuthorised('read', KunenaUserHelper::get($followerId))
                ) {
                    continue;
                }

                $follower->subscription = 1;
                $follower->moderator    = 0;
                $follower->admin        = 0;
                $follower->follower     = 1;
                $emailToList[]          = $follower;
                $known[]                = $followerId;
            }
        }

        // Nobody gets subscription e-mails about the posts of a user whom he ignores (moderators and administrators
        // still get the e-mails they get because of their task).
        $ignoredBy = $emailToList ? KunenaUserIgnore::getIgnoredBy((int) $this->userid) : [];

        if ($ignoredBy) {
            foreach ($emailToList as $key => $emailTo) {
                if (!\in_array((int) $emailTo->id, $ignoredBy, true)) {
                    continue;
                }

                if ($emailTo->moderator || $emailTo->admin) {
                    $emailTo->subscription = 0;
                } else {
                    unset($emailToList[$key]);
                }
            }

            $emailToList = array_values($emailToList);
        }

        // Only who may read the message: the access of the category does not say it in a category where the users see only their
        // own topics (a follower of such a category would get the topics of the others).
        if ($emailToList && $this->getCategory()->params->get('ownTopicsOnly')) {
            $emailToList = array_values(array_filter(
                $emailToList,
                fn ($emailTo) => !empty($emailTo->id) && $this->isAuthorised('read', KunenaUserHelper::get((int) $emailTo->id))
            ));
        }

        $sentusers = [];

        if ($emailToList) {
            foreach ($emailToList as $emailTo) {
                if (!$emailTo->email || !MailHelper::isEmailAddress($emailTo->email)) {
                    continue;
                }

                if (
                    $this->_config->emailVisibleAddress != $emailTo->email ||
                    (
                        \count($emailToList) == 1 &&
                        ($emailTo->moderator || $emailTo->subscription)
                    )
                ) {
                    $sentusers[] = $emailTo->id;
                }
            }
        }

        return [$emailToList, $once, $sentusers];
    }

    /**
     * @param   bool|array  $ids     ids
     * @param   string      $action  action
     *
     * @return  KunenaAttachment[]
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getAttachments($ids = false, $action = 'read'): array
    {
        if ($ids === false) {
            $attachments = KunenaAttachmentHelper::getByMessage($this->id, $action);
        } else {
            $attachments = KunenaAttachmentHelper::getById($ids, $action);

            foreach ($attachments as $id => $attachment) {
                if ($attachment->mesid && $attachment->mesid != $this->id) {
                    unset($attachments[$id]);
                }
            }
        }

        return $attachments;
    }

    /**
     * Get the list of attachments that have been marked to be added to this message.
     *
     * @return  KunenaAttachment[]
     *
     * @since   Kunena 7.1
     */
    public function getAttachmentsToAdd(): array
    {
        return $this->_attachments_add;
    }

    /**
     * @return  KunenaUser
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getAuthor(): KunenaUser
    {
        return KunenaUserHelper::getAuthor($this->userid, $this->name);
    }

    /**
     * @return  KunenaCategory
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getCategory(): KunenaCategory
    {
        return KunenaCategoryHelper::get($this->catid);
    }

    /**
     * The Message-ID of the e-mail of a message: the first message of a topic has the id of the topic, the other messages
     * have the id of the message, so the e-mail of a reply can point to the e-mail of the message it answers.
     *
     * @param   int   $messageId    The message id.
     * @param   int   $topicId      The topic id.
     * @param   int   $firstPostId  The id of the first message of the topic.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected function getEmailMessageId(int $messageId, int $topicId, int $firstPostId): string
    {
        $local = $messageId === $firstPostId || !$firstPostId ? 'kunena-topic-' . $topicId : 'kunena-post-' . $messageId;

        return '<' . $local . '@' . $this->getMessageIdDomain() . '>';
    }

    /**
     * Returns \Kunena\Forum\Libraries\Forum\Message\Message object.
     *
     * @param   int   $identifier  The message to load - Can be only an integer.
     * @param   bool  $reload      reload
     *
     * @return  KunenaMessage
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public static function getInstance($identifier = null, $reload = false): KunenaMessage
    {
        return KunenaMessageHelper::get($identifier, $reload);
    }

    /**
     * The values of the tags of the mail template of a notification: for the html body and for the plain text body.
     *
     * @param   int     $subscription    1 for a subscriber, 0 for a moderator.
     * @param   string  $url             The address of the message.
     * @param   bool    $once            Only the first message is mailed.
     * @param   string  $unsubscribeUrl  The address to unsubscribe (empty when there is none).
     *
     * @return  array  html and plain.
     *
     * @since   Kunena 8.0
     */
    protected function getMailTemplateData(int $subscription, string $url, bool $once, string $unsubscribeUrl): array
    {
        $topic   = $this->getTopic();
        $subject = $this->subject ?: $topic->subject;

        if ($subscription) {
            $heading = $this->_approved
                ? Text::_('COM_KUNENA_POST_EMAIL_NOTIFICATION_APPROVED_MESSAGE')
                : Text::_($this->parent ? 'COM_KUNENA_POST_EMAIL_NOTIFICATION1' : 'COM_KUNENA_POST_EMAIL_NOTIFICATION1_CAT') . ' ' . $this->_config->boardTitle;
            $note    = Text::_($this->parent ? 'COM_KUNENA_POST_EMAIL_NOTIFICATION2' : 'COM_KUNENA_POST_EMAIL_NOTIFICATION2_CAT');
        } else {
            $heading = Text::_(empty($this->modified_time) ? 'COM_KUNENA_POST_EMAIL_MOD1' : 'COM_KUNENA_POST_EMAIL_MOD3') . ' ' . $this->_config->boardTitle;
            $note    = Text::_('COM_KUNENA_POST_EMAIL_MOD2');
        }

        $more   = $once ? Text::_($this->parent ? 'COM_KUNENA_POST_EMAIL_NOTIFICATION_MORE_READ' : 'COM_KUNENA_POST_EMAIL_NOTIFICATION_MORE_SUBSCRIBE') : '';
        $footer = Text::_('COM_KUNENA_POST_EMAIL_NOTIFICATION3');
        $esc    = static fn (string $text): string => htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        $common = [
            'sitename'   => Factory::getApplication()->get('sitename'),
            'boardtitle' => $this->_config->boardTitle,
            'heading'    => $heading,
            'subject'    => $subject,
            'topic'      => $topic->subject,
            'category'   => $this->getCategory()->name,
            'author'     => $this->getAuthor()->getName('???', false),
            'url'        => $url,
        ];

        $html = $common + [
            'message'     => $this->_config->mailFull ? '<p style="margin:18px 0 4px;color:#888888">' . $esc(Text::_('COM_KUNENA_MESSAGE')) . ':</p><div style="border-left:3px solid #dddddd;padding:4px 14px;margin:0 0 6px">' . $this->displayField('message', true, 'subscription') . '</div>' : '',
            'more'        => $more ? '<p style="font-size:14px">' . $esc($more) . '</p>' : '',
            'footer'      => $esc($footer) . '<br>' . $esc($note),
            'unsubscribe' => $unsubscribeUrl
                ? '<p style="font-size:12px;color:#999999">' . $esc(Text::_('COM_KUNENA_EMAIL_UNSUBSCRIBE_DESC')) . ' <a href="' . $esc($unsubscribeUrl) . '" style="color:#999999">' . $esc(Text::_('COM_KUNENA_EMAIL_UNSUBSCRIBE')) . '</a></p>'
                : '',
        ];

        $plain = $common + [
            'message'     => $this->_config->mailFull ? Text::_('COM_KUNENA_MESSAGE') . ":\n" . $this->displayField('message', false) . "\n" : '',
            'more'        => $more ? $more . "\n" : '',
            'footer'      => $footer . "\n" . $note,
            'unsubscribe' => $unsubscribeUrl ? Text::_('COM_KUNENA_EMAIL_UNSUBSCRIBE_DESC') . "\n" . $unsubscribeUrl : '',
        ];

        return ['html' => $html, 'plain' => $plain];
    }

    /**
     * The ids of the users which are mentioned in a text: [mention userid=1]name[/mention].
     *
     * @param   string  $text  The text of a message.
     *
     * @return  int[]
     *
     * @since   Kunena 8.0
     */
    public static function getMentionedUserIds(string $text): array
    {
        if (!preg_match_all('/\[mention userid=(\d+)\]/i', $text, $matches)) {
            return [];
        }

        return array_values(array_unique(array_map('intval', $matches[1])));
    }

    /**
     * The address part of the Message-ID of the e-mails of the forum: the domain of the site.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected function getMessageIdDomain(): string
    {
        $host = (string) parse_url(Uri::root(), PHP_URL_HOST);
        $host = strtolower(preg_replace('/[^a-z0-9.-]/i', '', $host));

        // The Message-ID needs a domain with a dot.
        return $host !== '' && strpos($host, '.') !== false ? $host : ($host ?: 'localhost') . '.localdomain';
    }

    /**
     * @return  KunenaDate
     *
     * @since   Kunena 6.0
     */
    public function getModifiedTime(): KunenaDate
    {
        return new KunenaDate($this->modified_time);
    }

    /**
     * @return  KunenaUser
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getModifier(): KunenaUser
    {
        return KunenaUserHelper::get($this->modified_by);
    }

    /**
     * Get the number of attachments into a message
     *
     * @return  StdClass
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getNbAttachments()
    {
        $attachments = KunenaAttachmentHelper::getNumberAttachments($this->id);

        $attachs                       = new StdClass();
        $attachs->inline               = 0;
        $attachs->image                = 0;
        $attachs->file                 = 0;
        $attachs->totalNonProtected    = 0;
        $attachs->readable             = 0;
        $attachs->totalProtected       = 0;
        $attachs->totalPrivate         = 0;

        foreach ($attachments as $attach) {
            if ($attach->inline) {
                $attachs->inline = $attachs->inline + 1;
            }

            if ($attach->isImage()) {
                $attachs->image = $attachs->image + 1;
            } else {
                $attachs->file = $attachs->file + 1;
            }

            if ($attach->protected == 0) {
                $attachs->totalNonProtected = $attachs->totalNonProtected + 1;
            }

            if ($attach->isAuthorised('read')) {
                if (!$attach->inline) {
                    $attachs->readable = $attachs->readable + 1;
                }
            }

            if ($attach->protected != 32) {
                $attachs->totalProtected = $attachs->totalProtected + 1;
            }

            if ($attach->protected == 32) {
                $attachs->totalPrivate = $attachs->totalPrivate + 1;
            }
        }

        return $attachs;
    }

    /**
     * @return  KunenaMessage
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getParent(): KunenaMessage
    {
        return KunenaMessageHelper::get($this->parent);
    }

    /**
     * @param   null|KunenaCategory  $category  category
     *
     * @return  Uri|boolean
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getPermaUri($category = null)
    {
        $category = $category ? KunenaCategoryHelper::get($category) : $this->getCategory();

        if (!$this->exists() || !$category->exists()) {
            return false;
        }

        return Uri::getInstance("index.php?option=com_kunena&view=topic&catid={$category->id}&id={$this->thread}&mesid={$this->id}");
    }

    /**
     *  Get permament topic URL without domain.
     *
     * If you want to add domain (for email etc), you can prepend the output with this:
     * Uri::getInstance()->toString(array('scheme', 'host', 'port'))
     *
     * @param   null|KunenaCategory  $category  Fake category if needed. Used for aliases.
     * @param   bool                 $xhtml     xhtml
     *
     * @return  string
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getPermaUrl($category = null, $xhtml = true): string
    {
        $uri = $this->getPermaUri($category);

        return KunenaRoute::_($uri, $xhtml);
    }

    /**
     * Get published state in text.
     *
     * @return  string
     *
     * @since   Kunena 4.0
     */
    public function getState(): string
    {
        switch ($this->hold) {
            case 0:
                return 'published';
            case 1:
                return 'unapproved';
            case 2:
            case 3:
                return 'deleted';
        }

        return 'unknown';
    }

    /**
     * Get the substring
     *
     * @param   string   $string  string
     * @param   integer  $start   start
     * @param   integer  $length  length
     *
     * @return  string
     *
     * @since   Kunena 5.0.2
     */
    public function getsubstr(string $string, int $start, int $length): string
    {
        $mbString = \extension_loaded('mbstring');

        if ($mbString) {
            $title = mb_substr($string, $start, $length);
        } else {
            $title2 = substr($string, $start, $length);
            $title  = preg_replace(
                '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
                    '|[\x00-\x7F][\x80-\xBF]+' .
                    '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
                    '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
                    '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
                '',
                $title2
            );
        }

        return $title;
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
            'id'              => $this->id,
            'parent'          => $this->parent,
            'thread'          => $this->thread,
            'catid'           => $this->catid,
            'name'            => $this->name,
            'userid'          => $this->userid,
            'email'           => $this->email,
            'subject'         => $this->subject,
            'time'            => $this->time,
            'ip'              => $this->ip,
            'topic_emoticon'  => $this->topic_emoticon,
            'locked'          => $this->locked,
            'hold'            => $this->hold,
            'ordering'        => $this->ordering,
            'hits'            => $this->hits,
            'moved'           => $this->moved,
            'deleted_time'    => $this->deleted_time,
            'modified_by'     => $this->modified_by,
            'modified_time'   => $this->modified_time,
            'modified_reason' => $this->modified_reason,
            'message'         => $this->message,
        ];

        return $properties;
    }

    /**
     * @return  KunenaDate
     *
     * @since   Kunena 6.0
     */
    public function getTime(): KunenaDate
    {
        return new KunenaDate($this->time);
    }

    /**
     * @return KunenaTopic|null
     *
     * @since   Kunena 6.0
     * @throws Exception
     */
    public function getTopic(): ?KunenaTopic
    {
        if (!$this->_topic) {
            $this->_topic = KunenaTopicHelper::get($this->thread);
        }

        return $this->_topic;
    }

    /**
     * @param   null|KunenaCategory  $category  Fake category if needed. Used for aliases.
     *
     * @return  Uri
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getUri($category = null)
    {
        return $this->getTopic()->getUri($category, $this);
    }

    /**
     * @param   null|KunenaCategory  $category  Fake category if needed. Used for aliases.
     * @param   bool                 $xhtml     xhtml
     * @param   int                  $itemid    itemid
     *
     * @return string
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public function getUrl($category = null, $xhtml = true, $itemid = 0): string
    {
        return $this->getTopic()->getUrl($category, $xhtml, $this, $itemid);
    }

    /**
     * @param   mixed  $user  user
     *
     * @return  KunenaTopicUser
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getUserTopic($user = null)
    {
        return $this->getTopic()->getUserTopic($user);
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
    public function isAuthorised(string $action = 'read', ?KunenaUser $user = null)
    {
        if ($this->_config->readOnly) {
            // Special case to ignore authorisation.
            if ($action != 'read') {
                return false;
            }
        }

        return !$this->tryAuthorise($action, $user, false);
    }

    /**
     * @param   mixed  $user  user
     *
     * @return  boolean
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function isNew($user = null): bool
    {
        $user = KunenaUserHelper::get($user);

        if (!$this->_config->showNew || !$user->exists()) {
            return false;
        }

        $session = KunenaFactory::getSession();

        if ($this->time < $session->getAllReadTime()) {
            return false;
        }

        $allreadtime = KunenaCategoryUserHelper::get($this->getCategory()->id, $user)->allreadtime;

        if ($allreadtime && $this->time < $allreadtime) {
            return false;
        }

        $read = KunenaTopicUserReadHelper::get($this->getTopic(), $user);

        if ($this->id == $read->message_id || $this->time < $read->time) {
            return false;
        }

        return true;
    }

    /**
     * Method to load a \Kunena\Forum\Libraries\Forum\Message\Message object by id.
     *
     * @param   mixed  $id  The message id to be loaded
     *
     * @return  boolean  True on success
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function load($id = null): bool
    {
        $exists        = parent::load($id);
        $this->_hold   = $exists ? $this->hold : 1;
        $this->_thread = $this->thread;

        return $exists;
    }

    /**
     * @param   mixed  $user  user
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function makeAnonymous($user = null): void
    {
        $user = KunenaUserHelper::get($user);

        if ($user->userid == $this->userid && $this->modified_by == $this->userid) {
            // I am the author and previous modification was made by me => delete modification information to hide my personality
            $this->modified_by     = 0;
            $this->modified_time   = 0;
            $this->modified_reason = '';
        } elseif ($user->userid == $this->userid) {
            // I am the author, but somebody else has modified the message => leave modification information intact
            $this->modified_by     = null;
            $this->modified_time   = null;
            $this->modified_reason = null;
        }

        // Remove userid, email and ip address
        $this->userid = 0;
        $this->ip     = '';
        $this->email  = null;
    }

    /**
     * @param   array       $fields      fields
     * @param   int         $useridfromparent
     * @param   null|array  $safefields  safefields
     *
     * @return  array
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public function newReply($fields = [], $useridfromparent = 0, $safefields = null): array
    {
        $user     = KunenaUserHelper::get();
        $topic    = $this->getTopic();
        $category = $this->getCategory();

        $message = new KunenaMessage();
        $message->setTopic($topic);
        $message->parent  = $this->id;
        $message->thread  = $topic->id;
        $message->catid   = $topic->category_id;
        $message->name    = $user->getName('');
        $message->userid  = $user->userid;
        $message->subject = $this->subject;
        $message->ip      = KunenaUserHelper::getUserIp();

        // Add IP to user.
        if (KunenaConfig::getInstance()->ipTracking) {
            if (empty($user->ip)) {
                $user->ip = KunenaUserHelper::getUserIp();
            }
        }

        if (KunenaConfig::getInstance()->allowChangeSubject && $topic->first_post_userid == $message->userid || KunenaUserHelper::getMyself()->isModerator()) {
            if (isset($fields['subject'])) {
                $topic->subject = $fields['subject'];
            }
        }

        if ($topic->hold) {
            // If topic was unapproved or deleted, use the same state for the new message
            $message->hold = $topic->hold;
        } else {
            // Otherwise message is either unapproved or published depending if the category is moderated or not
            $message->hold = $category->review ? (int) !$category->isAuthorised('moderate', $user) : 0;
        }

        if ($fields['quote'] === true) {
            $userfromparent     = KunenaUserHelper::get($useridfromparent);
            $userfromparentname = $userfromparent->getName();

            if (empty($userfromparent->getName())) {
                $userfromparentname = 'anonymous';
            }

            $find             = ['/\[confidential\](.*?)\[\/confidential\]/su'];
            $replace          = '';
            $text             = preg_replace($find, $replace, $this->message);
            $message->message = "[quote=\"{$userfromparentname} post={$this->id} userid={$useridfromparent}\"]" . $text . "[/quote]";
        } else {
            if (\is_array($safefields)) {
                $message->bind($safefields);
            }

            if (\is_array($fields)) {
                $message->bind($fields, ['name', 'email', 'subject', 'message'], true);
            }
        }

        return [$topic, $message];
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public static function notificationCloseConnection(): void
    {
        $app = Factory::getApplication();
        $app->setHeader('Connection', 'close');
    }

    /**
     * Choose how to handle the notification : store in queue for later in task plugin or send it now
     *
     * @param   null|string  $url       url
     * @param   boolean      $approved  false
     * @param   boolean      $type  Default on false mean to store the notification in queue (in database)
     *
     * @return  boolean|false
     *
     * @throws  Exception
     * @since   Kunena 7.1
     */
    public function processNotification($url = null, $approved = false, $type = false)
    {
        $this->_approved = $approved;

        // Gather data to be able to the handle to send the notification
        list($emailToList, $once, $sentusers) = $this->gatherNotificationData();

        // An inbox stores the notification for the users, and tells which of them do not want the e-mail as well.
        if (!empty($emailToList)) {
            $inboxUsers = [];

            foreach ($emailToList as $emailTo) {
                if (!empty($emailTo->id)) {
                    $inboxUsers[(int) $emailTo->id] = !empty($emailTo->follower) ? 'follow' : ($emailTo->subscription ? 'subscription' : 'moderation');
                }
            }

            foreach (array_unique($inboxUsers) as $inboxType) {
                $noMail = KunenaPrivate::getInstance()->notify($this, $inboxType, array_keys(array_filter($inboxUsers, static fn ($type) => $type === $inboxType)));

                if ($noMail) {
                    $emailToList = array_values(array_filter($emailToList, static fn ($emailTo) => !\in_array((int) ($emailTo->id ?? 0), $noMail, true)));
                }
            }
        }

        // No need to go further if mail settings are not configured
        $joomlaConfig = Factory::getApplication()->getConfig();

        if (!$joomlaConfig->get('mailonline') || !$this->_config->sendEmails) {
            return false;
        }

        // Nobody to notify: do not queue or send an empty notification
        if (empty($emailToList)) {
            return false;
        }

        if (!$url) {
            $url = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $this->getPermaUrl();
        }

        $topic = $this->getTopic();

        $subject     = $this->subject ? $this->subject : $topic->subject;

        if ($type) {
            // Send the notification now
            return $this->sendNotificationNow($emailToList, $subject, $url, $once, $sentusers);
        }
        // Store the notification in queue
        $columns = ['subject', 'messageId', 'url', 'emailListJson', 'categoryName', 'once', 'sentusers'];

        $values = [$this->_db->quote($subject), $this->id, $this->_db->quote($url), $this->_db->quote(json_encode($emailToList)), $this->_db->quote($this->getCategory()->name), (int) $once, $this->_db->quote(json_encode($sentusers))];

        $query     = $this->_db->createQuery()
            ->insert($this->_db->quoteName('#__kunena_notifications_mailsqueue'))
            ->columns($this->_db->quoteName($columns))
            ->values(implode(',', $values));

        $this->_db->setQuery($query);

        try {
            $this->_db->execute();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);
        }

        return true;
    }

    /**
     * @param   int  $value  value
     *
     * @return  boolean
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function publish($value = KunenaForum::PUBLISHED)
    {
        if ($this->hold == $value) {
            return true;
        }

        $this->hold = (int) $value;

        return $this->save();
    }

    /**
     * Remove listed attachments from the message.
     *
     * @param   array  $ids  ids
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 4.0
     */
    public function removeAttachments(array $ids): void
    {
        $this->_attachments_del += $this->getAttachments($ids, 'none');
    }

    /**
     * Method to save the \Kunena\Forum\Libraries\Forum\Message\Message object to the database.
     *
     * @return bool
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public function save(): bool
    {
        $user = KunenaUserHelper::getMyself();

        if ($user->userid == 0 && $this->userid) {
            $user = KunenaUserHelper::get($this->userid);
        }

        if ($user->userid == 0 && !$this->_config->pubWrite) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN'), 401);
        }

        $isNew = !$this->_exists;

        $topic    = $this->getTopic();
        $newTopic = !$topic->exists();

        if ($newTopic) {
            // Create topic, but do not cascade changes to category etc..
            try {
                $topic->save(false);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }

            $this->_thread = $this->thread = $topic->id;
        }

        // Create message
        if (!parent::save()) {
            // If we created a new topic, remember to delete it too.
            if ($newTopic) {
                $topic->delete();
            }

            return false;
        }

        if ($isNew) {
            $this->_hold = 1;
        }

        // Update attachments and message text
        $update = $this->updateAttachments();

        // Did we change anything?
        if ($update) {
            if ($isNew && trim($this->message) == '') {
                // Oops, no attachments remain and the message becomes empty.
                // Let's delete the new message and fail on save.
                $this->delete();

                // If we created a new topic, remember to delete it too.
                if ($newTopic) {
                    $topic->delete();
                }

                throw new Exception(Text::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE'));
            }

            $table = $this->getTable();
            $table->bind($this->getTableProperties());
            $table->exists(true);

            try {
                $table->store();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        // Cascade changes to other tables
        $this->update($newTopic);

        return true;
    }

    /**
     * Send an e-mail to the users who are mentioned in the message: the ones who are not mentioned in the old text (an edit
     * does not send the e-mail again), are not the author, may read the message and want the e-mail.
     *
     * @param   string  $oldText  The text of the message before an edit (empty for a new message).
     * @param   bool    $isNew    Is it a new message: the users who get the notification of the message anyway do not get a second e-mail.
     *
     * @return  int  The number of e-mails which were sent.
     *
     * @since   Kunena 8.0
     */
    public function sendMentionNotifications(string $oldText = '', bool $isNew = true): int
    {
        if ($this->hold != 0) {
            return 0;
        }

        $userids = array_diff(self::getMentionedUserIds((string) $this->message), self::getMentionedUserIds($oldText));
        $userids = \array_slice($userids, 0, 10);

        if (!$userids) {
            return 0;
        }

        // The users who get the e-mail of the new message (subscribers, moderators) do not need a second one.
        $skip = [(int) $this->userid => true];

        foreach (KunenaUserIgnore::getIgnoredBy((int) $this->userid) as $ignoring) {
            $skip[(int) $ignoring] = true;
        }

        if ($isNew) {
            list($emailToList) = $this->gatherNotificationData();

            foreach ((array) $emailToList as $emailTo) {
                if (!empty($emailTo->id)) {
                    $skip[(int) $emailTo->id] = true;
                }
            }
        }

        // The users who are mentioned and may read the message.
        $recipients = [];

        foreach ($userids as $userid) {
            $recipient = KunenaUserHelper::get($userid);

            if (!isset($skip[$userid]) && $recipient->exists() && !$recipient->blocked && $this->isAuthorised('read', $recipient)) {
                $recipients[] = (int) $userid;
            }
        }

        // An inbox stores the notification for them, and tells which of them do not want the e-mail as well.
        $noMail = $recipients ? KunenaPrivate::getInstance()->notify($this, 'mention', $recipients) : [];

        if (empty($this->_config->mentionEmail) || !$this->_config->sendEmails || !Factory::getApplication()->getConfig()->get('mailonline')) {
            return 0;
        }

        $topic      = $this->getTopic();
        $authorName = $this->getAuthor()->getName($this->name, false);
        $url        = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $this->getPermaUrl(null, false);
        $sender     = !empty($this->_config->emailSenderName) ? MailHelper::cleanAddress($this->_config->emailSenderName) : MailHelper::cleanAddress($this->_config->boardTitle);
        $sent       = 0;

        KunenaFactory::loadLanguage('com_kunena', 'site');

        foreach ($userids as $userid) {
            if (isset($skip[$userid]) || \in_array((int) $userid, $noMail, true)) {
                continue;
            }

            $recipient = KunenaUserHelper::get($userid);

            // A user who is blocked, has no valid address, may not read the message or has switched the e-mail off gets none.
            if (
                !$recipient->exists() || $recipient->blocked || !MailHelper::isEmailAddress((string) $recipient->email)
                || KunenaUserHelper::getJoomlaParam($userid, 'kunena_mention_email', 1) == 0
                || !$this->isAuthorised('read', $recipient)
            ) {
                continue;
            }

            try {
                $subject = MailHelper::cleanSubject(Text::sprintf('COM_KUNENA_MENTION_EMAIL_SUBJECT', $authorName, $topic->subject));
                $body    = '<p>' . Text::sprintf('COM_KUNENA_MENTION_EMAIL_BODY', htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8'), htmlspecialchars($topic->subject, ENT_QUOTES, 'UTF-8')) . '</p>'
                    . '<p><a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . Text::_('COM_KUNENA_MENTION_EMAIL_LINK') . '</a></p>';

                $mail = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();
                $mail->setSubject($subject);
                $mail->setSender([$this->_config->email, $sender]);

                if (KunenaMailTemplate::isUsed('com_kunena.mention')) {
                    // The mail template of Joomla (Users, Mail Templates).
                    $values = [
                        'sitename' => Factory::getApplication()->get('sitename'),
                        'author'   => $authorName,
                        'topic'    => $topic->subject,
                        'url'      => $url,
                    ];

                    if (KunenaMailTemplate::send('com_kunena.mention', (string) ($recipient->language ?? ''), $mail, $values, [], ['sitename', 'author', 'topic', 'url'], (string) $recipient->email)) {
                        $sent++;
                    }

                    continue;
                }

                $mail->isHtml(true);
                $mail->setBody($body);

                if (KunenaEmail::send($mail, [$recipient->email])) {
                    $sent++;
                }
            } catch (Exception $e) {
                // A mention e-mail which fails must not stop the post.
            }
        }

        return $sent;
    }

    /**
     * Send the notification mail
     *
     * @param   array    $emailToList  Contains a list of users to whom the notification will be sent
     * @param   string   $subject  The subject of the notification mail
     * @param   string   $url  The url of the message of the notification mail
     * @param   boolean  $once
     * @param   array    $sentusers
     * @param   callable|null  $onRecipientSent  Callback for each successfully sent recipient
     *
     * @return  boolean
     *
     * @throws  Exception
     * @since   Kunena 7.1
     */
    public function sendNotificationNow($emailToList, $subject, $url, $once, $sentusers, ?callable $onRecipientSent = null): bool
    {
        $mailnamesender = !empty($this->_config->emailSenderName) ? MailHelper::cleanAddress($this->_config->emailSenderName) : MailHelper::cleanAddress($this->_config->boardTitle);

        $app             = Factory::getApplication();
        $lang            = $app->getLanguage();
        $currentLanguage = $lang->getTag();

        KunenaFactory::loadLanguage('com_kunena', 'site');
        KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');

        // Send one email per recipient, in their own language.
        $success = true;

        foreach ($emailToList as $recipientIndex => $emailTo) {
            if (!$emailTo->email || !MailHelper::isEmailAddress($emailTo->email)) {
                continue;
            }

            if (
                $this->_config->emailVisibleAddress == $emailTo->email &&
                !(
                    \count($emailToList) == 1 &&
                    ($emailTo->moderator || $emailTo->subscription)
                )
            ) {
                continue;
            }

            // Determine subscription type (1 = subscriber, 0 = moderator/admin).
            $subscriptionType = $emailTo->subscription ? 1 : 0;

            // Load the recipient's language if it differs from the current one.
            $recipientLanguage = null;

            if (!empty($emailTo->id)) {
                $kunenaUser = KunenaUserHelper::get((int) $emailTo->id);

                if (!empty($kunenaUser->language)) {
                    $recipientLanguage = $kunenaUser->language;
                }
            }

            $languageChanged = $recipientLanguage && $recipientLanguage !== $currentLanguage;

            $siteLanguageLoaded = KunenaFactory::loadLanguageMail(
                'com_kunena',
                'site',
                $recipientLanguage
            );

            $librariesLanguageLoaded = KunenaFactory::loadLanguageMail(
                'com_kunena.libraries',
                'admin',
                $recipientLanguage
            );

            try {
                $mailSubject = MailHelper::cleanSubject($subject . " (" . $this->getCategory()->name . ")");

                $mail = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();
                $mail->setSubject($mailSubject);
                $mail->setSender([$this->_config->email, $mailnamesender]);

                if (!$this->deliverNotification($mail, $subscriptionType, $mailSubject, $url, $once, (int) ($emailTo->id ?? 0), !empty($emailTo->follower), (string) $emailTo->email, (string) $recipientLanguage)) {
                    $success = false;
                } elseif ($onRecipientSent !== null) {
                    $onRecipientSent($recipientIndex);
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            } finally {
                // Restore original language regardless of success or failure.
                if ($languageChanged) {
                    $lang->load('com_kunena', JPATH_SITE, $currentLanguage, true);
                }
            }
        }

        // Update subscriptions.
        if ($success && $once && $sentusers) {
            $sentusers = implode(',', $sentusers);
            $query     = $this->_db->createQuery()
                ->update('#__kunena_user_topics')
                ->set('subscribed=2')
                ->where("topic_id={$this->thread}")
                ->where("user_id IN ({$sentusers})")
                ->where('subscribed=1');

            $this->_db->setQuery($query);

            try {
                $this->_db->execute();
            } catch (ExecutionFailureException $e) {
                throw new Exception($e->getMessage());
            }
        }

        return $success;
    }

    /**
     * @param   KunenaTopic  $topic  topic
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function setTopic(KunenaTopic $topic): void
    {
        $this->_topic = $topic;

        if ($topic->id) {
            $this->thread = $topic->id;
        }
    }

    /**
     * Throws an exception if user isn't authorised to do the action.
     *
     * @param   string           $action  action
     * @param   KunenaUser|null  $user    user
     * @param   bool             $throw   trow
     *
     * @return  mixed
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

        if (empty($this->_authcache[$user->userid][$action])) {
            // Unknown action - throw invalid argument exception.
            if (!isset(self::$actions[$action])) {
                throw new InvalidArgumentException(Text::sprintf('COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action), 500);
            }

            // Load category authorisation.
            if (!isset($this->_authtcache[$user->userid][$action])) {
                $this->_authtcache[$user->userid][$action] = $this->getTopic()->tryAuthorise('post.' . $action, $user, false);
            }

            $this->_authcache[$user->userid][$action] = $this->_authtcache[$user->userid][$action];

            if (empty($this->_authcache[$user->userid][$action])) {
                foreach (self::$actions[$action] as $function) {
                    if (!isset($this->_authfcache[$user->userid][$function])) {
                        $authFunction                                = 'authorise' . $function;
                        $this->_authfcache[$user->userid][$function] = $this->$authFunction($user);
                    }

                    $error = $this->_authfcache[$user->userid][$function];

                    if ($error) {
                        $this->_authcache[$user->userid][$action] = $error;
                        break;
                    }
                }
            }
        }

        $exception = $this->_authcache[$user->userid][$action];

        // Throw or return the exception.
        if ($throw && $exception) {
            throw $exception;
        }

        return $exception;
    }

    /**
     * @param   bool  $newTopic  new topic
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function update($newTopic = false): void
    {
        // If post was published and then moved, we need to update old topic
        if (!$this->_hold && $this->_thread && $this->_thread != $this->thread) {
            $topic = KunenaTopicHelper::get($this->_thread);

            try {
                $topic->update($this, -1);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        $postDelta = $this->delta();
        $topic     = $this->getTopic();

        // New topic
        if ($newTopic) {
            $topic->hold = 0;
        }

        // Update topic
        if (!$this->hold && $topic->hold && $topic->exists()) {
            // We published message -> publish and recount topic
            $topic->hold = 0;
            $topic->recount();
        }

        try {
            $topic->update($this, $postDelta);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        // Activity integration
        // FIXME : load the plugin finder produce a fatal error
        // Joomla\CMS\Plugin\PluginHelper::importPlugin('finder');
        $activity = KunenaFactory::getActivityIntegration();

        if ($postDelta < 0) {
            $dispatcher = Factory::getContainer()->get(DispatcherInterface::class);
            $dispatcher->dispatch('onDeleteKunenaPost', new KunenaDeleteEvent('onDeleteKunenaPost', ['context' => 'com_kunena.post', 'subject' => $this, 'ids' => [$this->id]]));
            $activity->onAfterDelete($this);
        } elseif ($postDelta > 0) {
            $topic->markRead();

            if ($this->parent == 0) {
                $activity->onAfterPost($this);
            } else {
                $activity->onAfterReply($this);
            }
        }
    }

    /**
     * @return  boolean
     *
     * @since   Kunena 6.0
     */
    protected function updateAttachments(): bool
    {
        // Save new attachments and update message text
        $message = $this->message;
        $app     = Factory::getApplication();

        // No more files in a message than the option attachmentLimit allows (the uploader stops there, a request did not): the files
        // which stay in the message count, the new ones which do not fit are left out.
        $limit = (int) $this->_config->attachmentLimit;

        if ($limit > 0) {
            $kept = $this->id ? array_diff_key($this->getAttachments(false, 'none'), $this->_attachments_del) : [];
            $room = max(0, $limit - \count($kept));

            foreach ($this->_attachments_add as $tmpid => $attachment) {
                if (isset($kept[$attachment->id])) {
                    continue;
                }

                if ($room > 0) {
                    $room--;
                } else {
                    unset($this->_attachments_add[$tmpid]);
                }
            }
        }

        foreach ($this->_attachments_add as $tmpid => $attachment) {
            if ($attachment->exists() && $attachment->mesid) {
                try {
                    $attachment->save();
                } catch (Exception $e) {
                    $app->enqueueMessage($e->getMessage(), 'error');
                }
            }

            $attachment->mesid = $this->id;

            if ($attachment->IsImage()) {
                try {
                    $attachment->tryAuthorise('createimage', null, false);
                } catch (Exception $e) {
                    $app->enqueueMessage($e->getMessage(), 'error');
                }
            } else {
                try {
                    $attachment->tryAuthorise('createfile', null, false);
                } catch (Exception $e) {
                    $app->enqueueMessage($e->getMessage(), 'error');
                }
            }

            try {
                $attachment->save();
            } catch (Exception $e) {
                $app->enqueueMessage($e->getMessage(), 'error');
            }

            // Update attachments count and fix attachment name inside message
            $this->getTopic()->attachments++;
            $this->message = preg_replace('/\[attachment\:' . $tmpid . '\].*?\[\/attachment\]/u', "[attachment={$attachment->id}]{$attachment->filename}[/attachment]", $this->message);
        }

        // Delete removed attachments and update attachments count and message text
        foreach ($this->_attachments_del as $attachment) {
            if ($attachment->mesid && $attachment->mesid != $this->id) {
                // Attachment doesn't belong to this message => skip it.
                continue;
            }

            $exception = $attachment->tryAuthorise('delete', null, false);

            if ($exception) {
                throw new Exception($exception->getMessage());
            }

            try {
                $attachment->delete();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }

            $this->getTopic()->attachments--;

            $this->message = preg_replace('/\[attachment\=' . $attachment->id . '\].*?\[\/attachment\]/u', '', $this->message);
            $this->message = preg_replace('/\[attachment\]' . $attachment->filename . '\[\/attachment\]/u', '', $this->message);
        }

        // Remove missing temporary attachments from the message text
        $this->message = trim(preg_replace('/\[attachment\:\d+\].*?\[\/attachment\]/u', '', $this->message));

        // Return true if we changed the message contents
        return $this->message != $message;
    }

    /**
     * @param   int         $tmpid    tmpid
     * @param   string      $postvar  postvar
     * @param   int|null    $catid    catid
     *
     * @return  boolean
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public function uploadAttachment(int $tmpid, string $postvar, $catid = null): bool
    {
        $attachment                     = new KunenaAttachment();
        $attachment->userid             = $this->userid;
        $success                        = $attachment->upload($postvar, $catid);
        $this->_attachments_add[$tmpid] = $attachment;

        return $success;
    }
}
