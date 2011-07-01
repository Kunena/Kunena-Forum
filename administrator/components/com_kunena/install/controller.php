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

require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

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
		// Start by loading English strings and override them by current locale
		$lang->load('com_kunena.install',JPATH_ADMINISTRATOR, 'en-GB');
		$lang->load('com_kunena.install',JPATH_ADMINISTRATOR);

		parent::__construct ();
		require_once(KPATH_ADMIN.'/install/model.php');
		$this->model = $this->getModel ( 'Install' );
		$this->step = $this->model->getStep ();
		$this->steps = $this->model->getSteps ();
	}

	function prepare() {
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$this->model->setStep ( 0 );

		// Install English and default language
		$tag = JFactory::getLanguage()->getTag();
		$this->model->installLanguage('en-GB');
		if ($tag != 'en-GB') $this->model->installLanguage($tag);

		$this->setRedirect('index.php?option=com_kunena&view=install');
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
	}

	public function run() {
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );

		set_exception_handler('kunenaInstallerExceptionHandler');
		//set_error_handler('kunenaInstallerErrorHandler');

		$session = JFactory::getSession();

		// Check requirements
		$this->model->checkTimeout ();
		$reqs = $this->model->getRequirements ();
		$action = $this->model->getAction();
		if (! empty ( $reqs->fail ) || !$action) {
			// If requirements are not met, do not install
			$this->model->setAction ( null );
			$this->model->setStep ( 0 );
			$this->setRedirect ( 'index.php?option=com_kunena&view=install' );
			return;
		}
		if (!isset($this->steps[$this->step+1])) {
			// Installation complete: reset and exit installer
			$this->model->setAction ( null );
			$this->model->setStep ( 0 );
			$this->setRedirect ( 'index.php?option=com_kunena' );
			return;
		}

		if ($this->step == 0) {
			// Reset enqueued messages before starting
			$session->set('kunena.queue', null);
			$session->set('kunena.newqueue', null);
			$this->model->setStep ( ++ $this->step );
		}
		do {
			$this->runStep ();
			$error = $this->model->getError ();
			$this->step = $this->model->getStep ();
			$stop = ($this->model->checkTimeout () || !isset($this->steps[$this->step+1]));
		} while ( ! $stop && ! $error );

		// Store queued messages so that they won't get lost
		$session->set('kunena.queue', array_merge( (array) $session->get('kunena.queue'), (array) $session->get('kunena.newqueue') ));
		$newqueue = array();
		$app = JFactory::getApplication ();
		foreach ($app->getMessageQueue() as $item) {
			if (!empty($item['message'])) $newqueue[] = $item;
		}
		$session->set('kunena.newqueue', $newqueue);

		if ( isset($this->steps[$this->step+1]) && ! $error ) {
			$this->setRedirect ( 'index.php?option=com_kunena&view=install&go=next' );
		} else {
			$this->setRedirect ( 'index.php?option=com_kunena&view=install' );
		}
	}

	public function restart() {
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$this->model->setStep ( 0 );
		$this->run();
	}
	function install() {
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$this->model->setAction ( 'install' );
		$this->run();
	}
	function upgrade() {
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$this->model->setAction ( 'upgrade' );
		$this->run();
	}
	function downgrade() {
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$this->model->setAction ( 'downgrade' );
		$this->run();
	}
	function up_build() {
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$this->model->setAction ( 'up_build' );
		$this->run();
	}
	function down_build() {
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$this->model->setAction ( 'down_build' );
		$this->run();
	}
	function reinstall() {
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$this->model->setAction ( 'reinstall' );
		$this->run();
	}
	function migrate() {
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$this->model->setAction ( 'migrate' );
		$this->run();
	}
	function uninstall() {
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$this->model->setAction ( 'uninstall' );
		$this->model->deleteTables('kunena_');
		$this->model->deleteMenu();
		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::_('COM_KUNENA_INSTALL_REMOVED'));
		if (!KunenaForum::isSvn()) {
			jimport('joomla.filesystem.folder');
			JFolder::delete(KPATH_MEDIA);
			jimport('joomla.installer.installer');
			$installer = new JInstaller ( );
			jimport('joomla.application.component.helper');
			$component = JComponentHelper::getComponent('com_kunena');
			$installer->uninstall ( 'component', $component->id );
			$this->setRedirect ( 'index.php?option=com_installer' );
		} else {
			$this->setRedirect ( 'index.php?option=com_kunena&view=install' );
		}
	}
	function restore() {
		$this->model->setAction ( 'restore' );
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$this->uninstall();
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