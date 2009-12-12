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
class KunenaViewInstall extends JView
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
		$user = JFactory::getUser();

		// Load the view data.
		$this->assignRef('state', $this->get('State'));
		$this->assignRef('versionWarning', $this->get('VersionWarning'));
		$this->assignRef('requirements', $this->get('Requirements'));
		$this->assign('installedVersion', $this->get('InstalledVersion'));
		$this->assign('installAction', $this->get('InstallAction'));

		// Push out the view data.
		$this->assignRef('state',	$state);

		$this->assign('link', JURI::root().'administrator/index.php?option=com_kunena&view=install&task=install');

		$search = array ('#COMPONENT_OLD#','#VERSION_OLD#','#BUILD_OLD#','#VERSION#','#BUILD#');
		$replace = array ($this->installedVersion->component, $this->installedVersion->version, $this->installedVersion->build, KUNENA_VERSION, KUNENA_VERSION_BUILD);
		$this->assign('txt_action', str_replace($search, $replace, JText::_('K_INSTALL_LONG_'.$this->installAction)));
		$this->assign('txt_install', str_replace($search, $replace, JText::_('K_INSTALL_'.$this->installAction)));

		// Render the layout.
		$app =& JFactory::getApplication();
		if (!empty($this->requirements->fail)) $app->enqueueMessage('Kunena Forum Installation Failed!', 'error');
		else if (!empty($this->versionWarning)) $app->enqueueMessage($this->versionWarning, 'notice');
		JRequest::setVar('hidemainmenu', 1);
		parent::display($tpl);
	}

	/**
	 * Private method to set the toolbar for this view
	 *
	 * @access private
	 *
	 * @return null
	 **/
	function setToolBar()
	{
		// Set the titlebar text
		JToolBarHelper::title('<span>KUNENA '.KUNENA_VERSION.'</span> '. JText::_( 'Installer' ), 'about' );

	}
}