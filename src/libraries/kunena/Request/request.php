<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator.Template
 * @subpackage    Categories
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Request;

defined('_JEXEC') or die();

use InvalidArgumentException;
use Joomla\Input\Input;
use Kunena\Forum\Libraries\Controller\KunenaControllerBase;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use function defined;

/**
 * implements \Kunena Request class.
 *
 * This class is part of Kunena HMVC implementation, allowing calls to
 * any display controller in the component.
 *
 * <code>
 *    // Executes the controller and sets the layout for the view.
 *    echo \Kunena\Forum\Libraries\Request\Request::factory('User/Login')->execute()->set('layout', 'form');
 *
 *    // If there are no parameters for the view, this shorthand works also.
 *    echo \Kunena\Forum\Libraries\Request\Request::factory('User/Registration');
 * </code>
 *
 * Individual controller classes are located in /components/com_kunena/controller
 * sub-folders eg: controller/user/login/display.php
 *
 * @see     KunenaLayout
 * @since   Kunena 6.0
 */
class Request
{
	/**
	 * Returns controller.
	 *
	 * @param   string  $path     Controller path.
	 * @param   Input   $input    input
	 * @param   mixed   $options  options
	 *
	 * @return  KunenaControllerBase| KunenaControllerDisplay
	 *
	 * @since   Kunena 6.0
	 */
	public static function factory($path, Input $input = null, $options = null)
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
