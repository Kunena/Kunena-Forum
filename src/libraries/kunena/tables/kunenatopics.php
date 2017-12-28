<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

require_once(__DIR__ . '/kunena.php');

/**
 * Kunena Topics
 * Provides access to the #__kunena_topics table
 */
class TableKunenaTopics extends KunenaTable
{
	public $id = null;
	public $category_id = null;
	public $subject = null;
	public $icon_id = null;
	public $locked = null;
	public $hold = null;
	public $ordering = null;
	public $posts = null;
	public $hits = null;
	public $attachments = null;
	public $poll_id = null;
	public $moved_id = null;
	public $first_post_id = null;
	public $first_post_time = null;
	public $first_post_userid = null;
	public $first_post_message = null;
	public $first_post_guest_name = null;
	public $last_post_id = null;
	public $last_post_time = null;
	public $last_post_userid = null;
	public $last_post_message = null;
	public $last_post_guest_name = null;
	public $params = null;

	/**
	 * @param   string $db
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_topics', 'id', $db);
	}

	/**
	 * @param   null $id
	 * @param   bool $reset
	 *
	 * @return boolean
	 */
	public function load($id = null, $reset = true)
	{
		$this->_exists = false;
		$k = $this->_tbl_key;

		// Get the id to load.
		if ($id !== null) {
			$this->$k = $id;
		}

		// Reset the table.
		if ($reset) { $this->reset(); }

		// Check for a valid id to load.
		if ($this->$k === null || intval($this->$k) < 1)
		{
			$this->$k = 0;

			return false;
		}

		// Load the user data.
		$query = "SELECT * FROM #__kunena_topics WHERE id = {$this->$k}";
		$this->_db->setQuery($query);
		$data = $this->_db->loadAssoc();

		// Check for an error message.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		if(!$data)
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
	 */
	public function check()
	{
		$category = KunenaForumCategoryHelper::get($this->category_id);

		if (!$category->exists())
		{
			$this->setError(JText::sprintf('COM_KUNENA_LIB_TABLE_TOPICS_ERROR_CATEGORY_INVALID', $category->id));
		}
		else
		{
			$this->category_id = $category->id;
		}

		$this->subject = trim($this->subject);
		if (!$this->subject)
		{
			$this->setError(JText::sprintf('COM_KUNENA_LIB_TABLE_TOPICS_ERROR_NO_SUBJECT'));
		}

		return ($this->getError() == '');
	}
}
