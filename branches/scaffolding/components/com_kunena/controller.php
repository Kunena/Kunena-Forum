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

/*
 * SOME TIME-BASED DEFINED CONSTANTS
 */
define('KUNENA_SECONDS_IN_HOUR', 3600);
define('KUNENA_SECONDS_IN_YEAR', 31536000);

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

	/**
	 * Method to display a view.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function display()
	{
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
	}
}
