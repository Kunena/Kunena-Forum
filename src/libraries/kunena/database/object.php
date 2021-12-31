<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Object
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;

/**
 * Class KunenaDatabaseObject
 * @since Kunena
 */
abstract class KunenaDatabaseObject extends CMSObject
{
	/**
	 * @var null
	 * @since Kunena
	 */
	public $id = null;

	/**
	 * @var null|string
	 * @since Kunena
	 */
	protected $_name = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	protected $_table = null;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $_exists = false;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $_saving = false;

	/**
	 * Class constructor, overridden in descendant classes.
	 *
	 * @param   mixed $properties   Associative array to set the initial properties of the object.
	 *                              If not profided, default values will be used.
	 *
	 * @internal
	 * @since Kunena
	 */
	public function __construct($properties = null)
	{
		if (!$this->_name)
		{
			$this->_name = get_class($this);
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
	}

	/**
	 * Method to bind an associative array to the instance.
	 *
	 * This method optionally takes an array of properties to ignore or allow when binding.
	 *
	 * @param   array   $src     An associative array or object to bind to the \Joomla\CMS\Table\Table instance.
	 * @param   array   $fields  An optional array list of properties to ignore / include only while binding.
	 * @param   boolean $include True to include only listed fields, false to ignore listed fields.
	 *
	 * @return  boolean  True on success.
	 * @since Kunena
	 */
	public function bind(array $src = null, array $fields = null, $include = false)
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
	 * Returns the global object.
	 *
	 * @param   int     $identifier Object identifier to load.
	 * @param   boolean $reload     Force object reload from the database.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getInstance($identifier = null, $reload = false)
	{
		throw new Exception(__CLASS__ . '::' . __FUNCTION__ . '(): Not defined.');
	}

	/**
	 * Method to save the object to the database.
	 *
	 * Before saving the object, this method checks if object can be safely saved.
	 * It will also trigger onKunenaBeforeSave and onKunenaAfterSave events.
	 *
	 * @return  boolean  True on success.
	 * @since Kunena
	 * @throws Exception
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
		if (!$table->check())
		{
			$this->setError($table->getError());

			return $this->_saving = false;
		}

		// Include the Kunena plugins for the on save events.

		\Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');

		// Trigger the onKunenaBeforeSave event.
		$result = Factory::getApplication()->triggerEvent('onKunenaBeforeSave', array("com_kunena.{$this->_name}", &$table, $isNew));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());

			return $this->_saving = false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());

			return $this->_saving = false;
		}

		// If item was created, load the object.
		if ($isNew)
		{
			$this->load($table->id);
		}

		$this->saveInternal();

		// Trigger the onKunenaAfterSave event.
		Factory::getApplication()->triggerEvent('onKunenaAfterSave', array("com_kunena.{$this->_name}", &$table, $isNew));

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
	 * @since Kunena
	 */
	public function check()
	{
		return true;
	}

	/**
	 * Method to get the table object.
	 *
	 * @return  \Joomla\CMS\Table\Table|KunenaTable  The table object.
	 * @since Kunena
	 */
	protected function getTable()
	{
		return \Joomla\CMS\Table\Table::getInstance($this->_table, 'Table');
	}

	/**
	 * Method to load object from the database.
	 *
	 * @param   mixed $id Id to be loaded.
	 *
	 * @return  boolean  True on success.
	 * @since Kunena
	 */
	public function load($id = null)
	{
		if ($id !== null)
		{
			$this->id = intval($id);
		}

		// Create the table object
		$table = $this->getTable();

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

	// Internal functions

	/**
	 * Internal save method.
	 *
	 * @return  boolean  True on success.
	 * @since Kunena
	 */
	protected function saveInternal()
	{
		return true;
	}

	/**
	 * Method to delete the object from the database.
	 *
	 * @return    boolean    True on success.
	 * @since Kunena
	 * @throws Exception
	 */
	public function delete()
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

		\Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');

		// Trigger the onKunenaBeforeDelete event.
		$result = Factory::getApplication()->triggerEvent('onKunenaBeforeDelete', array("com_kunena.{$this->_name}", $table));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());

			return false;
		}

		if (!$table->delete())
		{
			$this->setError($table->getError());

			return false;
		}

		$this->_exists = false;

		// Trigger the onKunenaAfterDelete event.
		Factory::getApplication()->triggerEvent('onKunenaAfterDelete', array("com_kunena.{$this->_name}", $table));

		return true;
	}

	/**
	 * Returns true if the object exists in the database.
	 *
	 * @param   boolean $exists Internal parameter to change state.
	 *
	 * @return  boolean  True if object exists in database.
	 * @since Kunena
	 */
	public function exists($exists = null)
	{
		$return = $this->_exists;

		if ($exists !== null)
		{
			$this->_exists = (bool) $exists;
		}

		return $return;
	}
}
