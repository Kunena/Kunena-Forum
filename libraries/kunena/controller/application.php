<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Controller
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

abstract class KunenaControllerApplication extends KunenaControllerDisplay
{
	/**
	 * @param $view
	 * @param $subview
	 * @param $task
	 * @param $input
	 * @param $app
	 *
	 * @return KunenaControllerApplicationDisplay|null
	 */
	static public function getInstance($view, $subview, $task, $input, $app)
	{
		// Define HMVC controller and execute it.
		$controllerClass = 'ComponentKunenaControllerApplication'.ucfirst($view).ucfirst($subview).ucfirst($task);
		$controllerDefault = 'KunenaControllerApplication'.ucfirst($task);
		/** @var KunenaControllerApplicationDisplay $controller */
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

		return null;
	}
}
