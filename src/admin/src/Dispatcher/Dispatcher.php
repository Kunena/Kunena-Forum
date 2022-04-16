<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Dispatcher
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

/**
 * ComponentDispatcher class for com_kunena
 *
 * @since  6.0
 */
class Dispatcher extends ComponentDispatcher
{
	protected $option = 'com_kunena';

	protected $defaultController = 'cpanel';

	protected function onBeforeDispatch()
	{
		// Load the languages
		$this->loadLanguage();

		// Apply the view and controller from the request, falling back to the default view/controller if necessary
		$this->applyViewAndController();

		// Access control
		$this->checkAccess();
	}

	protected function loadLanguage()
	{
		KunenaFactory::loadLanguage('com_kunena', 'admin');
		KunenaFactory::loadLanguage('com_kunena.views', 'admin');
		KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');
		KunenaFactory::loadLanguage('com_kunena.sys', 'admin');
		KunenaFactory::loadLanguage('com_kunena.install', 'admin');
		KunenaFactory::loadLanguage('com_kunena.models', 'admin');
		KunenaFactory::loadLanguage('com_kunena.controllers', 'admin');
		KunenaFactory::loadLanguage('com_plugins', 'admin');
		KunenaFactory::loadLanguage('com_kunena', 'site');
	}

	/**
	 * @since K6.0
	 */
	private function applyViewAndController(): void
	{
		// Handle a custom default controller name
		$view       = $this->input->getCmd('view', $this->defaultController);
		$controller = $this->input->getCmd('controller', $view);
		$task       = $this->input->getCmd('task', 'cpanel');

		// Check for a controller.task command.
		if (strpos($task, '.') !== false)
		{
			// Explode the controller.task command.
			[$controller, $task] = explode('.', $task);
		}

		$this->input->set('view', $controller);
		$this->input->set('controller', $controller);
		$this->input->set('task', $task);
	}

	/**
	 * Kunena have to check for extension permission
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function checkAccess()
	{
		if ($this->app->isClient('administrator') && !$this->app->getIdentity()->authorise('core.manage', 'com_kunena'))
		{
			throw new KunenaExceptionAuthorise($this->app->getLanguage()->_('COM_KUNENA_NO_ACCESS'), 401);
		}
	}
}
