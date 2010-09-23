<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.controller' );
jimport ( 'joomla.application.component.helper' );

/**
 * Base controller class for Kunena.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaController extends JController {
	function __construct() {
		parent::__construct ();

		$lang = JFactory::getLanguage();
		if (!$lang->load('com_kunena.install',JPATH_ADMINISTRATOR)) {
			$lang->load('com_kunena.install',KPATH_ADMIN);
		}
		if (!$lang->load('com_kunena',JPATH_SITE)) {
			$lang->load('com_kunena',KPATH_SITE);
		}
		if (!$lang->load('com_kunena',JPATH_ADMINISTRATOR, null, true)) {
			$lang->load('com_kunena',KPATH_ADMIN);
		}
	}

	/**
	 * Method to get the appropriate controller.
	 *
	 * @return	object	Kunena Controller
	 * @since	1.6
	 */
	public static function getInstance() {
		static $instance = null;

		if (! empty ( $instance )) {
			return $instance;
		}

		$lang = JFactory::getLanguage();
		if (!$lang->load('com_kunena_base',JPATH_ADMINISTRATOR)) {
			$lang->load('com_kunena_base',KPATH_ADMIN);
		}

		$view = strtolower ( JRequest::getCmd ( 'view', 'categories' ) );
		$path = KPATH_ADMIN . DS . 'controllers' . DS . $view . '.php';

		// If the controller file path exists, include it ... else die with a 500 error.
		if (file_exists ( $path )) {
			require_once $path;
		} else {
			JError::raiseError ( 500, JText::sprintf ( 'COM_KUNENA_INVALID_CONTROLLER', ucfirst ( $view ) ) );
		}

		// Set the name for the controller and instantiate it.
		$class = 'KunenaController' . ucfirst ( $view );
		if (class_exists ( $class )) {
			$instance = new $class ();
		} else {
			JError::raiseError ( 500, JText::sprintf ( 'COM_KUNENA_INVALID_CONTROLLER_CLASS', $class ) );
		}

		return $instance;
	}

	/**
	 * Method to display a view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function display() {
		// Version warning
		$version = new KunenaVersion();
		$version_warning = $version->getVersionWarning('COM_KUNENA_VERSION_INSTALLED');
		if (! empty ( $version_warning )) {
			$app = JFactory::getApplication ();
			$app->enqueueMessage ( $version_warning, 'notice' );
		}

		// Get the document object.
		$document = JFactory::getDocument ();

		// Set the default view name and format from the Request.
		$vName = JRequest::getWord ( 'view', 'categories' );
		$lName = JRequest::getWord ( 'layout', 'default' );
		$vFormat = $document->getType ();

		$view = $this->getView ( $vName, $vFormat, '', array ('base_path' => $this->_basePath ) );
		if ($view) {
			// Do any specific processing for the view.
			switch ($vName) {
				default :
					// Get the appropriate model for the view.
					$model = $this->getModel ( $vName );
					break;
			}

			// Push the model into the view (as default).
			$view->setModel ( $model, true );

			// Set the view layout.
			$view->setLayout ( $lName );

			// Push document object into the view.
			$view->assignRef ( 'document', $document );

			// Render the view.
			$view->display ();

			// Display Toolbar. View must have setToolBar method
			if (method_exists ( $view, 'setToolBar' )) {
				$view->setToolBar ();
			}
		}
	}
}
