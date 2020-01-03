<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Tables
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;

require_once __DIR__ . '/kunena.php';

/**
 * Kunena User Topics Table
 * Provides access to the #__kunena_user_topics table
 *
 * @since   Kunena 6.0
 */
class TableKunenaUserTopics extends KunenaTable
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $user_id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $topic_id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $category_id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $posts = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $last_post_id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $owner = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $favorite = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $subscribed = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $params = null;

	/**
	 * @param   DatabaseDriver  $db  Database driver
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_user_topics', array('user_id', 'topic_id'), $db);
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function check()
	{
		$user  = KunenaUserHelper::get($this->user_id);
		$topic = KunenaForumTopicHelper::get($this->topic_id);

		if (!$user->exists())
		{
			throw new RuntimeException(Text::sprintf('COM_KUNENA_LIB_TABLE_USERTOPICS_ERROR_USER_INVALID', (int) $user->userid));
		}

		if (!$topic->exists())
		{
			throw new RuntimeException(Text::sprintf('COM_KUNENA_LIB_TABLE_USERTOPICS_ERROR_TOPIC_INVALID', (int) $topic->id));
		}

		$this->category_id = $topic->category_id;

		return true;
	}
}
