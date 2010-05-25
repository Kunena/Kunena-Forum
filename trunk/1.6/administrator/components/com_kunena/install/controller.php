<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined ( '_JEXEC' ) or die ();

jimport('joomla.application.component.controller');

/**
 * The Kunena Installer Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaControllerInstall extends JController {
	protected $step = null;
	protected $steps = null;
	protected $model = null;

	public function __construct() {
		$lang = JFactory::getLanguage();
		$lang->load('com_kunena.install');
		$lang->load('com_kunena.install',KPATH_ADMIN);

		parent::__construct ();
		require_once(KPATH_ADMIN.'/install/model.php');
		$this->model = $this->getModel ( 'Install' );
		$this->step = $this->model->getStep ();
		$this->steps = $this->model->getSteps ();
	}

	public function display()
	{
		// Get the document object.
		$document = JFactory::getDocument();

		require_once(KPATH_ADMIN.'/install/view.php');
		$view = $this->getView('install', 'html');
		if ($view)
		{
			$view->addTemplatePath(KPATH_ADMIN.'/install/tmpl');
			$view->setModel($this->model, true);
			$view->setLayout(JRequest::getWord('layout', 'default'));
			$view->assignRef('document', $document);
			$view->display();

			// Display Toolbar. View must have setToolBar method
			if( method_exists( $view , 'setToolBar') )
			{
				$view->setToolBar();
			}
		}
			$app = JFactory::getApplication ();
	}

	public function restart() {
		$this->model->setStep ( 0 );
		$this->install();
	}

	public function install() {
		set_exception_handler('kunenaInstallerExceptionHandler');
		//set_error_handler('kunenaInstallerErrorHandler');

		// Check requirements
		$this->checkTimeout ();
		$reqs = $this->model->getRequirements ();
		if (! empty ( $reqs->fail )) {
			// If requirements are not met, do not install
			$this->model->setStep ( 0 );
			$this->setRedirect ( 'index.php?option=com_kunena&view=install' );
			return;
		}
		if (!isset($this->steps[$this->step+1])) {
			// Installation complete: reset and exit installer
			$this->model->setStep ( 0 );
			$this->setRedirect ( 'index.php?option=com_kunena' );
			return;
		}

		do {
			if ($this->step == 0) {
				$this->model->setStep ( ++ $this->step );
				break;
			}
			$this->runStep ();
			$error = $this->model->getError ();
			$this->step = $this->model->getStep ();
			$stop = ($this->checkTimeout () || !isset($this->steps[$this->step+1]));
		} while ( ! $stop && ! $error );
		if ( isset($this->steps[$this->step+1]) && ! $error ) {
			$this->setRedirect ( 'index.php?option=com_kunena&view=install&go=next' );
		} else {
			$this->setRedirect ( 'index.php?option=com_kunena&view=install' );
		}
	}

	function abort() {
		$app = JFactory::getApplication();
		$app->setUserState('com_kunena.install.step', 0);
		$this->setRedirect ( 'index.php?option=com_kunena' );
	}

	function runStep() {
		if (empty ( $this->steps [$this->step] ['step'] ))
			return;
		return call_user_func ( array ($this->model, "step" . $this->steps [$this->step] ['step'] ) );
	}

	function checkTimeout() {
		static $start = null;
		$time = microtime (true);
		if ($start === null) {
			$start = $time;
		}
		if ($time - $start < 1)
			return false;
		return true;
	}
}

function kunenaInstallerError($type, $errstr) {
	$model = JModel::getInstance('Install', 'KunenaModel');
	$model->addStatus($type, false, $errstr);
	$app = JFactory::getApplication();
	$app->redirect ( 'index.php?option=com_kunena&view=install' );
}

function kunenaInstallerExceptionHandler($exception) {
	kunenaInstallerError('', 'Uncaught Exception: '.$exception->getMessage());
	return true;
}

function kunenaInstallerErrorHandler($errno, $errstr, $errfile, $errline) {
	kunenaInstallerError('', "Fatal Error: $errstr in $errfile on line $errline");
	switch ($errno) {
		case E_ERROR:
		case E_USER_ERROR:
			kunenaInstallerError('', "Fatal Error: $errstr in $errfile on line $errline");
			return true;
		}
	return false;
}