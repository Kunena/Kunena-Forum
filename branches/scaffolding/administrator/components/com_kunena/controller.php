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

jimport('joomla.application.component.controller');
jimport('joomla.application.component.helper');
//var_dump($_REQUEST);die;
/**
 * Base controller class for Kunena.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaController extends JController
{
	/**
	 * Method to display a view.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function display()
	{
		// Get the current URI to redirect to.
		$uri		= &JURI::getInstance();
		$redirect	= $uri->toString();
		$redirect	= base64_encode($redirect);

		// Check for the JXtended Libraries.
		if (!file_exists( JPATH_SITE.DS.'plugins'.DS.'system'.DS.'jxtended.php' )) {
			JError::raiseWarning(500, JText::sprintf('JX_LIBRARIES_MISSING', $redirect, JUtility::getToken()));
			JHTML::script('setup.js', 'administrator/components/com_kunena/media/js/');
		}
		elseif (!function_exists('jximport')) {
			JError::raiseWarning(500, JText::sprintf('JX_LIBRARIES_DISABLED', $redirect, JUtility::getToken()));
			JHTML::script('setup.js', 'administrator/components/com_kunena/media/js/');
		}

		// Check for ACL initialization.
		$db	= &JFactory::getDBO();
		$db->setQuery(
			'SELECT COUNT(*)' .
			' FROM #__core_acl_acl_sections' .
			' WHERE `value`='.$db->Quote('com_kunena')
		);
		if (!$db->loadResult())
		{
			// Get the setup model.
			$model = &$this->getModel('Setup', 'KunenaModel');

			// Attempt to run the ACL initialization routine.
			$result	= $model->initializeAccessControls();

			// Check for errors.
			if (!$result) {
				JError::raiseWarning(500, JText::sprintf('KUNENA_ACCESS_CONTROL_INITIALIZATION_FAILED', $model->getError()));
			}
		}

		// Get the document object.
		$document = &JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName		= JRequest::getWord('view', 'categories');
		$vFormat	= $document->getType();
		$lName		= JRequest::getWord('layout', 'default');

		if ($view = &$this->getView($vName, $vFormat))
		{
			switch ($vName)
			{
				default:
					$model = &$this->getModel($vName);
					break;
			}

			// Push the model into the view (as default).
			$view->setModel($model, true);
			$view->setLayout($lName);

			// Push document object into the view.
			$view->assignRef('document', $document);

			$view->display();
		}

		// Build the submenu.
		JSubMenuHelper::addEntry(JText::_('KUNENA_SUBMENU_CATEGORIES'),		'index.php?option=com_kunena&view=categories',	$vName == 'categories');
		JSubMenuHelper::addEntry(JText::_('KUNENA_SUBMENU_SMILIES'),		'index.php?option=com_kunena&view=smilies',		$vName == 'smilies');
		JSubMenuHelper::addEntry(JText::_('KUNENA_SUBMENU_RANKS'),			'index.php?option=com_kunena&view=ranks',		$vName == 'ranks');
		JSubMenuHelper::addEntry(JText::_('KUNENA_SUBMENU_MEMBERS'),		'index.php?option=com_kunena&view=members',		$vName == 'members');
		JSubMenuHelper::addEntry(JText::_('KUNENA_SUBMENU_FILES'),			'index.php?option=com_kunena&view=files',		$vName == 'files');
		JSubMenuHelper::addEntry(JText::_('KUNENA_SUBMENU_MAINTENANCE'),	'index.php?option=com_kunena&view=maintenance',	$vName == 'maintenance');
		JSubMenuHelper::addEntry(JText::_('KUNENA_SUBMENU_LEVELS'),			'index.php?option=com_kunena&view=levels',		$vName == 'levels');
		//JSubMenuHelper::addEntry(JText::_('KUNENA_SUBMENU_RULES'),			'index.php?option=com_kunena&view=rules',		$vName == 'rules');
	}

	/**
	 * Method to get the appropriate controller.
	 *
	 * @access	public
	 * @return	object	Kunena Controller
	 * @since	1.0
	 */
	function &getInstance()
	{
		static $instance;

		if (!empty($instance)) {
			return $instance;
		}

		$cmd = JRequest::getCmd('task', 'display');

		// Check for a controller.task command.
		if (strpos($cmd, '.') != false)
		{
			// Explode the controller.task command.
			list($type, $task) = explode('.', $cmd);

			// Define the controller name and path
			$protocol	= JRequest::getWord('protocol');
			$type		= strtolower($type);
			$file		= (!empty($protocol)) ? $type.'.'.$protocol.'.php' : $type.'.php';
			$path		= JPATH_COMPONENT.DS.'controllers'.DS.$file;

			// If the controller file path exists, include it ... else die with a 500 error.
			if (file_exists($path)) {
				require_once($path);
			} else {
				JError::raiseError(500, JText::sprintf('KUNENA_INVALID_CONTROLLER', $type));
			}

			JRequest::setVar('task', $task);
		} else {
			// Base controller, just set the task.
			$type = null;
			$task = $cmd;
		}

		// Set the name for the controller and instantiate it.
		$class = 'KunenaController'.ucfirst($type);
		if (class_exists($class)) {
			$instance = new $class();
		} else {
			JError::raiseError(500, JText::sprintf('KUNENA_INVALID_CONTROLLER_CLASS', $class));
		}

		return $instance;
	}
}
