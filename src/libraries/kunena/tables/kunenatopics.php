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
 * Kunena Topics
 * Provides access to the #__kunena_topics table
 * @since Kunena
 */
class TableKunenaTopics extends KunenaTable
{
	/**
	 * @var null
	 * @since Kunena
	 */
	public $id = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $category_id = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $subject = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $icon_id = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $locked = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $hold = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $ordering = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $posts = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $hits = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $attachments = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $poll_id = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $moved_id = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $first_post_id = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $first_post_time = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $first_post_userid = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $first_post_message = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $first_post_guest_name = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $last_post_id = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $last_post_time = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $last_post_userid = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $last_post_message = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $last_post_guest_name = null;

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
		parent::__construct('#__kunena_topics', 'id', $db);
	}

	/**
	 * @param   null $id    id
	 * @param   bool $reset reset
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function load($id = null, $reset = true)
	{
		$this->_exists = false;
		$k             = $this->_tbl_key;

		// Get the id to load.
		if ($id !== null)
		{
			$this->$k = $id;
		}

		// Reset the table.
		if ($reset)
		{
			$this->reset();
		}

		// Check for a valid id to load.
		if ($this->$k === null || intval($this->$k) < 1)
		{
			$this->$k = 0;

			return false;
		}

		// Load the user data.
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from($this->_db->quoteName('#__kunena_topics'));
		$query->where($this->_db->quoteName('id') . '=' . $this->$k);
		$this->_db->setQuery($query);

		try
		{
			$data = $this->_db->loadAssoc();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		if (!$data)
		{
			$this->$k = 0;

			return false;
		}

		$this->_exists = true;

		// Bind the data to the table.
		$this->bind($data);

		return $this->_exists;
	}

	/**
	 * @return boolean
	 * @since Kunena
	 */
	public function check()
	{
		$category = KunenaForumCategoryHelper::get($this->category_id);

		if (!$category->exists())
		{
			$this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_TOPICS_ERROR_CATEGORY_INVALID', $category->id));
		}
		else
		{
			$this->category_id = $category->id;
		}

		$this->subject = trim($this->subject);

		if (!$this->subject)
		{
			$this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_TOPICS_ERROR_NO_SUBJECT'));
		}

		return $this->getError() == '';
	}
}
