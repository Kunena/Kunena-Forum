<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Private
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\KunenaPrivate;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\KunenaPrivate\Message\KunenaPrivateMessageFinder;
use Kunena\Forum\Libraries\Table\KunenaTableMap;

/**
 * Private message.
 *
 * @property int      $parent_id
 * @property int      $author_id
 * @property int      $created_at
 * @property int      $attachments
 * @property string   $subject
 * @property string   $body
 * @property Registry $params
 *
 * @property int $id
 * @since   Kunena 6.0
 */
class KunenaPrivateMessage extends KunenaDatabaseObject
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $_table = 'KunenaPrivate';

    /**
     * @var     KunenaTableMap|null
     * @since   Kunena 6.0
     */
    protected mixed $_attachments = null;

    /**
     * @var     KunenaTableMap|null
     * @since   Kunena 6.0
     */
    protected mixed $_posts = null;

    /**
     * @var     KunenaTableMap|null
     * @since   Kunena 6.0
     */
    protected mixed $_users = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $typeAlias = null;

    /**
     * @var     integer
     * @since   Kunena 6.4
     */
    public mixed $parentid = null;

    /**
     * @var     integer
     * @since   Kunena 6.4
     */
    public mixed $author_id = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $created_at = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $attachments = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $subject = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $body = null;

    /**
     * @var     string
     * @since   Kunena 6.4
     */
    public mixed $params = null;

    /**
     * @var     integer
     * @since   Kunena 6.4
     */
    public mixed $parent_id = null;

    /**
     * KunenaPrivateMessage constructor.
     *
     * @param   array|null  $properties  properties
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function __construct($properties = null)
    {
        if (!empty($this->id)) {
            $this->_exists = true;
        } else {
            parent::__construct($properties);
        }
    }

    /**
     * @return KunenaTableMap|null
     *
     * @since   Kunena 6.0
     */
    public function attachments(): ?KunenaTableMap
    {
        if (\is_null($this->_attachments)) {
            $this->_attachments = new KunenaTableMap('#__kunena_private_attachment_map', 'private_id', 'attachment_id');
            $this->_attachments->load($this->id);
        }

        return $this->_attachments;
    }

    /**
     * @return  boolean
     *
     * @since   Kunena 6.0
     */
    public function check(): bool
    {
        $this->params = new Registry($this->params);

        if (!\is_null($this->_attachments)) {
            $attachments       = array_values($this->_attachments->getMapped());
            $this->attachments = \count($attachments);
            $this->params->set('attachments', $attachments);
        }

        if (!\is_null($this->_posts)) {
            $this->params->set('receivers.posts', array_values($this->_posts->getMapped()));
        }

        if (!\is_null($this->_users)) {
            $this->params->set('receivers.users', array_values($this->_users->getMapped()));
        }

        return parent::check();
    }

    /**
     * Delete attachments
     *
     * @see     KunenaDatabaseObject::delete()
     *
     * @return  mixed
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    /**
     * The query which deletes the private messages of posts of the forum, with their recipients and the links to their posts
     * and their attachments (the files are attachments of the posts and go with them).
     *
     * @param   string  $messageIds  The ids of the posts: a list of integers or a query which selects them.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function deleteOfMessagesQuery(string $messageIds): string
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        return 'DELETE ' . $db->quoteName('p') . ', ' . $db->quoteName('pm') . ', ' . $db->quoteName('um') . ', ' . $db->quoteName('am')
            . ' FROM ' . $db->quoteName('#__kunena_private_post_map', 'pm')
            . ' INNER JOIN ' . $db->quoteName('#__kunena_private', 'p') . ' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('pm.private_id')
            . ' LEFT JOIN ' . $db->quoteName('#__kunena_private_user_map', 'um') . ' ON ' . $db->quoteName('um.private_id') . ' = ' . $db->quoteName('p.id')
            . ' LEFT JOIN ' . $db->quoteName('#__kunena_private_attachment_map', 'am') . ' ON ' . $db->quoteName('am.private_id') . ' = ' . $db->quoteName('p.id')
            . ' WHERE ' . $db->quoteName('pm.message_id') . ' IN (' . $messageIds . ')';
    }

    public function delete(): bool
    {
        // The finder keeps the messages which it found on this page: a deleted one is found no more.
        KunenaPrivateMessageFinder::forget();

        return parent::delete();
    }

    /**
     * @param   string  $field  field
     *
     * @return integer|string
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public function displayField(string $field)
    {
        switch ($field) {
            case 'id':
                return \intval($this->id);
            case 'subject':
                return KunenaParser::parseText($this->subject);
            case 'body':
                return KunenaParser::parseBBCode($this->body, $this);
        }

        return '';
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
            'id'          => $this->id,
            'parent_id'   => $this->parent_id,
            'author_id'   => $this->author_id,
            'created_at'  => $this->created_at,
            'attachments' => $this->attachments,
            'subject'     => $this->subject,
            'body'        => $this->body,
            'params'      => $this->params,
        ];

        return $properties;
    }

    /**
     * @return KunenaTableMap|null
     *
     * @since   Kunena 6.0
     */
    public function posts(): ?KunenaTableMap
    {
        if (\is_null($this->_posts)) {
            $this->_posts = new KunenaTableMap('#__kunena_private_post_map', 'private_id', 'message_id');
            $this->_posts->load($this->id);
        }

        return $this->_posts;
    }

    /**
     * Save changes in the relations.
     *
     * @return void
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    protected function saveInternal()
    {
        // The finder keeps the messages which it found on this page: a changed one is read again.
        KunenaPrivateMessageFinder::forget();

        if (!\is_null($this->_attachments)) {
            $this->_attachments->setKey($this->id)->save();
            $ids         = $this->_attachments->getMapped();
            $attachments = KunenaAttachmentHelper::getById($ids, 'none');

            foreach ($attachments as $attachment) {
                $attachment->protected = KunenaAttachment::PROTECTION_PRIVATE;
                $attachment->save();
            }
        }

        if (!\is_null($this->_posts)) {
            $this->_posts->setKey($this->id)->save();
        }

        if (!\is_null($this->_users)) {
            $this->_users->setKey($this->id)->save();
        }
    }

    /**
     * @return KunenaTableMap|null
     *
     * @since   Kunena 6.0
     */
    public function users(): ?KunenaTableMap
    {
        if (\is_null($this->_users)) {
            $this->_users = new KunenaTableMap('#__kunena_private_user_map', 'private_id', 'user_id');
            $this->_users->load($this->id);
        }

        return $this->_users;
    }
}
