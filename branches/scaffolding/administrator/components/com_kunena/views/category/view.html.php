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
 * The HTML Kunena Category View
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaViewCategory extends JView
{
	/**
	 * Display the view
	 *
	 * @access	public
	 */
	function display($tpl = null)
	{
		$state	= $this->get('State');
		$item	= $this->get('Item');
		$form	= $this->get('Form');
		$perms	= $this->get('Permissions');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Set the form name and action.
		$form->setName('adminForm');
		$form->setAction(JRoute::_('index.php?option=com_kunena'));

		// Bind the item data to the form object.
		if ($item) {
			$form->bind($item);
		}

		$this->assignRef('state',	$state);
		$this->assignRef('item',	$item);
		$this->assignRef('form',	$form);
		$this->assignRef('perms',	$perms);

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
		$user = &JFactory::getUser();
		if (is_object($this->item)) {
			$isCheckedOut	= JTable::isCheckedOut($user->get('id'), $this->item->checked_out);
			$isNew			= ($this->item->id == 0);
		}
		else {
			$isCheckedOut	= false;
			$isNew			= true;
		}

		JToolBarHelper::title('Kunena: '.JText::_(($isCheckedOut ? 'View Item' : ($isNew ? 'Add Category' : 'Edit Category'))), 'k');
		if (!$isNew) {
			JToolBarHelper::custom('category.save2copy', 'copy.png', 'copy_f2.png', 'Save To Copy', false);
		}
		if (!$isCheckedOut) {
			JToolBarHelper::custom('category.save2new', 'new.png', 'new_f2.png', 'Save And New', false);
			JToolBarHelper::save('category.save');
			JToolBarHelper::apply('category.apply');
		}
		JToolBarHelper::cancel('category.cancel');
	}
}