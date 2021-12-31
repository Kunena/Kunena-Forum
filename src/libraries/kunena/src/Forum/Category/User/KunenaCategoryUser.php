<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Category.User
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Category\User;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class \Kunena\Forum\Libraries\Forum\Category\CategoryUser
 *
 * @property int    $role
 * @property string $allreadtime
 * @property int    $subscribed
 * @property string $params
 * @property int    $user_id
 * @property int    $category_id
 * @since   Kunena 6.0
 */
class KunenaCategoryUser extends CMSObject
{
	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $_exists = false;

	/**
	 * @var     DatabaseDriver|null
	 * @since   Kunena 6.0
	 */
	protected $_db = null;

	/**
	 * @param   mixed  $user      user
	 *
	 * @param   int    $category  category
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 * @internal
	 *
	 */
	public function __construct($category = 0, $user = null)
	{
		// Always fill empty data
		$this->_db = Factory::getContainer()->get('DatabaseDriver');

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
	 * @param   string  $type    The categories table name to be used
	 * @param   string  $prefix  The categories table prefix to be used
	 *
	 * @return  boolean|Table The categories table object
	 *
	 * @since   Kunena 6.0
	 */
	public function getTable($type = 'Kunena\\Forum\\Libraries\\Tables\\', $prefix = 'KunenaUserCategories')
	{
		static $tabletype = null;

		// Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix'])
		{
			$tabletype ['name']   = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return Table::getInstance($tabletype ['prefix'], $tabletype ['name']);
	}

	/**
	 * @param   null|int  $id      id
	 * @param   mixed     $user    user
	 * @param   bool      $reload  reload
	 *
	 * @return  KunenaCategoryUser
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public static function getInstance($id = null, $user = null, $reload = false): KunenaCategoryUser
	{
		return KunenaCategoryUserHelper::get($id, $user, $reload);
	}

	/**
	 * @return  KunenaCategory
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function getCategory(): KunenaCategory
	{
		return KunenaCategoryHelper::get($this->category_id);
	}

	/**
	 * @param   array  $data    data
	 * @param   array  $ignore  ignore
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function bind(array $data, $ignore = []): void
	{
		$data = array_diff_key($data, array_flip($ignore));
		$this->setProperties($data);
	}

	/**
	 * Method to save the \Kunena\Forum\Libraries\Forum\Category\CategoryUser object to the database.
	 *
	 * @param   bool  $updateOnly  Save the object only if not a new category.
	 *
	 * @return  boolean  True on success
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function save($updateOnly = false): bool
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
		try
		{
			$result = $table->store();
		}
		catch (Exception $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		// Fill up \Kunena\Forum\Libraries\Forum\Category\CategoryUser object in case we created a new category.
		if ($result && $isnew)
		{
			$this->load();
		}

		return $result;
	}

	/**
	 * Method to load a \Kunena\Forum\Libraries\Forum\Category\CategoryUser object by id.
	 *
	 * @param   null|int  $categoryId  The category id to be loaded.
	 * @param   mixed     $user        The user to be loaded.
	 *
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function load($categoryId = null, $user = null): bool
	{
		if ($categoryId === null)
		{
			$categoryId = $this->category_id;
		}

		if ($user === null && $this->user_id !== null)
		{
			$user = $this->user_id;
		}

		$user = KunenaUserHelper::get($user);

		// Create the table object
		$table = $this->getTable();

		// Load the KunenaTable object based on id
		$this->_exists = $table->load(['user_id' => $user->userid, 'category_id' => $categoryId]);

		// Assuming all is well at this point lets bind the data
		$this->setProperties($table->getProperties());

		return $this->_exists;
	}

	/**
	 * Method to delete the \Kunena\Forum\Libraries\Forum\Category\CategoryUser object from the database.
	 *
	 * @return  boolean  True on success
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function delete(): bool
	{
		if (!$this->exists())
		{
			return true;
		}

		// Create the table object
		$table = $this->getTable();

		try
		{
			$result = $table->delete(['category_id' => $this->category_id, 'user_id' => $this->user_id]);
		}
		catch (Exception $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		$this->_exists = false;

		return $result;
	}

	/**
	 * @param   null|bool  $exists  True/false will change the state of the object.
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function exists($exists = null): bool
	{
		$return = $this->_exists;

		if ($exists !== null)
		{
			$this->_exists = $exists;
		}

		return $return;
	}
}
