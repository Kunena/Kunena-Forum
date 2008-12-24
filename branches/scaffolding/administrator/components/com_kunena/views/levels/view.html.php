<?php
/**
 * @version		$Id$
 * @package		JXtended.Members
 * @subpackage	com_members
 * @copyright	(C) 2008 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://jxtended.com
 */

defined('_JEXEC') or die('Invalid Request.');

jimport('joomla.application.component.view');

/**
 * @package		JXtended.Members
 * @subpackage	com_members
 */
class KunenaViewLevels extends JView
{
	/**
	 * Display the view
	 *
	 * @access	public
	 */
	function display($tpl = null)
	{
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		$this->_setToolBar();
		parent::display($tpl);
	}

	/**
	 * Display the toolbar
	 *
	 * @access	private
	 */
	function _setToolBar()
	{
		$state = $this->get('State');
		JToolBarHelper::title(JText::_('JX Members: Levels'), 'logo');
		JToolBarHelper::deleteList('', 'level.delete');
		JToolBarHelper::custom('rule.editlevel', 'edit.png', 'edit_f2.png', 'Edit', true);
		JToolBarHelper::custom('rule.addlevel', 'new.png', 'new_f2.png', 'New', false);
	}
}
