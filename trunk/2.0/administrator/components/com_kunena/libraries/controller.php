<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.controller' );
jimport ( 'joomla.application.component.helper' );
jimport ( 'joomla.error.profiler' );

require_once KPATH_SITE . '/router.php';

/**
 * Base controller class for Kunena.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaController extends JController {
	var $_escape = 'htmlspecialchars';

	function __construct() {
		parent::__construct ();
	}

	/**
	 * Method to get the appropriate controller.
	 *
	 * @return	object	Kunena Controller
	 * @since	1.6
	 */
	public static function getInstance($reload = false) {
		static $instance = null;

		if (! empty ( $instance ) && !isset($instance->home)) {
			return $instance;
		}

		// Display time it took to create the entire page in the footer
		$starttime = JProfiler::getmicrotime() - KunenaRouter::$time;
		$view = strtolower ( JRequest::getWord ( 'view', 'none' ) );

		$app = JFactory::getApplication();
		// FIXME: loading languages in Joomla is SLOW (30ms)!
		if ($app->isAdmin()) {
			$lang = JFactory::getLanguage();
			$lang->load('com_kunena',JPATH_SITE);
			$lang->load('com_kunena.install',JPATH_ADMINISTRATOR);
		} else {
			$home = $app->getMenu ()->getActive ();
			if (!$reload && !empty ( $home->query ['view'] ) && $home->query ['view'] == 'home' && !JRequest::getWord ( 'task' )) {
				$view = 'home';
			}
		}
		$path = JPATH_COMPONENT . DS . 'controllers' . DS . $view . '.php';

		// If the controller file path exists, include it ... else die with a 500 error.
		if (file_exists ( $path )) {
			require_once $path;
		} else {
			JError::raiseError ( 500, JText::sprintf ( 'COM_KUNENA_INVALID_CONTROLLER', ucfirst ( $view ) ) );
		}

		// Set the name for the controller and instantiate it.
		if ($app->isAdmin()) {
			$class = 'KunenaAdminController' . ucfirst ( $view );
		} else {
			$class = 'KunenaController' . ucfirst ( $view );
		}
		if (class_exists ( $class )) {
			$instance = new $class ();
			$instance->starttime = $starttime;
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
		$app = JFactory::getApplication();
		if ($app->isAdmin()) {
			// Version warning
			require_once KPATH_ADMIN . '/install/version.php';
			$version = new KunenaVersion();
			$version_warning = $version->getVersionWarning('COM_KUNENA_VERSION_INSTALLED');
			if (! empty ( $version_warning )) {
				$app->enqueueMessage ( $version_warning, 'notice' );
			}
		} else {
			// Initialize profile integration
			$integration = KunenaFactory::getProfile();
			$integration->open();

			/*if (!$app->getMenu ()->getActive ()) {
				// FIXME:
				JError::raiseError ( 500, JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			}*/
		}

		// Get the document object.
		$document = JFactory::getDocument ();

		// Set the default view name and format from the Request.
		$vName = JRequest::getWord ( 'view', 'none' );
		$lName = JRequest::getWord ( 'layout', 'default' );
		$vFormat = $document->getType ();

		$view = $this->getView ( $vName, $vFormat, '', array ('base_path' => $this->_basePath ) );
		if ($view) {
			if ($app->isSite() && $vFormat=='html') {
				$view->template = KunenaFactory::getTemplate();
				$common = $this->getView ( 'common', $vFormat, '', array ('base_path' => $this->_basePath ) );
				$common->starttime = $this->starttime;
				$common->setModel ( $this->getModel ( 'common' ), true );
				$view->common = $common;
				$templatepath = KPATH_SITE."/{$view->template->getPath()}/html/";
				$view->addTemplatePath($templatepath.$vName);
				$view->common->addTemplatePath($templatepath.'common');
			}

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
			if ($app->isAdmin()) {
				$view->display ();
			} elseif ($vFormat=='html') {
				$view->displayAll ();
			} else {
				$view->displayLayout ();
			}
		}

		if ($app->isSite()) {
			// Close profile integration
			$integration = KunenaFactory::getProfile();
			$integration->close();
		}
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * If escaping mechanism is one of htmlspecialchars or htmlentities.
	 *
	 * @param  mixed $var The output to escape.
	 * @return mixed The escaped value.
	 */
	function escape($var) {
		if (in_array ( $this->_escape, array ('htmlspecialchars', 'htmlentities' ) )) {
			return call_user_func ( $this->_escape, $var, ENT_COMPAT, 'UTF-8' );
		}
		return call_user_func ( $this->_escape, $var );
	}

	/**
	 * Sets the _escape() callback.
	 *
	 * @param mixed $spec The callback for _escape() to use.
	 */
	function setEscape($spec) {
		$this->_escape = $spec;
	}

	function getRedirect() {
		return $this->_redirect;
	}
	function getMessage() {
		return $this->_message;
	}
	function getMessageType() {
		return $this->_messageType;
	}

	protected function redirectBack() {
		$httpReferer = JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' );
		JFactory::getApplication ()->redirect ( $httpReferer );
	}

}
