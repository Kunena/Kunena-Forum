<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport('joomla.application.component.view');

/**
 * The HTML Kunena configuration view.
 *
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
		if ($this->getLayout() == 'schema') {
			parent::display($tpl);
			return;
		}
		// Initialize variables.
		$user = JFactory::getUser();

		// Load the view data.
		$this->model = $this->get('Model');
		$this->state = $this->get('State');
		$this->step = $this->get('Step');
		$this->steps = $this->get('Steps');
		$this->status = $this->get('Status');

		$this->error = $this->get('Error');
		$this->requirements = $this->get('Requirements');
		$this->versions = $this->get('DetectVersions');

		require_once(KPATH_ADMIN.'/install/version.php');
		$version = new KunenaVersion();
		$this->versionWarning = $version->getVersionWarning('COM_KUNENA_INSTALL_WARNING');

		// Render the layout.
		$app = JFactory::getApplication();
		if (!empty($this->requirements->fail) || !empty($this->error)) $app->enqueueMessage(JText::_('COM_KUNENA_INSTALL_FAILED'), 'error');
		else if ($this->step && isset($this->steps[$this->step+1])) $app->enqueueMessage(JText::_('COM_KUNENA_INSTALL_DO_NOT_INTERRUPT'), 'notice');
		else if (!isset($this->steps[$this->step+1])) $app->enqueueMessage(JText::_('COM_KUNENA_INSTALL_SUCCESS'));
		else if (!empty($this->versionWarning)) $app->enqueueMessage($this->versionWarning, 'notice');
		JRequest::setVar('hidemainmenu', 1);

		$this->go = JRequest::getCmd('go', '');

		$session = JFactory::getSession();
		$this->cnt = $session->get('kunena.reload', 1);

		if ($this->step) {
			// Output enqueued messages from previous reloads (to show Joomla warnings)
			$queue = (array) $session->get('kunena.queue');
			foreach ($queue as $item) {
				if (is_array($item) && $item['type'] != 'message') {
					$app->enqueueMessage($item['message'], $item['type']);
				}
			}
		}

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
		JToolBarHelper::title('<span>Kunena '.KunenaForum::version().'</span> '. JText::_( 'COM_KUNENA_INSTALLER' ), 'kunena.png' );

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
		if ($this->error) return "location.replace('index.php?option=com_kunena&view=install&task=restart&".JUtility::getToken()."=1');";
		return "location.replace('index.php?option=com_kunena&view=install&task=run&n={$this->cnt}&".JUtility::getToken()."=1');";
	}

	function getActionText($version, $type='', $action=null) {
		return $this->model->getActionText($version, $type, $action);
	}

	function displaySchema() {
		require_once KPATH_ADMIN . '/install/schema.php';
		$schema = new KunenaModelSchema ();
		$create = $schema->getCreateSQL();
		echo '<textarea cols="80" rows="50">';
		echo $this->escape ( $schema->getSchema ()->saveXML () );
		echo '</textarea>';
		if (KunenaForum::isDev()) {
			echo '<textarea cols="80" rows="20">';
			foreach ( $create as $item ) {
				echo $this->escape($item ['sql']) . "\n\n";
			}
			echo '</textarea>';
		}
	}

	function displaySchemaDiff() {
		require_once KPATH_ADMIN . '/install/schema.php';
		$schema = new KunenaModelSchema ();
		$diff = $schema->getDiffSchema ();
		$sql = $schema->getSchemaSQL ( $diff );
		echo '<textarea cols="80" rows="20">';
		echo $this->escape ( $diff->saveXML () );
		echo '</textarea>';
		if (KunenaForum::isDev()) {
			echo '<textarea cols="80" rows="20">';
			foreach ( $sql as $item ) {
				echo $this->escape($item ['sql']) . "\n\n";
			}
			echo '</textarea>';
		}
	}
}
