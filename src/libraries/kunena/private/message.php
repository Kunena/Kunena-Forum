<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Private
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined('_JEXEC') or die();
/**
 * Private message.
 *
 * @property int $id
 * @property int $parent_id
 * @property int $author_id
 * @property int $created_at
 * @property int $attachments
 * @property string $subject
 * @property string $body
 * @property JRegistry $params
 */
class KunenaPrivateMessage extends KunenaDatabaseObject
{
    protected $_table = 'KunenaPrivate';
    protected $_attachments;
    protected $_posts;
    protected $_users;
    public function __construct($properties = null)
    {
        if (!empty($this->id))
        {
            $this->_exists = true;
        }
        else
        {
            parent::__construct($properties);
        }
        $this->params = new Joomla\Registry\Registry($this->params);
    }
    /**
     * @param string $field
     *
     * @return int|string
     */
    public function displayField($field)
    {
        switch ($field) {
            case 'id':
                return intval($this->id);
            case 'subject':
                return KunenaHtmlParser::parseText($this->subject);
            case 'body':
                return KunenaHtmlParser::parseBBCode($this->body, $this);
        }
        return '';
    }
    public function attachments()
    {
        if (is_null($this->_attachments)) {
            $this->_attachments = new KunenaTableMap('#__kunena_private_attachment_map', 'private_id', 'attachment_id');
            $this->_attachments->load($this->id);
        }
        return $this->_attachments;
    }
    public function posts()
    {
        if (is_null($this->_posts)) {
            $this->_posts = new KunenaTableMap('#__kunena_private_post_map', 'private_id', 'message_id');
            $this->_posts->load($this->id);
        }
        return $this->_posts;
    }
    public function users()
    {
        if (is_null($this->_users)) {
            $this->_users = new KunenaTableMap('#__kunena_private_user_map', 'private_id', 'user_id');
            $this->_users->load($this->id);
        }
        return $this->_users;
    }
    public function check()
    {
        if (!is_null($this->_attachments))
        {
            $attachments = array_values($this->_attachments->getMapped());
            $this->attachments = count($attachments);
            $this->params->set('attachments', $attachments);
        }
        if (!is_null($this->_posts))
        {
            $this->params->set('receivers.posts', array_values($this->_posts->getMapped()));
        }
        if (!is_null($this->_users))
        {
            $this->params->set('receivers.users', array_values($this->_users->getMapped()));
        }
        return parent::check();
    }
    /**
     * Save changes in the relations.
     *
     * @return void
     */
    protected function saveInternal() {
        if (!is_null($this->_attachments)) {
            $this->_attachments->setKey($this->id)->save();
            $ids = $this->_attachments->getMapped();
            $attachments = KunenaAttachmentHelper::getById($ids, 'none');
            foreach ($attachments as $attachment) {
                $attachment->protected = KunenaAttachment::PROTECTION_PRIVATE;
                $attachment->save();
            }
        }
        if (!is_null($this->_posts)) {
            $this->_posts->setKey($this->id)->save();
        }
        if (!is_null($this->_users)) {
            $this->_users->setKey($this->id)->save();
        }
    }
    public function delete() {
        $attachments = $this->attachments()->delete();
        $posts = $this->posts()->delete();
        $users = $this->users()->delete();
        return parent::delete();
    }
} 