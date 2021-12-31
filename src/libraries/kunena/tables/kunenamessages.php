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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

require_once __DIR__ . '/kunena.php';

/**
 * Kunena Messages
 * Provides access to the #__kunena_messages table
 * @since Kunena
 */
class TableKunenaMessages extends KunenaTable
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
	public $parent = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $thread = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $catid = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $name = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $userid = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $email = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $subject = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $time = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $ip = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $topic_emoticon = null;

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
	public $hits = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $moved = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $modified_by = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $modified_time = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $modified_reason = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $params = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $message = null;

	/**
	 * @param   JDatabaseDriver $db Database driver
	 *
	 * @since Kunena
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_messages', 'id', $db);
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
		$query->select(array('m.*', 't.message'));
		$query->from($this->_db->quoteName('#__kunena_messages', 'm'));
		$query->innerJoin($this->_db->quoteName('#__kunena_messages_text', 't') .
			' ON ' . $this->_db->quoteName('m.id') . ' = ' . $this->_db->quoteName('t.mesid')
		);
		$query->where($this->_db->quoteName('m.id') . '=' . $this->$k);
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
	 * @since Kunena
	 * @return void
	 */
	public function reset()
	{
		parent::reset();
		$this->message = null;
	}

	/**
	 * @return boolean
	 * @since Kunena
	 */
	public function check()
	{
		$category = KunenaForumCategoryHelper::get($this->catid);

		if (!$category->exists())
		{
			// TODO: maybe we should have own error message? or not?
			$this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_TOPICS_ERROR_CATEGORY_INVALID', $this->catid));
		}
		else
		{
			$this->catid = $category->id;
		}

		$this->subject = trim($this->subject);

		if (!$this->subject)
		{
			$this->setError(Text::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_SUBJECT'));
		}

		$this->message = trim($this->message);

		if (!$this->message)
		{
			$this->setError(Text::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE'));
		}

		if (!$this->time)
		{
			$this->time = Factory::getDate()->toUnix();
		}

		$this->modified_reason = trim($this->modified_reason);

		return $this->getError() == '';
	}

	/**
	 * @param   boolean $updateNulls has no effect.
	 *
	 * @return boolean
	 * @throws Exception
	 * @see   KunenaTable::store()
	 * @since Kunena
	 */
	public function store($updateNulls = false)
	{
		$k       = $this->_tbl_key;
		$update  = $this->_exists;
		$message = $this->message;
		unset($this->message);

		if (!parent::store())
		{
			return false;
		}

		$this->message = $message;
		$query         = $this->_db->getQuery(true);

		if ($update)
		{
			$query->update($this->_db->quoteName('#__kunena_messages_text'));
			$query->set($this->_db->quoteName('message') . '=' . $this->_db->quote($this->message));
			$query->where($this->_db->quoteName('mesid') . '=' . $this->$k);
		}
		else
		{
			$query->insert('#__kunena_messages_text')
				->columns(
					array(
						$this->_db->quoteName('mesid'),
						$this->_db->quoteName('message'),
					)
				)
				->values($this->$k . ', ' . $this->_db->quote($this->message));
		}

		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		return true;
	}
}
