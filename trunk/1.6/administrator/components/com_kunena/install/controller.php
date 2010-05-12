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
		parent::__construct ();
		require_once(KPATH_ADMIN.'/install/model.php');
		$this->model = $this->getModel ( 'Install' );
		$this->step = $this->model->getStep ();
		$this->steps = $this->model->getSteps ();

		$lang = JFactory::getLanguage();
		$lang->load('com_kunena.install',KPATH_ADMIN);
		$lang->load('com_kunena.install');
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
			// Push the model into the view (as default).
			$view->setModel($this->model, true);

			// Set the view layout.
			$view->setLayout(JRequest::getWord('layout', 'default'));

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

	public function install() {
		// Check requirements
		$reqs = $this->model->getRequirements ();
		if (! empty ( $reqs->fail )) {
			// If requirements are not met, do not install
			$this->model->setStep ( 0 );
			$this->setRedirect ( 'index.php?option=com_kunena&view=install' );
			return;
		}
		if (! $this->step)
			$this->model->setStep ( ++ $this->step );
		if ($this->step >= count ( $this->steps ) - 1) {
			// Installation complete: reset and exit installer
			$this->model->setStep ( 0 );
			$this->setRedirect ( 'index.php?option=com_kunena' );
			return;
		}

		do {
			$this->next ();
			$error = $this->model->getError ();
			$stop = ($this->checkTimeout () || $this->step <= 2 || ($this->step >= count ( $this->steps ) - 1));
			$this->setRedirect ( 'index.php?option=com_kunena&view=install' );
		} while ( ! $stop && ! $error );
	}

	function next() {
		if (empty ( $this->steps [$this->step] ['step'] ))
			return;
		return call_user_func ( array ($this, "step" . $this->steps [$this->step] ['step'] ) );
	}

	function stepPrepare() {
		$this->model->beginInstall ();
		if (! $this->model->getError ())
			$this->model->setStep ( ++ $this->step );
	}

	function stepBackend() {
		$path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' . DS . 'archive';
		$file = 'admin.zip';
		if (file_exists ( $path . DS . $file ))
			$this->model->extract ( $path, $file, JPATH_ROOT );
		if (! $this->model->getError ())
			$this->model->setStep ( ++ $this->step );
	}

	function stepFrontend() {
		$path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' . DS . 'archive';
		$file = 'site.zip';
		if (file_exists ( $path . DS . $file ))
			$this->model->extract ( $path, $file, JPATH_ROOT );
		if (! $this->model->getError ())
			$this->model->setStep ( ++ $this->step );
	}

	function stepDatabase() {
		require_once(KPATH_ADMIN.'/install/kunena.install.php');
		com_install();
		$this->model->addStatus ( JText::_('COM_KUNENA_INSTALL_STEP_DATABASE'), true, '' );
		if (! $this->model->getError ())
			$this->model->setStep ( ++ $this->step );
	}

	function stepFinish() {
		$this->model->installFinish ();
		if (! $this->model->getError ()) {
			$this->model->setStep ( ++ $this->step );
		}
	}

	function checkTimeout() {
		static $start = null;

		list ( $usec, $sec ) = explode ( ' ', microtime () );
		$time = (( float ) $usec + ( float ) $sec);
		if (empty ( $start ))
			$start = $time;
		if ($time - $start < 2)
			return false;
		return true;
	}
}