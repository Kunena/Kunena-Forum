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

jimport('joomla.application.component.view');

/**
 * The HTML Kunena configuration view.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.6
 */
class KunenaViewConfig extends JView
{
	/**
	 * Method to display the view.
	 *
	 * @param	string	A template file to load.
	 * @return	mixed	JError object on failure, void on success.
	 * @throws	object	JError
	 * @since	1.6
	 */
	public function display($tpl = null)
	{
		// Initialize variables.
		$user	= JFactory::getUser();

		// Load the view data.
		$state	= $this->get('State');
		$form	= $this->get('Options');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Push out the view data.
		$this->assignRef('state',	$state);
		$this->assignRef('options',	$form);

		// Add submenu
		$contents = '';
		ob_start();
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_kunena' . DS . 'views' . DS . 'config' . DS . 'tmpl' . DS . 'navigation.php' );

		$contents = ob_get_contents();
		ob_end_clean();

		$document	=& JFactory::getDocument();

		$document->setBuffer($contents, 'modules', 'submenu');

		// Render the layout.
		parent::display($tpl);
	}

	public function setToolBar()
	{
	    self::_displayMainToolbar();
	}

	protected function _displayMainToolbar()
	{
		JToolBarHelper::title('Kunena: '.JText::_('Configuration'), 'config');

		// We can't use the toolbar helper here because there is no generic link button.
		$bar = &JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'config', 'Import/Export', 'index.php?option=com_kunena&view=config&layout=import');

		JToolBarHelper::divider();

		JToolBarHelper::save('config.save');
		JToolBarHelper::apply('config.apply');
		JToolBarHelper::cancel('config.cancel');
	}

	protected function _displayImportToolbar()
	{
		JToolBarHelper::title('Kunena: '.JText::_('Configuration Import/Export'), 'config');

		JToolBarHelper::custom('config.import', 'import', 'import', 'Import', false);
		JToolBarHelper::custom('config.export', 'export', 'export', 'Export', false);

		// We can't use the toolbar helper here because there is no generic link button.
		$bar = &JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'cancel', 'Cancel', 'index.php?option=com_kunena&view=config');
	}
}