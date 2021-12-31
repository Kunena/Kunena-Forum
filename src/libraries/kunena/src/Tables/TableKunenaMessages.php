<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Tables
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tables;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use RuntimeException;
use UnexpectedValueException;

/**
 * Kunena Messages
 * Provides access to the #__kunena_messages table
 *
 * @since   Kunena 6.0
 */
class TableKunenaMessages extends KunenaTable
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $parent = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $thread = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $catid = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $name = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $userid = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $email = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $subject = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $time = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $ip = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $topic_emoticon = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $locked = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $hold = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $ordering = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $hits = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $moved = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $modified_by = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $modified_time = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $modified_reason = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $params = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $message = null;

	/**
	 * @param   DatabaseDriver  $db  Database driver
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__kunena_messages', 'id', $db);
	}

	/**
	 * @param   null  $id     id
	 * @param   bool  $reset  reset
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function load($id = null, $reset = true): bool
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
		if ($this->$k === null || \intval($this->$k) < 1)
		{
			$this->$k = 0;

			return false;
		}

		// Load the user data.
		$query = $this->_db->getQuery(true)
			->select(['m.*', 't.message'])
			->from($this->_db->quoteName('#__kunena_messages', 'm'))
			->innerJoin(
				$this->_db->quoteName('#__kunena_messages_text', 't') .
				' ON ' . $this->_db->quoteName('m.id') . ' = ' . $this->_db->quoteName('t.mesid')
			)
			->where($this->_db->quoteName('m.id') . ' = ' . (int) $this->$k);
		$this->_db->setQuery($query);

		try
		{
			$data = $this->_db->loadAssoc();
		}
		catch (ExecutionFailureException $e)
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
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function reset()
	{
		parent::reset();
		$this->message = null;
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function check(): bool
	{
		$category = KunenaCategoryHelper::get($this->catid);

		if (!$category->exists())
		{
			// TODO: maybe we should have own error message? or not?
			throw new RuntimeException(Text::sprintf('COM_KUNENA_LIB_TABLE_TOPICS_ERROR_CATEGORY_INVALID', $this->catid));
		}

		$this->catid   = $category->id;
		$this->subject = trim($this->subject);

		if (!$this->subject)
		{
			throw new UnexpectedValueException(Text::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_SUBJECT'));
		}

		$this->message = trim($this->message);

		if (!$this->message)
		{
			throw new UnexpectedValueException(Text::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE'));
		}

		if (!$this->time)
		{
			$this->time = Factory::getDate()->toUnix();
		}

		$this->modified_reason = trim($this->modified_reason);

		return true;
	}

	/**
	 * @see     KunenaTable::store()
	 *
	 * @param   boolean  $updateNulls  has no effect.
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function store($updateNulls = false): bool
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
			$query->update($this->_db->quoteName('#__kunena_messages_text'))
				->set($this->_db->quoteName('message') . ' = ' . $this->_db->quote($this->message))
				->where($this->_db->quoteName('mesid') . ' = ' . (int) $this->$k);
		}
		else
		{
			$query->insert($this->_db->quoteName('#__kunena_messages_text'))
				->columns(
					[
						$this->_db->quoteName('mesid'),
						$this->_db->quoteName('message'),
					]
				)
				->values((int) $this->$k . ', ' . $this->_db->quote($this->message));
		}

		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		return true;
	}
}
