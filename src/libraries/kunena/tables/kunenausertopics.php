<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Tables
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

require_once __DIR__ . '/kunena.php';

/**
 * Kunena User Topics Table
 * Provides access to the #__kunena_user_topics table
 * @since Kunena
 */
class TableKunenaUserTopics extends KunenaTable
{
	/**
	 * @var null
	 * @since Kunena
	 */
	public $user_id = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $topic_id = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $category_id = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $posts = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $last_post_id = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $owner = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $favorite = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $subscribed = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $params = null;

	/**
	 * @param   JDatabaseDriver $db Database driver
	 *
	 * @since Kunena
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_user_topics', array('user_id', 'topic_id'), $db);
	}

	/**
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function check()
	{
		$user  = KunenaUserHelper::get($this->user_id);
		$topic = KunenaForumTopicHelper::get($this->topic_id);

		if (!$user->exists())
		{
			$this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_USERTOPICS_ERROR_USER_INVALID', (int) $user->userid));
		}

		if (!$topic->exists())
		{
			$this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_USERTOPICS_ERROR_TOPIC_INVALID', (int) $topic->id));
		}

		$this->category_id = $topic->category_id;

		return $this->getError() == '';
	}
}
