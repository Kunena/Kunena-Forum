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
 * Category model class for Kunena.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaModelCategory extends JModel
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

			// Attempt to auto-load the category id.
			if (!$catId = (int)$app->getUserState('com_kunena.edit.category.id')) {
				$catId = (int)JRequest::getInt('cat_id');
			}

			// Only set the category id if there is a value.
			if ($catId) {
				$this->setState('category.id', $catId);
			}

			// Set the model state set flat to true.
			$this->__state_set = true;
		}

		$value = parent::getState($property);
		return (is_null($value) ? $default : $value);
	}

	/**
	 * Method to get a category item.
	 *
	 * @access	public
	 * @param	integer	The id of the category to get.
	 * @return	mixed	Category data object on success, false on failure.
	 * @since	1.0
	 */
	function &getItem($catId = null)
	{
		// Initialize variables.
		$catId = (!empty($catId)) ? $catId : (int)$this->getState('category.id');
		$false	= false;

		// Get a category row instance.
		$table = &$this->getTable('Category', 'KunenaTable');

		// Attempt to load the row.
		$return = $table->load($catId);

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
	 * Method to get the category form.
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
		jximport('jxtended.form.field');
		jximport('jxtended.form.fields.list');
		JXForm::addFormPath(JPATH_COMPONENT.'/models/forms');
		JXForm::addFieldPath(JPATH_COMPONENT.'/models/fields');
		$form = &JXForm::getInstance('jxform', 'category', true, array('array' => true));

		// Check for an error.
		if (JError::isError($form)) {
			$this->setError($form->getMessage());
			return $false;
		}

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_kunena.edit.category.data', array());

		// Bind the form data if present.
		if (!empty($data)) {
			$form->bind($data);
		}

		return $form;
	}

	function getPermissions($catId = null)
	{
		// Initialize variables.
		$catId = (!empty($catId)) ? $catId : (int)$this->getState('category.id');
		$perms = array();

		jximport('jxtended.access.permission.simplerule');

		$rule = new JSimpleRule();
		$rule->load('com_kunena.category.post', 'category.'.$catId);
		$perms['post'] = $rule->getUserGroups();

		$rule = new JSimpleRule();
		$rule->load('com_kunena.category.manage', 'category.'.$catId);
		$perms['manage'] = $rule->getUserGroups();

		return $perms;
	}

	/**
	 * Method to publish categories.
	 *
	 * @access	public
	 * @param	array	The ids of the items to publish.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function publish($catIds)
	{
		// Sanitize the ids.
		$catIds = (array) $catIds;
		JArrayHelper::toInteger($catIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a category row instance.
		$table = &$this->getTable('Category', 'KunenaTable');

		// Attempt to publish the items.
		if (!$table->publish($catIds, 1, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to unpublish categories.
	 *
	 * @access	public
	 * @param	array	The ids of the items to unpublish.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function unpublish($catIds)
	{
		// Sanitize the ids.
		$catIds = (array) $catIds;
		JArrayHelper::toInteger($catIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a category row instance.
		$table = &$this->getTable('Category', 'KunenaTable');

		// Attempt to unpublish the items.
		if (!$table->publish($catIds, 0, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to put categories in the trash.
	 *
	 * @access	public
	 * @param	array	The ids of the items to trash.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function trash($catIds)
	{
		// Sanitize the ids.
		$catIds = (array) $catIds;
		JArrayHelper::toInteger($catIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a category row instance.
		$table = &$this->getTable('Category', 'KunenaTable');

		// Attempt to trash the items.
		if (!$table->publish($catIds, -2, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to delete categories.
	 *
	 * @access	public
	 * @param	array	An array of category ids.
	 * @return	boolean	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function delete($catIds)
	{
		// Sanitize the ids.
		$catIds = (array) $catIds;
		JArrayHelper::toInteger($catIds);

		// Get a category row instance.
		$table = &$this->getTable('Category', 'KunenaTable');

		// Iterate the categories to delete each one.
		foreach ($catIds as $catId)
		{
			$table->delete($catId);
		}

		// Rebuild the nested set tree.
		$table->rebuild();

		return true;
	}

	/**
	 * Method to toggle settings on a category or a set of categories.
	 *
	 * The method toggles a property as either 1 or 0 for the given
	 * property.
	 *
	 * @access	public
	 * @param	array	An array of category ids.
	 * @param	string	The property to toggle.
	 * @return	boolean	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function toggleProperty($catIds, $property)
	{
		// Sanitize the ids.
		$catIds = (array) $catIds;
		JArrayHelper::toInteger($catIds);

		// Get a category row instance.
		$table = &$this->getTable('Category', 'KunenaTable');

		// Ensure the property exists.
		if (isset($table->$property)) {
			$this->setError('KUNENA_INVALID_PROPERTY');
			return false;
		}

		// Iterate the categories to perform the toggle on each one.
		foreach ($catIds as $catId)
		{
			// Attempt to load the row.
			$return = $table->load($catId);

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

	function setProperty($catIds, $property, $value)
	{
		// Sanitize the ids.
		$catIds = (array) $catIds;
		JArrayHelper::toInteger($catIds);

		// Get a category row instance.
		$table = &$this->getTable('Category', 'KunenaTable');

		// Ensure the property exists.
		if (isset($table->$property)) {
			$this->setError('KUNENA_INVALID_PROPERTY');
			return false;
		}

		// Set the property for relevant rows.
		$this->_db->setQuery(
			'UPDATE `#__kunena_categories`' .
			' SET '.$this->_db->nameQuote($property).' = '.$this->_db->Quote($value) .
			' WHERE `id` IN ('.implode(',', $catIds).')'
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
	 * Adjust the category ordering.
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

		// Get a category row instance.
		$table = &$this->getTable('Category', 'KunenaTable');

		// Attempt to adjust the row ordering.
		if (!$table->ordering($move, $id)) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to check in a category.
	 *
	 * @access	public
	 * @param	integer	The id of the row to check in.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function checkin($catId = null)
	{
		// Initialize variables.
		$catId	= (!empty($catId)) ? $catId : (int)$this->getState('category.id');
		$result	= true;

		// Only attempt to check the row in if it exists.
		if ($catId)
		{
			// Get a category row instance.
			$table = &$this->getTable('Category', 'KunenaTable');

			// Attempt to check the row in.
			if (!$table->checkin($catId)) {
				$this->setError($table->getError());
				$result	= false;
			}
		}

		return $result;
	}

	/**
	 * Method to check out a category.
	 *
	 * @access	public
	 * @param	integer	The id of the row to check out.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function checkout($catId = null)
	{
		// Initialize variables.
		$catId	= (!empty($catId)) ? $catId : (int)$this->getState('category.id');
		$result	= true;

		// Only attempt to check the row in if it exists.
		if ($catId)
		{
			// Get a category row instance.
			$table = &$this->getTable('Category', 'KunenaTable');

			// Get the current user object.
			$user = &JFactory::getUser();

			// Attempt to check the row out.
			if (!$table->checkout($user->get('id'), $catId)) {
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
		$catId = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('category.id');
		$isNew	= true;

		// Extract the permissions data from the data array.
		$permissions = $data['permissions'];
		unset($data['permissions']);

		// Get a category row instance.
		$table = &$this->getTable('Category', 'KunenaTable');

		// Load the row if saving an existing item.
		if ($catId > 0) {
			$table->load($catId);
			$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError(JText::sprintf('KUNENA_CATEGORY_BIND_FAILED', $table->getError()));
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

		// Get the root category.
		$this->_db->setQuery(
			'SELECT `id`' .
			' FROM `#__kunena_categories`' .
			' WHERE `parent_id` = 0',
			0, 1
		);
		$rootId	= $this->_db->loadResult();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Rebuild the hierarchy.
		if (!$table->rebuild($rootId)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Build the category path.
		if (!$table->buildPath($table->id)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		jximport('jxtended.access.helper');
		$return = JXAccessHelper::registerSimpleRule('com_kunena.category.post', 'category.'.$table->id, $permissions['post']);
		if (JError::isError($return)) {
			$this->setError($return->getMessage());
			return false;
		}

		$return = JXAccessHelper::registerSimpleRule('com_kunena.category.manage', 'category.'.$table->id, $permissions['manage']);
		if (JError::isError($return)) {
			$this->setError($return->getMessage());
			return false;
		}

		return $table->id;
	}

	/**
	 * Method to perform batch operations on a category or a set of categories.
	 *
	 * @access	public
	 * @param	array	An array of commands to perform.
	 * @param	array	An array of category ids.
	 * @return	boolean	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function batch($commands, $catIds)
	{
		// Sanitize the ids.
		$catIds = (array) $catIds;
		JArrayHelper::toInteger($catIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a category row instance.
		$table = &$this->getTable('Category', 'KunenaTable');

		/*
		 * BUILD OUT BATCH OPERATIONS
		 */

		return true;
	}
}