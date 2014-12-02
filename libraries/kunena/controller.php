<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.helper' );

/**
 * Class KunenaController
 */
class KunenaController extends JControllerLegacy {
	public $app = null;
	public $me = null;
	public $config = null;

	public function __construct($config = array()) {
		parent::__construct ($config);
		$this->profiler = KunenaProfiler::instance('Kunena');
		$this->app = JFactory::getApplication();
		$this->config = KunenaFactory::getConfig();
		$this->me = KunenaUserHelper::getMyself();

		// Save user profile if it didn't exist.
		if ($this->me->userid && !$this->me->exists()) {
			$this->me->save();
		}
	}

	/**
	 * Method to get the appropriate controller.
	 *
	 * @param	string	$prefix
	 * @param	mixed	$config
	 * @return	KunenaController
	 */
	public static function getInstance($prefix = 'Kunena', $config = array()) {
		static $instance = null;

		if (!$prefix) $prefix = 'Kunena';
		if (! empty ( $instance ) && !isset($instance->home)) {
			return $instance;
		}

		$input = JFactory::getApplication()->input;

		$app = JFactory::getApplication();
		$command  = $input->get('task', 'display');

		// Check for a controller.task command.
		if (strpos($command, '.') !== false) {
			// Explode the controller.task command.
			list ($view, $task) = explode('.', $command);

			// Reset the task without the controller context.
			$input->set('task', $task);
		} else {
			// Base controller.
			$view = strtolower ( JRequest::getWord ( 'view', $app->isAdmin() ? 'cpanel' : 'home' ) );
		}

		$path = JPATH_COMPONENT . "/controllers/{$view}.php";

		// If the controller file path exists, include it ... else die with a 500 error.
		if (file_exists ( $path )) {
			require_once $path;
		} else {
			JError::raiseError ( 404, JText::sprintf ( 'COM_KUNENA_INVALID_CONTROLLER', ucfirst ( $view ) ) );
		}

		// Set the name for the controller and instantiate it.
		if ($app->isAdmin()) {
			$class = $prefix . 'AdminController' . ucfirst ( $view );
			KunenaFactory::loadLanguage('com_kunena.controllers', 'admin');
			KunenaFactory::loadLanguage('com_kunena.models', 'admin');
			KunenaFactory::loadLanguage('com_kunena.sys', 'admin');
			KunenaFactory::loadLanguage('com_kunena', 'site');

		} else {
			$class = $prefix . 'Controller' . ucfirst ( $view );
			KunenaFactory::loadLanguage('com_kunena.controllers');
			KunenaFactory::loadLanguage('com_kunena.models');
			KunenaFactory::loadLanguage('com_kunena.sys', 'admin');

		}
		if (class_exists ( $class )) {
			$instance = new $class ();
		} else {
			JError::raiseError ( 404, JText::sprintf ( 'COM_KUNENA_INVALID_CONTROLLER_CLASS', $class ) );
		}

		return $instance;
	}

	/**
	 * Execute task.
	 *
	 * @param string $task
	 * @return mixed
	 * @throws Exception
	 */
	public function execute($task)
	{
		$dot = strpos($task, '.');
		$this->task = $dot ? substr($task, $dot + 1) : $task;

		$task = strtolower($this->task);
		if (isset($this->taskMap[$this->task]))
		{
			$doTask = $this->taskMap[$this->task];
		}
		elseif (isset($this->taskMap['__default']))
		{
			$doTask = $this->taskMap['__default'];
		}
		else
		{
			throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_TASK_NOT_FOUND', $task), 404);
		}

		// Record the actual task being fired
		$this->doTask = $doTask;

		return $this->$doTask();
	}

	/**
	 * Method to display a view.
	 *
	 * @param   boolean    $cachable   If true, the view output will be cached
	 * @param   array|bool $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  A JControllerLegacy object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false) {
		KUNENA_PROFILER ? $this->profiler->mark('beforeDisplay') : null;
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		// Get the document object.
		$document = JFactory::getDocument ();

		// Set the default view name and format from the Request.
		$vName = JRequest::getWord ( 'view', $this->app->isAdmin() ? 'cpanel' : 'home' );
		$lName = JRequest::getWord ( 'layout', 'default' );
		$vFormat = $document->getType ();

		if ($this->app->isAdmin()) {
			// Load admin language files
			KunenaFactory::loadLanguage('com_kunena.install', 'admin');
			KunenaFactory::loadLanguage('com_kunena.views', 'admin');
			// Load last to get deprecated language files to work
			KunenaFactory::loadLanguage('com_kunena', 'admin');

			// Version warning
			require_once KPATH_ADMIN . '/install/version.php';
			$version = new KunenaVersion();
			$version_warning = $version->getVersionWarning();
			if (! empty ( $version_warning )) {
				$this->app->enqueueMessage ( $version_warning, 'notice' );
			}
		} else {
			// Load site language files
			KunenaFactory::loadLanguage('com_kunena.views');
			KunenaFactory::loadLanguage('com_kunena.templates');
			// Load last to get deprecated language files to work
			KunenaFactory::loadLanguage('com_kunena');

			$menu = $this->app->getMenu ();
			$active = $menu->getActive ();

			// Check if menu item was correctly routed
			$routed = $menu->getItem ( KunenaRoute::getItemID() );

			if ($vFormat=='html' && !empty($routed->id) && (empty($active->id) || $active->id != $routed->id)) {
				// Routing has been changed, redirect
				// FIXME: check possible redirect loops!
				$route = KunenaRoute::_(null, false);
				$activeId = !empty($active->id) ? $active->id : 0;
				JLog::add("Redirect from ".JUri::getInstance()->toString(array('path', 'query'))." ({$activeId}) to {$route} ($routed->id)", JLog::DEBUG, 'kunena');
				$this->app->redirect ($route);
			}

			// Joomla 2.5+ multi-language support
			/* // FIXME:
			if (isset($active->language) && $active->language != '*') {
				$language = JFactory::getDocument()->getLanguage();
				if (strtolower($active->language) != strtolower($language)) {
					$route = KunenaRoute::_(null, false);
					JLog::add("Language redirect from ".JUri::getInstance()->toString(array('path', 'query'))." to {$route}", JLog::DEBUG, 'kunena');
					$this->redirect ($route);
				}
			}
			*/
		}

		$view = $this->getView ( $vName, $vFormat );
		if ($view) {
			if ($this->app->isSite() && $vFormat=='html') {
				$common = $this->getView ( 'common', $vFormat );
				$model = $this->getModel ( 'common' );
				$common->setModel ( $model, true );
				$view->ktemplate = $common->ktemplate = KunenaFactory::getTemplate();
				$view->common = $common;
			}

			// Set the view layout.
			$view->setLayout ( $lName );

			// Get the appropriate model for the view.
			$model = $this->getModel ( $vName );

			// Push the model into the view (as default).
			$view->setModel ( $model, true );

			// Push document object into the view.
			$view->document = $document;

			// Render the view.
			if ($vFormat=='html') {
				JPluginHelper::importPlugin('kunena');
				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger('onKunenaDisplay', array('start', $view));
				$view->displayAll ();
				$dispatcher->trigger('onKunenaDisplay', array('end', $view));
			} else {
				$view->displayLayout ();
			}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return $this;
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param  string $var The output to escape.
	 *
	 * @return string The escaped value.
	 */
	public function escape($var) {
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * @return string
	 */
	public function getRedirect() {
		return $this->redirect;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function getMessageType() {
		return $this->messageType;
	}

	/**
	 * @param string $fragment
	 */
	protected function redirectBack($fragment = '') {
		$httpReferer = JRequest::getVar ( 'HTTP_REFERER', JUri::base ( true ), 'server' );
		JFactory::getApplication ()->redirect ( $httpReferer.($fragment ? '#'.$fragment : '') );
	}

}
