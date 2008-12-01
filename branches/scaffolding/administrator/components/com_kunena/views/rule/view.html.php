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

jimport('joomla.application.component.view');

/**
 * The HTML Kunena Rule View
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaViewRule extends JView
{
	/**
	 * Display the view
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function display($tpl = null)
	{
		$state		= $this->get('State');
		$item		= $this->get('Item');

		$actions	= $this->get('Actions');
		$userGroups	= $this->get('UserGroups');
		$assets		= $this->get('Assets');
		$assetGroups= $this->get('AssetGroups');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('state',		$state);
		$this->assignRef('item',		$item);
		$this->assignRef('actions',		$actions);
		$this->assignRef('usergroups',	$userGroups);
		$this->assignRef('assets',		$assets);
		$this->assignRef('assetgroups',	$assetGroups);

		parent::display($tpl);
	}

	/**
	 * Build the default toolbar.
	 *
	 * @access	protected
	 * @return	void
	 * @since	1.0
	 */
	function buildDefaultToolBar()
	{
		JToolBarHelper::title('Kunena: '.JText::_(($this->item->id) ? 'Edit Access Rule' : 'Add Access Rule'), 'logo');

		JToolBarHelper::save('rule.save');
		JToolBarHelper::apply('rule.apply');
		JToolBarHelper::cancel('rule.cancel');
	}

	/**
	 * Check to see if an item should be checked.
	 *
	 * @access	protected
	 * @param	array	$array		The array of values to check the value against.
	 * @param	scalar	$value		The value to check to see if it is set.
	 * @param	scalar	$section	The section of the array of valuels to check the value against.
	 * @return	boolean	True if the item should be checked.
	 * @since	1.0
	 */
	function isChecked(&$array, $value, $section = null)
	{
		$values = (array) ($section == null) ? $array : $array[$section];
		return in_array($value, $values) ? 'checked="checked"' : '';
	}
}
