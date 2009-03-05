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
jximport('jxtended.database.query');

/**
 * Smiley model class for Kunena.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaModelSmiley extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @access	protected
	 * @var		boolean
	 */
	var $__state_set	= false;

	/**
	 * Array of items for memory caching.
	 *
	 * @access	protected
	 * @var		array
	 */
	var $_items			= array();

	/**
	 * Overridden method to get model state variables.
	 *
	 * @access	public
	 * @param	string	$property	Optional parameter name.
	 * @return	object	The property where specified, the state object where omitted.
	 * @since	1.0
	 */
	function getState($property = null, $default = null)
	{
		if (!$this->__state_set)
		{
			// Get the application object.
			$app = &JFactory::getApplication();

			// Attempt to auto-load the smiley id.
			if (!$smileyId = (int)$app->getUserState('com_kunena.edit.smiley.id')) {
				$smileyId = (int)JRequest::getInt('cat_id');
			}

			// Only set the smiley id if there is a value.
			if ($smileyId) {
				$this->setState('smiley.id', $smileyId);
			}

			// Set the model state set flat to true.
			$this->__state_set = true;
		}

		$value = parent::getState($property);
		return (is_null($value) ? $default : $value);
	}

	/**
	 * Method to get a smiley item.
	 *
	 * @access	public
	 * @param	integer	The id of the smiley to get.
	 * @return	mixed	Smiley data object on success, false on failure.
	 * @since	1.0
	 */
	function &getItem($smileyId = null)
	{
		// Initialize variables.
		$smileyId = (!empty($smileyId)) ? $smileyId : (int)$this->getState('smiley.id');
		$false	= false;

		// Get a smiley row instance.
		$table = &$this->getTable('Smiley', 'KunenaTable');

		// Attempt to load the row.
		$return = $table->load($smileyId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->serError($table->getError());
			return $false;
		}

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return $false;
		}

		$value = JArrayHelper::toObject($table->getProperties(1), 'JObject');
		return $value;
	}

	/**
	 * Method to get the smiley form.
	 *
	 * @access	public
	 * @return	mixed	JXForm object on success, false on failure.
	 * @since	1.0
	 */
	function &getForm()
	{
		// Initialize variables.
		$app	= &JFactory::getApplication();
		$false	= false;

		// Get the form.
		jximport('jxtended.form.form');
		JXForm::addFormPath(JPATH_COMPONENT.'/models/forms');
		JXForm::addFieldPath(JPATH_COMPONENT.'/models/fields');
		$form = &JXForm::getInstance('smiley');

		// Check for an error.
		if (JError::isError($form)) {
			$this->setError($form->getMessage());
			return $false;
		}

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_kunena.edit.smiley.data', array());

		// Bind the form data if present.
		if (!empty($data)) {
			$form->bind($data);
		}

		return $form;
	}

	/**
	 * Method to publish smilies.
	 *
	 * @access	public
	 * @param	array	The ids of the items to publish.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function publish($smileyIds)
	{
		// Sanitize the ids.
		$smileyIds = (array) $smileyIds;
		JArrayHelper::toInteger($smileyIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a smiley row instance.
		$table = &$this->getTable('Smiley', 'KunenaTable');

		// Attempt to publish the items.
		if (!$table->publish($smileyIds, 1, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to unpublish smilies.
	 *
	 * @access	public
	 * @param	array	The ids of the items to unpublish.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function unpublish($smileyIds)
	{
		// Sanitize the ids.
		$smileyIds = (array) $smileyIds;
		JArrayHelper::toInteger($smileyIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a smiley row instance.
		$table = &$this->getTable('Smiley', 'KunenaTable');

		// Attempt to unpublish the items.
		if (!$table->publish($smileyIds, 0, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to put smilies in the trash.
	 *
	 * @access	public
	 * @param	array	The ids of the items to trash.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function trash($smileyIds)
	{
		// Sanitize the ids.
		$smileyIds = (array) $smileyIds;
		JArrayHelper::toInteger($smileyIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a smiley row instance.
		$table = &$this->getTable('Smiley', 'KunenaTable');

		// Attempt to trash the items.
		if (!$table->publish($smileyIds, -2, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to delete smilies.
	 *
	 * @access	public
	 * @param	array	An array of smiley ids.
	 * @return	boolean	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function delete($smileyIds)
	{
		// Sanitize the ids.
		$smileyIds = (array) $smileyIds;
		JArrayHelper::toInteger($smileyIds);

		// Get a smiley row instance.
		$table = &$this->getTable('Smiley', 'KunenaTable');

		// Iterate the smilies to delete each one.
		foreach ($smileyIds as $smileyId)
		{
			$table->delete($smileyId);
		}

		// Rebuild the nested set tree.
		$table->rebuild();

		return true;
	}

	/**
	 * Method to toggle settings on a smiley or a set of smilies.
	 *
	 * The method toggles a property as either 1 or 0 for the given
	 * property.
	 *
	 * @access	public
	 * @param	array	An array of smiley ids.
	 * @param	string	The property to toggle.
	 * @return	boolean	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function toggleProperty($smileyIds, $property)
	{
		// Sanitize the ids.
		$smileyIds = (array) $smileyIds;
		JArrayHelper::toInteger($smileyIds);

		// Get a smiley row instance.
		$table = &$this->getTable('Smiley', 'KunenaTable');

		// Ensure the property exists.
		if (isset($table->$property)) {
			$this->setError('KUNENA_INVALID_PROPERTY');
			return false;
		}

		// Iterate the smilies to perform the toggle on each one.
		foreach ($smileyIds as $smileyId)
		{
			// Attempt to load the row.
			$return = $table->load($smileyId);

			// Check for a table object error.
			if ($return === false && $table->getError()) {
				$this->serError($table->getError());
				return false;
			}

			// Check for a database error.
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// Toggle the property.
			$table->$property = ($table->$property) ? 0 : 1;

			// Check the data.
			if (!$table->check()) {
				$this->setError($table->getError());
				return false;
			}

			// Store the data.
			if (!$table->store()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

		}

		return true;
	}

	function setProperty($smileyIds, $property, $value)
	{
		// Sanitize the ids.
		$smileyIds = (array) $smileyIds;
		JArrayHelper::toInteger($smileyIds);

		// Get a smiley row instance.
		$table = &$this->getTable('Smiley', 'KunenaTable');

		// Ensure the property exists.
		if (!isset($table->$property)) {
			$this->setError('KUNENA_INVALID_PROPERTY');
			return false;
		}

		// Set the property for relevant rows.
		$this->_db->setQuery(
			'UPDATE `#__kunena_smileys`' .
			' SET '.$this->_db->nameQuote($property).' = '.$this->_db->Quote($value) .
			' WHERE `id` IN ('.implode(',', $smileyIds).')'
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Adjust the smiley ordering.
	 *
	 * @access	public
	 * @param	integer	Primary key of the item to adjust.
	 * @param	integer	Increment, usually +1 or -1
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function ordering($id, $move = 0)
	{
		// Sanitize the id and adjustment.
		$id = (int) $id;
		$move = (int) $move;

		// Get a smiley row instance.
		$table = &$this->getTable('Smiley', 'KunenaTable');

		// Attempt to adjust the row ordering.
		if (!$table->ordering($move, $id)) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to check in a smiley.
	 *
	 * @access	public
	 * @param	integer	The id of the row to check in.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function checkin($smileyId = null)
	{
		// Initialize variables.
		$smileyId	= (!empty($smileyId)) ? $smileyId : (int)$this->getState('smiley.id');
		$result	= true;

		// Only attempt to check the row in if it exists.
		if ($smileyId)
		{
			// Get a smiley row instance.
			$table = &$this->getTable('Smiley', 'KunenaTable');

			// Attempt to check the row in.
			if (!$table->checkin($smileyId)) {
				$this->setError($table->getError());
				$result	= false;
			}
		}

		return $result;
	}

	/**
	 * Method to check out a smiley.
	 *
	 * @access	public
	 * @param	integer	The id of the row to check out.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function checkout($smileyId = null)
	{
		// Initialize variables.
		$smileyId	= (!empty($smileyId)) ? $smileyId : (int)$this->getState('smiley.id');
		$result	= true;

		// Only attempt to check the row in if it exists.
		if ($smileyId)
		{
			// Get a smiley row instance.
			$table = &$this->getTable('Smiley', 'KunenaTable');

			// Get the current user object.
			$user = &JFactory::getUser();

			// Attempt to check the row out.
			if (!$table->checkout($user->get('id'), $smileyId)) {
				$this->setError($table->getError());
				$result	= false;
			}
		}

		return $result;
	}

	/**
	 * Method to validate the form data.
	 *
	 * @access	public
	 * @param	array	The form data.
	 * @return	mixed	Array of filtered data if valid, false otherwise.
	 * @since	1.0
	 */
	function validate($data)
	{
		// Get the form.
		$form = &$this->getForm();

		// Check for an error.
		if ($form === false) {
			return false;
		}

		// Filter and validate the form data.
		$data	= $form->filter($data);
		$return	= $form->validate($data);

		// Check for an error.
		if (JError::isError($return)) {
			$this->setError($return->getMessage());
			return false;
		}

		// Check the validation results.
		if ($return === false)
		{
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message) {
				$this->setError($message);
			}

			return false;
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @access	public
	 * @param	array	The form data.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function save($data)
	{
		$smileyId = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('smiley.id');
		$isNew	= true;

		// Get a smiley row instance.
		$table = &$this->getTable('Smiley', 'KunenaTable');

		// Load the row if saving an existing item.
		if ($smileyId > 0) {
			$table->load($smileyId);
			$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError(JText::sprintf('KUNENA_SMILEY_BIND_FAILED', $table->getError()));
			return false;
		}

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $table->id;
	}

	/**
	 * Method to perform batch operations on a smiley or a set of smilies.
	 *
	 * @access	public
	 * @param	array	An array of commands to perform.
	 * @param	array	An array of smiley ids.
	 * @return	boolean	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function batch($commands, $smileyIds)
	{
		// Sanitize the ids.
		$smileyIds = (array) $smileyIds;
		JArrayHelper::toInteger($smileyIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a smiley row instance.
		$table = &$this->getTable('Smiley', 'KunenaTable');

		/*
		 * BUILD OUT BATCH OPERATIONS
		 */

		return true;
	}
}