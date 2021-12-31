<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Object
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Database;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Kunena\Forum\Libraries\Exception\KunenaException;

/**
 * Class KunenaDatabaseObject
 *
 * @since   Kunena 6.0
 */
abstract class KunenaDatabaseObject extends CMSObject
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $id = null;

	/**
	 * @var     null|string
	 * @since   Kunena 6.0
	 */
	protected $_name = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_table = null;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $_exists = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $_saving = false;

	/**
	 * Class constructor, overridden in descendant classes.
	 *
	 * @param   mixed  $properties  Associative array to set the initial properties of the object.
	 *                              If not provided, default values will be used.
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 * @internal
	 */
	public function __construct($properties = null)
	{
		if (!$this->_name)
		{
			$this->_name = \get_class($this);
		}

		// Load properties from database.
		if (!empty($this->id))
		{
			$this->_exists = true;
		}
		// Bind properties provided as parameters.
		elseif ($properties !== null)
		{
			$this->bind($properties);
		}
		// Initialize new object.
		else
		{
			$this->load();
		}

		parent::__construct($properties);
	}

	/**
	 * Method to bind an associative array to the instance.
	 *
	 * This method optionally takes an array of properties to ignore or allow when binding.
	 *
	 * @param   array|null  $src      An associative array or object to bind to the Joomla\CMS\Table\Table instance.
	 * @param   array|null  $fields   An optional array list of properties to ignore / include only while binding.
	 * @param   boolean     $include  True to include only listed fields, false to ignore listed fields.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 */
	public function bind(array $src = null, array $fields = null, $include = false): bool
	{
		if (empty($src))
		{
			return false;
		}

		if (!empty($fields))
		{
			$src = $include ? array_intersect_key($src, array_flip($fields)) : array_diff_key($src, array_flip($fields));
		}

		$this->setProperties($src);

		return true;
	}

	/**
	 * Method to load object from the database.
	 *
	 * @param   mixed  $id  Id to be loaded.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function load($id = null): bool
	{
		if ($id !== null)
		{
			$this->id = \intval($id);
		}

		// Create the table object
		$table = $this->getTable();

		if ($table)
		{
			// Load the object based on id
			if ($this->id)
			{
				$this->_exists = $table->load($this->id);
			}

			// Always set id
			$table->id = $this->id;

			// Assuming all is well at this point lets bind the data
			$this->setProperties($table->getProperties());

			return $this->_exists;
		}

		return false;
	}

	/**
	 * Method to get the table object.
	 *
	 * @return  boolean|Table  The table object.
	 *
	 * @since   Kunena 6.0
	 */
	protected function getTable()
	{
		$table = Table::getInstance($this->_table, 'Kunena\Forum\Libraries\Tables\Table');

		return $table;
	}

	/**
	 * Returns the global object.
	 *
	 * @param   int      $identifier  Object identifier to load.
	 * @param   boolean  $reload      Force object reload from the database.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getInstance($identifier = null, $reload = false)
	{
		throw new KunenaException(__CLASS__ . '::' . __FUNCTION__ . '(): Not defined.');
	}

	/**
	 * Method to save the object to the database.
	 *
	 * Before saving the object, this method checks if object can be safely saved.
	 * It will also trigger onKunenaBeforeSave and onKunenaAfterSave events.
	 *
	 * @return  boolean  True on success, Exception when fail.
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function save()
	{
		$this->_saving = true;

		// Check the object.
		if (!$this->check())
		{
			return $this->_saving = false;
		}

		// Initialize table object.
		$table = $this->getTable();
		$table->bind($this->getProperties());
		$table->exists($this->_exists);
		$isNew = !$this->_exists;

		// Check the table object.
		try
		{
			if (!$table->check())
			{
				return $this->_saving = false;
			}
		}
		catch (KunenaException $e)
		{
			throw new KunenaException($e->getMessage());
		}

		// Include the Kunena plugins for the on save events.
		PluginHelper::importPlugin('kunena');

		// Trigger the onKunenaBeforeSave event.
		$result = Factory::getApplication()->triggerEvent('onKunenaBeforeSave', ["com_kunena.{$this->_name}", &$table, $isNew]);

		if (\in_array(false, $result, true))
		{
			throw new KunenaException($table->getError());

			return $this->_saving = false;
		}

		// Store the data, the store() method from Joomla\CMS\Table\Table return only boolean and not exception.
		$result = $table->store();

		if (!$result)
		{
			throw new KunenaException($table->getError());
		}

		// If item was created, load the object.
		if ($isNew)
		{
			$this->load($table->id);
		}

		$this->saveInternal();

		// Trigger the onKunenaAfterSave event.
		Factory::getApplication()->triggerEvent('onKunenaAfterSave', ["com_kunena.{$this->_name}", &$table, $isNew]);

		$this->_saving = false;

		return true;
	}

	/**
	 * Method to perform sanity checks on the instance properties to ensure
	 * they are safe to store in the database.
	 *
	 * Child classes should override this method to make sure the data they are storing in
	 * the database is safe and as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @since   Kunena 6.0
	 */
	public function check(): bool
	{
		return true;
	}

	// Internal functions

	/**
	 * Internal save method.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 */
	protected function saveInternal()
	{
		return true;
	}

	/**
	 * Method to delete the object from the database.
	 *
	 * @return    boolean    True on success.
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function delete(): bool
	{
		// TODO: return false
		if (!$this->exists())
		{
			return true;
		}

		// Initialize table object.
		$table = $this->getTable();
		$table->bind($this->getProperties());
		$table->exists($this->_exists);

		// Include the Kunena plugins for the on save events.
		PluginHelper::importPlugin('kunena');

		// Trigger the onKunenaBeforeDelete event.
		$result = Factory::getApplication()->triggerEvent('onKunenaBeforeDelete', ["com_kunena.{$this->_name}", $table]);

		if (\in_array(false, $result, true))
		{
			$this->setError($table->getError());

			return false;
		}

		try
		{
			$table->delete();
		}
		catch (KunenaException $e)
		{
			return false;

			throw new KunenaException($e->getMessage());
		}

		$this->_exists = false;

		// Trigger the onKunenaAfterDelete event.
		Factory::getApplication()->triggerEvent('onKunenaAfterDelete', ["com_kunena.{$this->_name}", $table]);

		return true;
	}

	/**
	 * Returns true if the object exists in the database.
	 *
	 * @param   boolean  $exists  Internal parameter to change state.
	 *
	 * @return  boolean  True if object exists in database.
	 *
	 * @since   Kunena 6.0
	 */
	public function exists($exists = null): bool
	{
		$return = $this->_exists;

		if ($exists !== null)
		{
			$this->_exists = (bool) $exists;
		}

		return $return;
	}
}
