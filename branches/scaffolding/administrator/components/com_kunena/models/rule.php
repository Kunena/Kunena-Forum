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

jximport('jxtended.application.component.modelrule');
jximport('jxtended.database.query');

/**
 * Rule model for Kunena.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaModelRule extends JXModelRule
{
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
		// If the model state is uninitialized lets set some values we will need from the request.
		if (!$this->__state_set)
		{
			$app = &JFactory::getApplication();

			$this->setState('section.value',	JRequest::getVar('section', 'com_kunena'));
			$this->setState('acl.type',			JRequest::getVar('type', 1));
			$this->setState('default.name',		JRequest::getVar('name'));

			$this->__state_set = true;
		}

		return parent::getState($property, $default);
	}

	/**
	 * Method to get an access control rule object.
	 *
	 * @access	public
	 * @param	integer	$id	The rule ID.
	 * @return	object	The access control rule object.
	 * @since	1.0
	 */
	function &getItem($id = null)
	{
		$item = &parent::getItem($id);

		if (empty($item->id)) {
			$item->name = $this->getState('default.name');
		}

		return $item;
	}

	/**
	 * Gets the Form
	 */
	function &getForm($type = 'view')
	{
		jximport('jxtended.form.helper');
		JXFormHelper::addIncludePath(dirname(__FILE__));

		if ($type == 'model') {
			$result = &JXFormHelper::getModel('acl');
		} else {
			$result = &JXFormHelper::getView('acl');
		}
		if (JError::isError($result)) {
			echo $result->message;
		}
		return $result;
	}
}
