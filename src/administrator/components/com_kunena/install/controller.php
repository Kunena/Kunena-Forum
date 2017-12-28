<?php
/**
 * Kunena Component
 *
 * @package    Kunena.Installer
 *
 * @copyright  (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * The Kunena Installer Controller
 *
 * @since  1.6
 */
class KunenaControllerInstall extends JControllerLegacy
{
	protected $step = null;

	protected $steps = null;

	protected $model = null;

	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		require_once __DIR__ . '/model.php';
		$this->model = $this->getModel('Install');
		$this->step  = $this->model->getStep();
		$this->steps = $this->model->getSteps();
	}

	/**
	 * @param   bool $cachable
	 * @param   bool $urlparams
	 *
	 * @return JControllerLegacy|void
	 *
	 * @throws Exception
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once __DIR__ . '/view.php';
		$view = $this->getView('install', 'html');

		if ($view)
		{
			$view->addTemplatePath(__DIR__ . '/tmpl');
			$view->setModel($this->model, true);
			$view->setLayout(JFactory::getApplication()->input->getWord('layout', 'default'));
			$view->document = JFactory::getDocument();
			$view->display();

			// Display Toolbar. View must have setToolBar method
			if (method_exists($view, 'setToolBar'))
			{
				$view->setToolBar();
			}
		}
	}

	/**
	 * @throws Exception
	 */
	public function run()
	{
		if (!JSession::checkToken('post'))
		{
			echo json_encode(array('success' => false, 'html' => 'Invalid token!'));

			return;
		}

		set_exception_handler(array(__CLASS__, 'exceptionHandler'));
		set_error_handler(array(__CLASS__, 'errorHandler'));

		$session = JFactory::getSession();

		$this->model->checkTimeout();
		$action = $this->model->getAction();

		if (!$action)
		{
			$this->model->setAction(null);
			$this->model->setStep(0);
			echo json_encode(array('success' => false, 'html' => 'No action defined!'));

			return;
		}

		if (!isset($this->steps[$this->step + 1]))
		{
			// Installation complete: reset and exit installer
			$this->model->setAction(null);
			$this->model->setStep(0);
			echo json_encode(array('success' => true, 'status' => '100%', 'html' => JText::_('COM_KUNENA_CONTROLLER_INSTALL_INSTALLATION_COMPLETE')));

			return;
		}

		if ($this->step == 0)
		{
			// Reset enqueue messages before starting
			$session->set('kunena.reload', 1);
			$session->set('kunena.queue', null);
			$session->set('kunena.newqueue', null);
			$this->model->setStep(++$this->step);
		}

		do
		{
			$this->runStep();
			$error      = $this->model->getInstallError();
			$this->step = $this->model->getStep();
			$stop       = ($this->model->checkTimeout() || !isset($this->steps[$this->step + 1]));
		}
		while (!$stop && !$error);

		// Store queued messages so that they won't get lost
		$session->set('kunena.queue', array_merge((array) $session->get('kunena.queue'), (array) $session->get('kunena.newqueue')));
		$newqueue = array();
		$app      = JFactory::getApplication();

		foreach ($app->getMessageQueue() as $item)
		{
			if (!empty($item['message']))
			{
				$newqueue[] = $item;
			}
		}

		$session->set('kunena.newqueue', $newqueue);

		$this->status = $this->model->getStatus();
		ob_start();
		include __DIR__ . '/tmpl/install.php';
		$log = ob_get_contents();
		ob_end_clean();

		JFactory::getDocument()->setMimeEncoding('application/json');
		JFactory::getApplication()->setHeader('Content-Disposition', 'attachment;filename="kunena-install.json"');

		$percent = intval(99 * $this->step / count($this->steps));

		if ($error)
		{
			echo json_encode(array('success' => false, 'status' => "{$percent}%", 'error' => $error, 'log' => $log));
		}
		elseif (isset($this->steps[$this->step + 1]))
		{
			$current = end($this->status);
			echo json_encode(array('success' => true, 'status' => "{$percent}%", 'current' => $current['task'], 'log' => $log));
		}
		else
		{
			echo json_encode(array('success' => true, 'status' => '100%', 'current' => JText::_('COM_KUNENA_CONTROLLER_INSTALL_INSTALLATION_COMPLETE'), 'log' => $log));
		}

		JFactory::getApplication()->close();
	}

	/**
	 * @throws Exception
	 */
	function uninstall()
	{
		if (!JSession::checkToken('get'))
		{
			$this->setRedirect('index.php?option=com_kunena');

			return;
		}

		$this->model->setAction('uninstall');
		$this->model->deleteTables('kunena_');
		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::_('COM_KUNENA_INSTALL_REMOVED'));

		if (class_exists('KunenaForum') && !KunenaForum::isDev())
		{
			jimport('joomla.application.component.helper');
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');

			$installer = new JInstaller;
			$component = JComponentHelper::getComponent('com_kunena');
			$installer->uninstall('component', $component->id);

			if (JFolder::exists(KPATH_MEDIA))
			{
				JFolder::delete(KPATH_MEDIA);
			}

			if (JFolder::exists(JPATH_ROOT . '/plugins/kunena'))
			{
				JFolder::delete(JPATH_ROOT . '/plugins/kunena');
			}

			if (JFile::exists(JPATH_ADMINISTRATOR . '/manifests/packages/pkg_kunena.xml'))
			{
				JFile::delete(JPATH_ADMINISTRATOR . '/manifests/packages/pkg_kunena.xml');
			}

			$this->setRedirect('index.php?option=com_installer');
		}
		else
		{
			$this->setRedirect('index.php?option=com_kunena&view=install');
		}
	}

	/**
	 * @return mixed|null
	 *
	 */
	function runStep()
	{
		if (empty($this->steps[$this->step]['step']))
		{
			return null;
		}

		return call_user_func(array($this->model, "step" . $this->steps[$this->step]['step']));
	}

	/**
	 * @param $type
	 * @param $errstr
	 */
	static public function error($type, $errstr)
	{
		$model = JModelLegacy::getInstance('Install', 'KunenaModel');
		$model->addStatus($type, false, $errstr);
		echo json_encode(array('success' => false, 'html' => $errstr));
	}

	/**
	 * @param $exception
	 *
	 * @return boolean
	 *
	 */
	static public function exceptionHandler($exception)
	{
		self::error('', 'Uncaught Exception: ' . $exception->getMessage());

		return true;
	}

	/**
	 * @param $errno
	 * @param $errstr
	 * @param $errfile
	 * @param $errline
	 *
	 * @return boolean
	 *
	 */
	static public function errorHandler($errno, $errstr, $errfile, $errline)
	{
		// Self::error('', "Fatal Error: $errstr in $errfile on line $errline");
		switch ($errno)
		{
			case E_ERROR:
			case E_USER_ERROR:
				self::error('', "Fatal Error: $errstr in $errfile on line $errline");

				return true;
		}

		return false;
	}
}
