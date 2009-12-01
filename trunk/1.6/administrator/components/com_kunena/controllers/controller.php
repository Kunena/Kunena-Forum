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

jimport('joomla.application.component.controller');
jimport('joomla.application.component.helper');

/**
 * Base controller class for Kunena.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaController extends JController
{
    function __construct()
	{
		parent::__construct();

		$task	= JRequest::getCmd( 'task' , '' );

		$document	=& JFactory::getDocument();

		// Placeholder for additional CSS, JS or ajax

	}

	/**
	 * Method to get the appropriate controller.
	 *
	 * @return	object	Kunena Controller
	 * @since	1.6
	 */
	public static function getInstance()
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
	 * @return	void
	 * @since	1.6
	 */
	public function display()
	{
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName	 = JRequest::getWord('view', 'kunena');
		$lName	 = JRequest::getWord('layout', 'default');
		$vFormat = $document->getType();

		if ($view = $this->getView($vName, $vFormat, '', array( 'base_path'=>$this->_basePath)))
		{
			// Do any specific processing for the view.
			switch ($vName)
			{
				default:
					// Get the appropriate model for the view.
					$model = $this->getModel($vName);
					break;
			}

			// Push the model into the view (as default).
			$view->setModel($model, true);

			// Set the view layout.
			$view->setLayout($lName);

			// Push document object into the view.
			$view->assignRef('document', $document);

			// Render the view.
			$view->display();

			// Display Toolbar. View must have setToolBar method
			if( method_exists( $view , 'setToolBar') )
			{
				$view->setToolBar();
			}
		}
	}
}
