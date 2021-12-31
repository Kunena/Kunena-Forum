<?php
/**
 * Kunena Component
 * @package       Kunena.Administrator.Template
 * @subpackage    Categories
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Implements Kunena Request class.
 *
 * This class is part of Kunena HMVC implementation, allowing calls to
 * any display controller in the component.
 *
 * <code>
 *    // Executes the controller and sets the layout for the view.
 *    echo KunenaRequest::factory('User/Login')->execute()->set('layout', 'form');
 *
 *    // If there are no parameters for the view, this shorthand works also.
 *    echo KunenaRequest::factory('User/Registration');
 * </code>
 *
 * Individual controller classes are located in /components/com_kunena/controller
 * sub-folders eg: controller/user/login/display.php
 *
 * @see   KunenaLayout
 * @since Kunena
 */
class KunenaRequest
{
	/**
	 * Returns controller.
	 *
	 * @param   string              $path    Controller path.
	 * @param   \Joomla\Input\Input $input   input
	 * @param   mixed               $options options
	 *
	 * @return  KunenaControllerBase|KunenaControllerDisplay
	 * @since Kunena
	 */
	public static function factory($path, \Joomla\Input\Input $input = null, $options = null)
	{
		// Normalize input.
		$words = ucwords(strtolower(trim(preg_replace('/[^a-z0-9_]+/i', ' ', (string) $path))));

		if (!$words)
		{
			throw new InvalidArgumentException('No controller given.', 404);
		}

		// Attempt to load controller.
		$class = 'ComponentKunenaController' . str_replace(' ', '', $words);

		if (!class_exists($class))
		{
			throw new InvalidArgumentException(sprintf('Controller %s doesn\'t exist.', $class), 404);
		}

		// Create controller object.
		return new $class($input, null, $options);
	}
}
