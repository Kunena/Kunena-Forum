<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Private
 *
 * @copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\KunenaPrivate;

\defined('_JEXEC') or die();

use Exception;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Table\KunenaTableMap;

/**
 * Private message.
 *
 * @property int      $parentid
 * @property int      $author_id
 * @property int      $created_at
 * @property int      $attachments
 * @property string   $subject
 * @property string   $body
 * @property Registry $params
 *
 * @property int      $id
 * @since   Kunena 6.0
 */
class KunenaPrivateMessage extends KunenaDatabaseObject
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $_table = 'KunenaPrivate';

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_attachments = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_posts = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_users = null;

	/**
	 * KunenaPrivateMessage constructor.
	 *
	 * @param   null  $properties  properties
	 *
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
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
		switch ($field)
		{
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
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function check(): bool
	{
		$this->params = new Registry($this->params);

		if (!\is_null($this->_attachments))
		{
			$attachments       = array_values($this->_attachments->getMapped());
			$this->attachments = \count($attachments);
			$this->params->set('attachments', $attachments);
		}

		if (!\is_null($this->_posts))
		{
			$this->params->set('receivers.posts', array_values($this->_posts->getMapped()));
		}

		if (!\is_null($this->_users))
		{
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
	public function delete(): bool
	{
		return parent::delete();
	}

	/**
	 * @return \Kunena\Forum\Libraries\Table\KunenaTableMap|null
	 *
	 * @since   Kunena 6.0
	 */
	public function attachments(): ?KunenaTableMap
	{
		if (\is_null($this->_attachments))
		{
			$this->_attachments = new KunenaTableMap('#__kunena_private_attachment_map', 'private_id', 'attachment_id');
			$this->_attachments->load($this->id);
		}

		return $this->_attachments;
	}

	/**
	 * @return \Kunena\Forum\Libraries\Table\KunenaTableMap|null
	 *
	 * @since   Kunena 6.0
	 */
	public function posts(): ?KunenaTableMap
	{
		if (\is_null($this->_posts))
		{
			$this->_posts = new KunenaTableMap('#__kunena_private_post_map', 'private_id', 'message_id');
			$this->_posts->load($this->id);
		}

		return $this->_posts;
	}

	/**
	 * @return \Kunena\Forum\Libraries\Table\KunenaTableMap|null
	 *
	 * @since   Kunena 6.0
	 */
	public function users(): ?KunenaTableMap
	{
		if (\is_null($this->_users))
		{
			$this->_users = new KunenaTableMap('#__kunena_private_user_map', 'private_id', 'user_id');
			$this->_users->load($this->id);
		}

		return $this->_users;
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
		if (!\is_null($this->_attachments))
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

		if (!\is_null($this->_posts))
		{
			$this->_posts->setKey($this->id)->save();
		}

		if (!\is_null($this->_users))
		{
			$this->_users->setKey($this->id)->save();
		}
	}
}
