<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

/**
 * The Kunena Installer Controller
 *
 * @since  1.6
 */
class KunenaControllerInstall extends \Joomla\CMS\MVC\Controller\BaseController
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $step = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	protected $steps = null;

	/**
	 * @var bool|\Joomla\CMS\MVC\Model\BaseDatabaseModel|null
	 * @since Kunena
	 */
	protected $model = null;

	/**
	 * @since Kunena
	 */
	public function __construct()
	{
		// Disable error_reporting improves more successfully install.
		error_reporting(0);

		parent::__construct();
		require_once __DIR__ . '/model.php';
		$this->model = $this->getModel('Install');
		$this->step  = $this->model->getStep();
		$this->steps = $this->model->getSteps();
	}

	/**
	 * @param   bool $cachable  cachable
	 * @param   bool $urlparams urlparams
	 *
	 * @return \Joomla\CMS\MVC\Controller\BaseController|void
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once __DIR__ . '/view.php';
		$view = $this->getView('install', 'html');

		if ($view)
		{
			$view->addTemplatePath(__DIR__ . '/tmpl');
			$view->setModel($this->model, true);
			$view->setLayout(Factory::getApplication()->input->getWord('layout', 'default'));
			$view->document = Factory::getDocument();
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
	 * @since Kunena
	 */
	public function run()
	{
		if (!Session::checkToken('post'))
		{
			echo json_encode(array('success' => false, 'html' => 'Invalid token!'));

			return;
		}

		set_exception_handler(array(__CLASS__, 'exceptionHandler'));
		set_error_handler(array(__CLASS__, 'errorHandler'));

		$session = Factory::getSession();

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
			echo json_encode(array('success' => true, 'status' => '100%', 'html' => Text::_('COM_KUNENA_CONTROLLER_INSTALL_INSTALLATION_COMPLETE')));

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
		$app      = Factory::getApplication();

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

		Factory::getDocument()->setMimeEncoding('application/json');
		Factory::getApplication()->setHeader('Content-Disposition', 'attachment;filename="kunena-install.json"');
		Factory::getApplication()->sendHeaders();

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
			echo json_encode(array('success' => true, 'status' => '100%', 'current' => Text::_('COM_KUNENA_CONTROLLER_INSTALL_INSTALLATION_COMPLETE'), 'log' => $log));
		}

		Factory::getApplication()->close();
	}

	/**
	 * @throws Exception
	 * @since Kunena
	 */
	public function uninstall()
	{
		if (!Session::checkToken('get'))
		{
			$this->setRedirect('index.php?option=com_kunena');

			return;
		}

		$this->model->setAction('uninstall');
		$this->model->deleteTables('kunena_');
		$app = Factory::getApplication();
		$app->enqueueMessage(Text::_('COM_KUNENA_INSTALL_REMOVED'));

		if (class_exists('KunenaForum') && !KunenaForum::isDev())
		{
			jimport('joomla.application.component.helper');
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');

			$installer = new \Joomla\CMS\Installer\Installer;
			$component = \Joomla\CMS\Component\ComponentHelper::getComponent('com_kunena');
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
	 * @since Kunena
	 */
	public function runStep()
	{
		if (empty($this->steps[$this->step]['step']))
		{
			return;
		}

		return call_user_func(array($this->model, "step" . $this->steps[$this->step]['step']));
	}

	/**
	 * @param $type
	 * @param $errstr
	 *
	 * @since Kunena
	 */
	public static function error($type, $errstr)
	{
		$model = \Joomla\CMS\MVC\Model\BaseDatabaseModel::getInstance('Install', 'KunenaModel');
		$model->addStatus($type, false, $errstr);
		echo json_encode(array('success' => false, 'html' => $errstr));
	}

	/**
	 * @param $exception
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public static function exceptionHandler($exception)
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
	 * @since Kunena
	 */
	public static function errorHandler($errno, $errstr, $errfile, $errline)
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
