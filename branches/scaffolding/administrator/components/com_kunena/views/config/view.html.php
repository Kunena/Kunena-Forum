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
 * The HTML Kunena configuration view
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaViewConfig extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @access	public
	 * @param	string	$tpl	A template file to load.
	 * @return	mixed	JError object on failure, void on success.
	 * @throws	object	JError
	 * @since	1.0
	 */
	function display($tpl = null)
	{
		// Load the view data.
		$state		= &$this->get('State');
		$params		= &$state->get('config');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');

		// Push out the view data.
		$this->assignRef('state',	$state);
		$this->assignRef('config',	$params);

		$this->_setToolbar();
		parent::display($tpl);
		JRequest::setVar('hidemainmenu', 1);
	}

	/**
	 * Display the toolbar
	 *
	 * @access	protected
	 */
	function _setToolBar()
	{
		JToolBarHelper::title(JText::_('FB Kunena: Configuration'), 'logo');
		JToolBarHelper::apply('config.apply');
		JToolBarHelper::cancel('config.cancel');
	}
}