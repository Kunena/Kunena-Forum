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

namespace Kunena\Forum\Libraries\KunenaPrivate;

defined('_JEXEC') or die();

use Exception;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Attachment\Attachment;
use Kunena\Forum\Libraries\Attachment\Helper;
use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;
use Kunena\Forum\Libraries\Html\Parser;
use Kunena\Forum\Libraries\Table\KunenaTableMap;
use function defined;

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
 * @property Registry  $params
 *
 * @since   Kunena 6.0
 */
class Message extends KunenaDatabaseObject
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
	 * @param   null  $properties properties
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
	 * @param   string  $field field
	 *
	 * @return integer|string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayField($field)
	{
		switch ($field)
		{
			case 'id':
				return intval($this->id);
			case 'subject':
				return Parser::parseText($this->subject);
			case 'body':
				return Parser::parseBBCode($this->body, $this);
		}

		return '';
	}

	/**
	 * @return  KunenaTableMap
	 *
	 * @since   Kunena 6.0
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
	 * @return  KunenaTableMap
	 *
	 * @since   Kunena 6.0
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
	 * @return  KunenaTableMap
	 *
	 * @since   Kunena 6.0
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
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
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
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function saveInternal()
	{
		if (!is_null($this->_attachments))
		{
			$this->_attachments->setKey($this->id)->save();
			$ids         = $this->_attachments->getMapped();
			$attachments = Helper::getById($ids, 'none');

			foreach ($attachments as $attachment)
			{
				$attachment->protected = Attachment::PROTECTION_PRIVATE;
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
	 * @see     KunenaDatabaseObject::delete()
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function delete()
	{
		$attachments = $this->attachments()->delete();
		$posts       = $this->posts()->delete();
		$users       = $this->users()->delete();

		return parent::delete();
	}
}
