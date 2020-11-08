<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Controller
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Controller;

defined('_JEXEC') or die();

use function defined;

/**
 * Class KunenaControllerApplication
 *
 * @since   Kunena 6.0
 */
abstract class KunenaControllerApplication extends KunenaControllerDisplay
{
	/**
	 * @param   mixed  $view     view
	 * @param   mixed  $subview  subview
	 * @param   mixed  $task     task
	 * @param   mixed  $input    input
	 * @param   mixed  $app      app
	 *
	 * @return  mixed|void
	 *
	 * @since   Kunena 6.0
	 */
	public static function getInstance($view, $subview, $task, $input, $app)
	{
		// Define HMVC controller and execute it.
		$controllerClass   = 'ComponentKunenaControllerApplication' . ucfirst($view) . ucfirst($subview) . ucfirst($task);
		$controllerDefault = 'KunenaControllerApplication' . ucfirst($task);

		// @var KunenaControllerApplicationDisplay $controller

		$controller = class_exists($controllerClass)
			? new $controllerClass($input, $app, $app->getParams('com_kunena'))
			: (class_exists($controllerDefault)
				? new $controllerDefault($input, $app, $app->getParams('com_kunena'))
				: null);

		// Execute HMVC if the controller is present.
		if ($controller && $controller->exists())
		{
			return $controller;
		}

		return;
	}
}
