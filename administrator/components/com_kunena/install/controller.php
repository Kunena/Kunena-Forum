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

require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

/**
 * The Kunena Installer Controller
 *
 * @since		1.6
 */
class KunenaControllerInstall extends JControllerLegacy {
	protected $step = null;
	protected $steps = null;
	protected $model = null;

	public function __construct() {
		parent::__construct ();
		require_once(KPATH_ADMIN.'/install/model.php');
		$this->model = $this->getModel ( 'Install' );
		$this->step = $this->model->getStep ();
		$this->steps = $this->model->getSteps ();
	}

	// Run from administrator installer
	function prepare() {
		if (!JSession::checkToken( 'get' )) {
			$this->setRedirect('index.php?option=com_kunena');
			return;
		}

		$start = JRequest::getBool('start', false);

		// Workaround situation where KunenaForum class doesn't exist (api.php was cached)
		if (!class_exists('KunenaForum')) {
			// TODO: add version check
			$app = JFactory::getApplication();
			$try = $app->getUserState('kunena-prepare', 0) + 1;
			clearstatcache();
			if (function_exists('apc_clear_cache')) apc_clear_cache('system');
			sleep(1);
			$app->setUserState('kunena-prepare', $try);
			$start = $start? '&start=1' : '';
			$this->setRedirect('index.php?option=com_kunena&view=install&task=prepare&try='.$try.$start.'&'.JSession::getFormToken().'=1');
			$this->redirect();
		}

		$this->model->install ();

		if ($start) {
			// Make sure that the code is identical to the installer (we can improve it later on)
			$versions = $this->model->getDetectVersions();
			$version = reset($versions);
			if (!empty($version->state) || ($version->version == KunenaForum::version() && $version->versiondate == KunenaForum::versionDate())) {
				unset($version);
			}
		}
		if (isset($version)) {
			$this->setRedirect($version->link);
		} else {
			$this->setRedirect('index.php?option=com_kunena&view=install');
		}
	}

	public function display($cachable = false, $urlparams = false) {
		require_once(KPATH_ADMIN.'/install/view.php');
		$view = $this->getView('install', 'html');
		if ($view)
		{
			$view->addTemplatePath(KPATH_ADMIN.'/install/tmpl');
			$view->setModel($this->model, true);
			$view->setLayout(JRequest::getWord('layout', 'default'));
			$view->document = JFactory::getDocument();
			$view->display();

			// Display Toolbar. View must have setToolBar method
			if( method_exists( $view , 'setToolBar') )
			{
				$view->setToolBar();
			}
		}
	}

	public function run() {
		if (!JSession::checkToken( 'get' )) {
			$this->setRedirect('index.php?option=com_kunena');
			return;
		}

		set_exception_handler('kunenaInstallerExceptionHandler');
		//set_error_handler('kunenaInstallerErrorHandler');

		$session = JFactory::getSession();

		$this->model->checkTimeout ();
		$action = $this->model->getAction();
		if (!$action) {
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
			$session->set('kunena.reload', 1);
			$session->set('kunena.queue', null);
			$session->set('kunena.newqueue', null);
			$this->model->setStep ( ++ $this->step );
		}
		do {
			$this->runStep ();
			$error = $this->model->getInstallError ();
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
			$cnt = $session->get('kunena.reload', 1);
			$this->setRedirect ( 'index.php?option=com_kunena&view=install&go=next&n='.$cnt );
			$session->set('kunena.reload', $cnt+1);
		} else {
			$this->setRedirect ( 'index.php?option=com_kunena&view=install' );
		}
	}

	public function restart() {
		if (!JSession::checkToken( 'get' )) {
			$this->setRedirect('index.php?option=com_kunena');
			return;
		}
		$this->model->setStep ( 0 );
		$this->run();
	}
	function install() {
		if (!JSession::checkToken( 'get' )) {
			$this->setRedirect('index.php?option=com_kunena');
			return;
		}
		$this->model->setAction ( 'install' );
		$this->run();
	}
	function upgrade() {
		if (!JSession::checkToken( 'get' )) {
			$this->setRedirect('index.php?option=com_kunena');
			return;
		}
		$this->model->setAction ( 'upgrade' );
		$this->run();
	}
	function downgrade() {
		if (!JSession::checkToken( 'get' )) {
			$this->setRedirect('index.php?option=com_kunena');
			return;
		}
		$this->model->setAction ( 'downgrade' );
		$this->run();
	}
	function reinstall() {
		if (!JSession::checkToken( 'get' )) {
			$this->setRedirect('index.php?option=com_kunena');
			return;
		}
		$this->model->setAction ( 'reinstall' );
		$this->run();
	}
	function migrate() {
		if (!JSession::checkToken( 'get' )) {
			$this->setRedirect('index.php?option=com_kunena');
			return;
		}
		$this->model->setAction ( 'migrate' );
		$this->run();
	}
	function uninstall() {
		if (!JSession::checkToken( 'get' )) {
			$this->setRedirect('index.php?option=com_kunena');
			return;
		}
		$this->model->setAction ( 'uninstall' );
		$this->model->deleteTables('kunena_');
		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::_('COM_KUNENA_INSTALL_REMOVED'));
		if (!KunenaForum::isDev()) {
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
		if (!JSession::checkToken( 'get' )) {
			$this->setRedirect('index.php?option=com_kunena');
			return;
		}
		$this->model->setAction ( 'restore' );
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
	$model = JModelLegacy::getInstance('Install', 'KunenaModel');
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