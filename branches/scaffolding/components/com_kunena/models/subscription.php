<?php
/**
 * @version		$Id$
 * @package		JXtended.Subscriptions
 * @subpackage	com_subscriptions
 * @copyright	(C) 2008 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License.
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');
jximport('jxtended.database.query');

/**
 * Subscription Model for JXtended Subscriptions
 *
 * @package		JXtended.Subscriptions
 * @subpackage	com_subscriptions
 * @version		1.0
 */
class SubscriptionsModelSubscription extends JModel
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

			// Attempt to auto-load the subscription id.
			if (!$catId = (int)$app->getUserState('com_subscriptions.edit.subscription.id')) {
				$catId = (int)JRequest::getInt('cat_id');
			}

			// Only set the subscription id if there is a value.
			if ($catId) {
				$this->setState('subscription.id', $catId);
			}

			// Set the model state set flat to true.
			$this->__state_set = true;
		}

		$value = parent::getState($property);
		return (is_null($value) ? $default : $value);
	}

	/**
	 * Method to get a subscription item.
	 *
	 * @access	public
	 * @param	integer	The id of the subscription to get.
	 * @return	mixed	Subscription data object on success, false on failure.
	 * @since	1.0
	 */
	function &getItem($subId = null)
	{
		// Initialize variables.
		$subId = (!empty($subId)) ? $subId : (int)$this->getState('subscription.id');
		$false	= false;

		// Get a subscription row instance.
		$table = &$this->getTable('Subscription', 'SubscriptionsTable');

		// Attempt to load the row.
		$return = $table->load($subId);

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
	 * Method to get the subscription form.
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
		JXForm::addFieldPath(JPATH_ADMINISTRATOR.'/components/com_members/models/fields');
		$form = &JXForm::getInstance('subscription');

		// Set the rule path.
		jximport('jxtended.form.validator');
		JXFormValidator::addRulePath(JPATH_COMPONENT.'/models/rules');

		// Check for an error.
		if (JError::isError($form)) {
			$this->setError($form->getMessage());
			return $false;
		}

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_subscriptions.edit.subscription.data', array());

		// Bind the form data if present.
		if (!empty($data)) {
			$form->bind($data);
		}

		return $form;
	}

	/**
	 * Method to publish subscriptions.
	 *
	 * @access	public
	 * @param	array	The ids of the items to publish.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function publish($subIds)
	{
		// Sanitize the ids.
		$subIds = (array) $subIds;
		JArrayHelper::toInteger($subIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a provider row instance.
		$table = &$this->getTable('Subscription', 'SubscriptionsTable');

		// Attempt to publish the items.
		if (!$table->publish($subIds, 1, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to unpublish subscriptions.
	 *
	 * @access	public
	 * @param	array	The ids of the items to unpublish.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function unpublish($subIds)
	{
		// Sanitize the ids.
		$subIds = (array) $subIds;
		JArrayHelper::toInteger($subIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a provider row instance.
		$table = &$this->getTable('Subscription', 'SubscriptionsTable');

		// Attempt to unpublish the items.
		if (!$table->publish($subIds, 0, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to put subscriptions in the trash.
	 *
	 * @access	public
	 * @param	array	The ids of the items to trash.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function trash($subIds)
	{
		// Sanitize the ids.
		$subIds = (array) $subIds;
		JArrayHelper::toInteger($subIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a provider row instance.
		$table = &$this->getTable('Subscription', 'SubscriptionsTable');

		// Attempt to trash the items.
		if (!$table->publish($subIds, -2, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to delete subscriptions.
	 *
	 * @access	public
	 * @param	array	An array of subscription ids.
	 * @return	boolean	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function delete($subIds)
	{
		// Sanitize the ids.
		$subIds = (array) $subIds;
		JArrayHelper::toInteger($subIds);

		// Get a provider row instance.
		$table = &$this->getTable('Subscription', 'SubscriptionsTable');

		// Iterate the subscriptions to delete each one.
		foreach ($subIds as $subId)
		{
			$table->delete($subId);
		}

		return true;
	}

	/**
	 * Method to toggle settings on a subscription or a set of subscriptions.
	 *
	 * The method toggles a property as either 1 or 0 for the given
	 * property.
	 *
	 * @access	public
	 * @param	array	An array of subscription ids.
	 * @param	string	The property to toggle.
	 * @return	boolean	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function toggleProperty($subIds, $property)
	{
		// Sanitize the ids.
		$subIds = (array) $subIds;
		JArrayHelper::toInteger($subIds);

		// Get a subscription row instance.
		$table = &$this->getTable('Subscription', 'SubscriptionsTable');

		// Ensure the property exists.
		if (isset($table->$property)) {
			$this->setError('JX_INVALID_PROPERTY');
			return false;
		}

		// Iterate the subscriptoins to perform the toggle on each one.
		foreach ($subIds as $subId)
		{
			// Attempt to load the row.
			$return = $table->load($subId);

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

	function setProperty($subIds, $property, $value)
	{
		// Sanitize the ids.
		$subIds = (array) $subIds;
		JArrayHelper::toInteger($subIds);

		// Get a subscription row instance.
		$table = &$this->getTable('Subscription', 'SubscriptionsTable');

		// Ensure the property exists.
		if (isset($table->$property)) {
			$this->setError('JX_INVALID_PROPERTY');
			return false;
		}

		// Set the property for relevant rows.
		$this->_db->setQuery(
			'UPDATE `#__jxsubscriptions_subscriptions`' .
			' SET '.$this->_db->nameQuote($property).' = '.$this->_db->Quote($value) .
			' WHERE `id` IN ('.implode(',', $subIds).')'
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
	 * Adjust the subscription ordering.
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

		// Get a provider row instance.
		$table = &$this->getTable('Subscription', 'SubscriptionsTable');

		// Attempt to adjust the row ordering.
		if (!$table->ordering($move, $id)) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to check in a subscription.
	 *
	 * @access	public
	 * @param	integer	The id of the row to check in.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function checkin($subId = null)
	{
		// Initialize variables.
		$subId	= (!empty($subId)) ? $subId : (int)$this->getState('subscription.id');
		$result	= true;

		// Only attempt to check the row in if it exists.
		if ($subId)
		{
			// Get a subscription row instance.
			$table = &$this->getTable('Subscription', 'SubscriptionsTable');

			// Attempt to check the row in.
			if (!$table->checkin($subId)) {
				$this->setError($table->getError());
				$result	= false;
			}
		}

		return $result;
	}

	/**
	 * Method to check out a subscription.
	 *
	 * @access	public
	 * @param	integer	The id of the row to check out.
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	function checkout($subId = null)
	{
		// Initialize variables.
		$subId	= (!empty($subId)) ? $subId : (int)$this->getState('subscription.id');
		$result	= true;

		// Only attempt to check the row in if it exists.
		if ($subId)
		{
			// Get a subscription row instance.
			$table = &$this->getTable('Subscription', 'SubscriptionsTable');

			// Get the current user object.
			$user = &JFactory::getUser();

			// Attempt to check the row out.
			if (!$table->checkout($user->get('id'), $subId)) {
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
		$subId = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('subscription.id');
		$isNew	= true;

		// Get a subscription row instance.
		$table = &$this->getTable('Subscription', 'SubscriptionsTable');

		// Load the row if saving an existing item.
		if ($subId > 0) {
			$table->load($subId);
			$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError(JText::sprintf('SUBSCRIPTIONS_SUBSCRIPTION_BIND_FAILED', $table->getError()));
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
	 * Method to perform batch operations on a subscription or a set of subscriptions.
	 *
	 * @access	public
	 * @param	array	An array of commands to perform.
	 * @param	array	An array of subscription ids.
	 * @return	boolean	Returns true on success, false on failure.
	 * @since	1.0
	 */
	function batch($commands, $subIds)
	{
		// Sanitize the ids.
		$subIds = (array) $subIds;
		JArrayHelper::toInteger($subIds);

		// Get the current user object.
		$user = &JFactory::getUser();

		// Get a subscription row instance.
		$table = &$this->getTable('Subscription', 'SubscriptionsTable');

		/*
		 * BUILD OUT BATCH OPERATIONS
		 */

		return true;
	}
}