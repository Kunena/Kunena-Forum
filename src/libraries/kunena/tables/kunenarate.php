<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined('_JEXEC') or die();

require_once(__DIR__ . '/kunena.php');

/**
 * Kunena Rate
 * Provides access to the #__kunena_rate table
 */
class TableKunenaRate extends KunenaTable
{
	public $topicid = null;
	public $userid = null;
	public $rate = null;
	public $time = null;

	/**
	 * @param   string $db
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_rate', 'topicid', $db);
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
		$query = "SELECT * FROM #__kunena_rate WHERE topicid = {$this->$k}";
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
