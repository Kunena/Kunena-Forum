<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Configuration Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelConfig extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @var		boolean
	 * @since	1.6
	 */
	protected $__state_set = false;

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param	string	Optional parameter name.
	 * @param	mixed	The default value to use if no state property exists by name.
	 * @return	object	The property where specified, the state object where omitted.
	 * @since	1.6
	 */
	public function getState($property = null, $default = null)
	{
		// if the model state is uninitialized lets set some values we will need from the request.
		if (!$this->__state_set)
		{
			$this->__state_set = true;
		}

		$value = parent::getState($property);
		return (is_null($value) ? $default : $value);
	}

	/**
	 * Method to get the configuration form.
	 *
	 * @return	mixed	JParameter object on success, false on failure.
	 * @since	1.6
	 */
	public function getOptions()
	{
		// Bind the current settings to the form.
		$options = JComponentHelper::getParams('com_kunena');
		return $options;
	}

	/**
	 * Method to save the component configuration
	 *
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	public function save()
	{
		// initialize variables.
		$table			= JTable::getInstance('component');
		$params 		= JRequest::getVar('config', array(), 'post', 'array');
		$row			= array();
		$row['option']	= 'com_kunena';
		$row['params']	= $params;

		// load the component data
		if (!$table->loadByOption('com_kunena')) {
			$this->setError($table->getError());
			return false;
		}

		// bind the new values
		$table->bind($row);

		// check the row.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// store the row.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to import the component configuration
	 *
	 * @param	string	The configuration string in INI format.
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	public function import($data = null)
	{
		// load the component data
		$table = &JTable::getInstance('component');
		if (!$table->loadByOption('com_kunena')) {
			$this->setError($table->getError());
			return false;
		}

		// set the new configuration values
		$table->set('params', $data);

		// check the row.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// store the row.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}
}