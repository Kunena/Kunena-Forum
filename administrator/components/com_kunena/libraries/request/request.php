<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Categories
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Implements Kunena Request class.
 *
 * This class is part of Kunena HMVC implementation, allowing calls to
 * any display controller in the component.
 *
 * <code>
 *	// Executes the controller and sets the layout for the view.
 *	echo KunenaRequest::factory('user/login')->execute()->set('layout', 'form');
 *
 *	// If there are no parameters for the view, this shorthand works also.
 *	echo KunenaRequest::factory('user/registration');
 * </code>
 *
 * Individual controller classes are located in /components/com_kunena/controllers
 * sub-folders eg: controllers/user/login/display.php
 *
 * @see KunenaLayout
 */
class KunenaRequest
{
	/**
	 * Returns controller.
	 *
	 * @param   mixed $path Controller path.
	 *
	 * @return  KunenaController
	 */
	public static function factory($path, JInput $input = null) {
		$path = (string) $path;
		if (!$path) return;

		// Attempt to load controller class if it doesn't exist.
		$class = 'KunenaController' . preg_replace('/[^A-Z0-9_]/i', '', $path) . 'Display';
		if (!class_exists($class)) {
			$filename = JPATH_BASE . "/components/com_kunena/controllers/{$path}/display.php";
			if (!is_file($filename)) {
				return;
			}
			require_once $filename;
		}
		if (!class_exists($class)) {
			$class = 'KunenaControllerDisplay';
		}

		// Create controller object.
		return new $class($input);
	}
}
