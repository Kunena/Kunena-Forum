<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Forum.Category.User
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;

/**
 * Class KunenaForumCategoryUser
 *
 * @property int    $user_id
 * @property int    $category_id
 * @property int    $role
 * @property string $allreadtime
 * @property int    $subscribed
 * @property string $params
 * @since Kunena
 */
class KunenaForumCategoryUser extends CMSObject
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
	 * @param   int   $category category
	 * @param   mixed $user     user
	 *
	 * @throws Exception
	 * @internal
	 * @since Kunena
	 */
	public function __construct($category = 0, $user = null)
	{
		// Always fill empty data
		$this->_db = Factory::getDBO();

		// Create the table object
		$table = $this->getTable();

		// Lets bind the data
		$this->setProperties($table->getProperties());
		$this->_exists     = false;
		$this->category_id = $category;
		$this->user_id     = KunenaUserHelper::get($user)->userid;
	}

	/**
	 * Method to get the categories table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @param   string $type   The categories table name to be used
	 * @param   string $prefix The categories table prefix to be used
	 *
	 * @return \Joomla\CMS\Table\Table|TableKunenaUserCategories        The categories table object
	 * @since Kunena
	 */
	public function getTable($type = 'KunenaUserCategories', $prefix = 'Table')
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
	 * @param   null|int $id     id
	 * @param   mixed    $user   user
	 * @param   bool     $reload reload
	 *
	 * @return KunenaForumCategoryUser
	 * @since Kunena
	 * @throws Exception
	 */
	public static function getInstance($id = null, $user = null, $reload = false)
	{
		return KunenaForumCategoryUserHelper::get($id, $user, $reload);
	}

	/**
	 * @return KunenaForumCategory
	 * @since Kunena
	 */
	public function getCategory()
	{
		return KunenaForumCategoryHelper::get($this->category_id);
	}

	/**
	 * @param   array $data   data
	 * @param   array $ignore ignore
	 *
	 * @since Kunena
	 * @return void
	 */
	public function bind($data, $ignore = array())
	{
		$data = array_diff_key($data, array_flip($ignore));
		$this->setProperties($data);
	}

	/**
	 * Method to save the KunenaForumCategoryUser object to the database.
	 *
	 * @param   bool $updateOnly Save the object only if not a new category.
	 *
	 * @return boolean    True on success
	 * @throws Exception
	 * @since Kunena
	 */
	public function save($updateOnly = false)
	{
		// Create the categories table object
		$table = $this->getTable();
		$table->bind($this->getProperties());
		$table->exists($this->_exists);

		// Check and store the object.
		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// Are we creating a new category
		$isnew = !$this->_exists;

		// If we aren't allowed to create new category return
		if ($isnew && $updateOnly)
		{
			return true;
		}

		// Store the category data in the database
		if (!$result = $table->store())
		{
			$this->setError($table->getError());
		}

		// Fill up KunenaForumCategoryUser object in case we created a new category.
		if ($result && $isnew)
		{
			$this->load();
		}

		return $result;
	}

	/**
	 * Method to load a KunenaForumCategoryUser object by id.
	 *
	 * @param   null|int $category_id The category id to be loaded.
	 * @param   mixed    $user        The user to be loaded.
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function load($category_id = null, $user = null)
	{
		if ($category_id === null)
		{
			$category_id = $this->category_id;
		}

		if ($user === null && $this->user_id !== null)
		{
			$user = $this->user_id;
		}

		$user = KunenaUserHelper::get($user);

		// Create the table object
		$table = $this->getTable();

		// Load the KunenaTable object based on id
		$this->_exists = $table->load(array('user_id' => $user->userid, 'category_id' => $category_id));

		// Assuming all is well at this point lets bind the data
		$this->setProperties($table->getProperties());

		return $this->_exists;
	}

	/**
	 * Method to delete the KunenaForumCategoryUser object from the database.
	 *
	 * @return boolean    True on success
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

		$result = $table->delete(array('category_id' => $this->category_id, 'user_id' => $this->user_id));

		if (!$result)
		{
			$this->setError($table->getError());
		}

		$this->_exists = false;

		return $result;
	}

	/**
	 * @param   null|bool $exists True/false will change the state of the object.
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
