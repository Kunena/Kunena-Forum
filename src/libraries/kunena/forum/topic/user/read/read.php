<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Forum.Topic.User.Read
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;

/**
 * Class KunenaForumTopicUserRead
 *
 * @property int $user_id
 * @property int $topic_id
 * @property int $category_id
 * @property int $message_id
 * @property int $time
 * @since Kunena
 */
class KunenaForumTopicUserRead extends CMSObject
{
	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $_exists = false;

	/**
	 * @var JDatabaseDriver|null
	 * @since Kunena
	 */
	protected $_db = null;

	/**
	 * @param   mixed $topic topic
	 * @param   mixed $user  user
	 *
	 * @throws Exception
	 * @internal
	 * @since Kunena
	 */
	public function __construct($topic = null, $user = null)
	{
		$topic = KunenaForumTopicHelper::get($topic);

		// Always fill empty data
		$this->_db = Factory::getDBO();

		// Create the table object
		$table = $this->getTable();

		// Lets bind the data
		$this->setProperties($table->getProperties());
		$this->_exists     = false;
		$this->topic_id    = $topic->exists() ? $topic->id : null;
		$this->category_id = $topic->exists() ? $topic->category_id : null;
		$this->user_id     = KunenaUserHelper::get($user)->userid;
	}

	/**
	 * Method to get the topics table object.
	 *
	 * @param   string $type   Topics table name to be used.
	 * @param   string $prefix Topics table prefix to be used.
	 *
	 * @return boolean|\Joomla\CMS\Table\Table|KunenaTable|TableKunenaUserRead
	 * @since Kunena
	 */
	public function getTable($type = 'KunenaUserRead', $prefix = 'Table')
	{
		static $tabletype = null;

		// Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix'])
		{
			$tabletype ['name']   = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return \Joomla\CMS\Table\Table::getInstance($tabletype ['name'], $tabletype ['prefix']);
	}

	/**
	 * @param   mixed $id     id
	 * @param   mixed $user   user
	 * @param   bool  $reload reload
	 *
	 * @return KunenaForumTopicUserRead
	 * @since Kunena
	 * @throws Exception
	 */
	public static function getInstance($id = null, $user = null, $reload = false)
	{
		return KunenaForumTopicUserReadHelper::get($id, $user, $reload);
	}

	/**
	 * @return KunenaForumTopicUserRead
	 * @since Kunena
	 * @throws Exception
	 */
	public function getTopic()
	{
		return KunenaForumTopicUserReadHelper::get($this->topic_id);
	}

	/**
	 * @param   array $data   data
	 * @param   array $ignore ignore
	 *
	 * @since Kunena
	 * @return void
	 */
	public function bind(array $data, array $ignore = array())
	{
		$data = array_diff_key($data, array_flip($ignore));
		$this->setProperties($data);
	}

	/**
	 * @since Kunena
	 * @return void
	 * @throws Exception
	 */
	public function reset()
	{
		$this->topic_id = 0;
		$this->load();
	}

	/**
	 * Method to load a KunenaForumTopicUserRead object by id.
	 *
	 * @param   int   $topic_id Topic id to be loaded.
	 * @param   mixed $user     user
	 *
	 * @return boolean    True on success.
	 * @throws Exception
	 * @since Kunena
	 */
	public function load($topic_id = null, $user = null)
	{
		if ($topic_id === null)
		{
			$topic_id = $this->topic_id;
		}

		if ($user === null && $this->user_id !== null)
		{
			$user = $this->user_id;
		}

		$user = KunenaUserHelper::get($user);

		// Create the table object
		$table = $this->getTable();

		// Load the KunenaTable object based on id
		if ($topic_id)
		{
			$this->_exists = $table->load(array('user_id' => $user->userid, 'topic_id' => $topic_id));
		}
		else
		{
			$this->_exists = false;
		}

		// Assuming all is well at this point lets bind the data
		$this->setProperties($table->getProperties());

		return $this->_exists;
	}

	/**
	 * Method to save the KunenaForumTopicUserRead object to the database.
	 *
	 * @param   bool $updateOnly Save the object only if not a new entry.
	 *
	 * @return boolean    True on success.
	 * @throws Exception
	 * @since Kunena
	 */
	public function save($updateOnly = false)
	{
		// Create the topics table object
		$table = $this->getTable();
		$table->bind($this->getProperties());
		$table->exists($this->_exists);

		// Check and store the object.
		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// Are we creating a new topic
		$isnew = !$this->_exists;

		// If we aren't allowed to create new topic return
		if ($isnew && $updateOnly)
		{
			return true;
		}

		// Store the topic data in the database
		if (!$result = $table->store())
		{
			$this->setError($table->getError());
		}

		// Fill up KunenaForumTopicUserRead object in case we created a new topic.
		if ($result && $isnew)
		{
			$this->load();
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaForumTopicUserRead object from the database.
	 *
	 * @return boolean    True on success.
	 * @since Kunena
	 */
	public function delete()
	{
		if (!$this->exists())
		{
			return true;
		}

		// Create the table object
		$table = $this->getTable();

		$result = $table->delete(array('topic_id' => $this->topic_id, 'user_id' => $this->user_id));

		if (!$result)
		{
			$this->setError($table->getError());
		}

		$this->_exists = false;

		return $result;
	}

	/**
	 * @param   null|bool $exists exists
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function exists($exists = null)
	{
		$return = $this->_exists;

		if ($exists !== null)
		{
			$this->_exists = $exists;
		}

		return $return;
	}
}
