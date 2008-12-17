<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

jimport('joomla.application.component.model');

/**
 * Config Model for the Kunena Package
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaModelConfig extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @access	protected
	 * @var		boolean
	 */
	var $__state_set		= null;

	/**
	 * Overridden method to get model state variables.
	 *
	 * @access	public
	 * @param	string	$property	Optional parameter name.
	 * @return	object	The property where specified, the state object where omitted.
	 * @since	1.0
	 */
	function getState($property = null)
	{
		// if the model state is uninitialized lets set some values we will need from the request.
		if (!$this->__state_set)
		{
			// load the component configuration parameters.
			$this->setState('config', JComponentHelper::getParams('com_kunena'));
			$this->__state_set = true;
		}

		return parent::getState($property);
	}

	/**
	 * Method to save the component configuration
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.0
	 */
	function save()
	{
		// initialize variables.
		$table			= &JTable::getInstance('component');
		$params 		= JRequest::getVar('params', array(), 'post', 'array');
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
	 * @access	public
	 * @param	string	$data	The configuration string in INI format.
	 * @return	boolean	True on success
	 * @since	1.0
	 */
	function import($data = null)
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