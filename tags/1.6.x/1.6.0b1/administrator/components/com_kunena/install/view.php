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
		$this->assign('step', $this->get('Step'));
		$this->assignRef('steps', $this->get('Steps'));
		$this->assignRef('status', $this->get('Status'));
		$this->assign('error', $this->get('Error'));

		$this->assignRef('requirements', $this->get('Requirements'));
		$this->assign('installedVersion', $this->get('InstalledVersion'));
		$this->assign('installAction', $this->get('InstallAction'));

		require_once(KPATH_ADMIN.'/install/version.php');
		$version = new KunenaVersion();
		$this->assignRef('versionWarning', $version->getVersionWarning('COM_KUNENA_INSTALL_WARNING'));

		// Push out the view data.
		$this->assign('link', JURI::root().'administrator/index.php?option=com_kunena&view=install&task=install');

		$search = array ('#COMPONENT_OLD#','#VERSION_OLD#','#BUILD_OLD#','#VERSION#','#BUILD#');
		$replace = array ($this->installedVersion->component, $this->installedVersion->version, $this->installedVersion->build, Kunena::version(), Kunena::versionBuild());
		$this->assign('txt_action', str_replace($search, $replace, JText::_('COM_KUNENA_INSTALL_LONG_'.$this->installAction)));
		$this->assign('txt_install', str_replace($search, $replace, JText::_('COM_KUNENA_INSTALL_'.$this->installAction)));

		// Render the layout.
		$app =& JFactory::getApplication();
		if (!empty($this->requirements->fail) || !empty($this->error)) $app->enqueueMessage(JText::_('COM_KUNENA_INSTALL_FAILED'), 'error');
		else if ($this->step && isset($this->steps[$this->step+1])) $app->enqueueMessage(JText::_('COM_KUNENA_INSTALL_DO_NOT_INTERRUPT'), 'notice');
		else if (!isset($this->steps[$this->step+1])) $app->enqueueMessage(JText::_('COM_KUNENA_INSTALL_SUCCESS'));
		else if (!empty($this->versionWarning)) $app->enqueueMessage($this->versionWarning, 'notice');
		JRequest::setVar('hidemainmenu', 1);

		$this->assign('go', JRequest::getCmd('go', ''));

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
		JToolBarHelper::title('<span>'.Kunena::version().'</span> '. JText::_( 'COM_KUNENA_INSTALLER' ), 'kunena.png' );

	}

	function showSteps() {
		foreach ($this->steps as $key=>$value) {
			if (empty($value['step'])) continue;
			echo '<div class="step'.($key <= $this->step ? "-on" : "-off").'">'.$key.'. '.$value['menu'].'</div>';
		}
	}

	function getAction() {
		if (!$this->step) return JText::_('COM_KUNENA_BUTTON_INSTALL');
		return JText::_($this->error ? 'COM_KUNENA_BUTTON_RETRY' : ($this->step == count($this->steps)-1 ? 'COM_KUNENA_BUTTON_FINISH' : 'COM_KUNENA_BUTTON_NEXT'));
	}

	function getActionURL() {
		if ($this->error) return "location.replace('index.php?option=com_kunena&view=install&task=restart');";
		return "location.replace('index.php?option=com_kunena&view=install&task=install');";
	}

}
