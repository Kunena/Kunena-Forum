<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Private
 *
 * @copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\Registry\Registry;

/**
 * Private message.
 *
 * @property int       $id
 * @property int       $parent_id
 * @property int       $author_id
 * @property int       $created_at
 * @property int       $attachments
 * @property string    $subject
 * @property string    $body
 * @property JRegistry $params
 */
class KunenaPrivateMessage extends KunenaDatabaseObject
{
	/**
	 * @var string
	 * @since version
	 */
	protected $_table = 'KunenaPrivate';
	/**
	 * @var
	 * @since version
	 */
	protected $_attachments;
	/**
	 * @var
	 * @since version
	 */
	protected $_posts;
	/**
	 * @var
	 * @since version
	 */
	protected $_users;

	/**
	 * KunenaPrivateMessage constructor.
	 *
	 * @param   null  $properties
	 *
	 * @throws Exception
	 */
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
	}

	/**
	 * @param   string  $field
	 *
	 * @return int|string
	 * @throws Exception
	 */
	public function displayField($field)
	{
		switch ($field)
		{
			case 'id':
				return intval($this->id);
			case 'subject':
				return KunenaHtmlParser::parseText($this->subject);
			case 'body':
				return KunenaHtmlParser::parseBBCode($this->body, $this);
		}

		return '';
	}

	/**
	 * @return KunenaTableMap
	 *
	 * @since version
	 */
	public function attachments()
	{
		if (is_null($this->_attachments))
		{
			$this->_attachments = new KunenaTableMap('#__kunena_private_attachment_map', 'private_id', 'attachment_id');
			$this->_attachments->load($this->id);
		}

		return $this->_attachments;
	}

	/**
	 * @return KunenaTableMap
	 *
	 * @since version
	 */
	public function posts()
	{
		if (is_null($this->_posts))
		{
			$this->_posts = new KunenaTableMap('#__kunena_private_post_map', 'private_id', 'message_id');
			$this->_posts->load($this->id);
		}

		return $this->_posts;
	}

	/**
	 * @return KunenaTableMap
	 *
	 * @since version
	 */
	public function users()
	{
		if (is_null($this->_users))
		{
			$this->_users = new KunenaTableMap('#__kunena_private_user_map', 'private_id', 'user_id');
			$this->_users->load($this->id);
		}

		return $this->_users;
	}

	/**
	 * @return bool
	 *
	 * @since version
	 */
	public function check()
	{
		$this->params = new Registry($this->params);

		if (!is_null($this->_attachments))
		{
			$attachments       = array_values($this->_attachments->getMapped());
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
	 * @throws Exception
	 */
	protected function saveInternal()
	{
		if (!is_null($this->_attachments))
		{
			$this->_attachments->setKey($this->id)->save();
			$ids         = $this->_attachments->getMapped();
			$attachments = KunenaAttachmentHelper::getById($ids, 'none');
			foreach ($attachments as $attachment)
			{
				$attachment->protected = KunenaAttachment::PROTECTION_PRIVATE;
				$attachment->save();
			}
		}

		if (!is_null($this->_posts))
		{
			$this->_posts->setKey($this->id)->save();
		}

		if (!is_null($this->_users))
		{
			$this->_users->setKey($this->id)->save();
		}
	}

	/**
	 * Delete attachments
	 *
	 * @see KunenaDatabaseObject::delete()
	 */
	public function delete()
	{
		$attachments = $this->attachments()->delete();
		$posts       = $this->posts()->delete();
		$users       = $this->users()->delete();

		return parent::delete();
	}
}
