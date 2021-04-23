<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Controller
 *
 * @copyright     Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Controller;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\BaseLayout;
use Joomla\CMS\Pathway\Pathway;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Exception\KunenaAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Layout\KunenaPage;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaBan;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use function defined;

/**
 * Class KunenaControllerApplication
 *
 * @since   Kunena 6.0
 */
class KunenaControllerApplication extends KunenaControllerDisplay
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
	    //echo ' $view '.$view.' $subview '. $subview. ' $task '.$task;
		
	    if ($subview == 'default')
	    {
$subviewfixed = 'Initial';
	    }
	    
		// Define HMVC controller and execute it.
	    $controllerClassNamespaced = 'Kunena\Forum\Site\Controller\Application\\' . ucfirst($view) . '\\' . $subviewfixed . '\\' . ucfirst($task); // Display is the replacement of word Default, because it's a reserved word in Php 7.0+
		$controllerDefaultNamespaced = 'Kunena\Forum\Libraries\Controller\Application\\'. ucfirst($task);
		/*echo ' $controllerClassNamespaced '.$controllerClassNamespaced. ' exist '.class_exists($controllerClassNamespaced);
		echo ' $controllerDefaultNamespaced '.$controllerDefaultNamespaced. ' exist '.class_exists($controllerDefaultNamespaced);*/
		$controller = class_exists($controllerClassNamespaced)
		? new $controllerClassNamespaced($input, $app, $app->getParams('com_kunena'))
		: (class_exists($controllerDefaultNamespaced)
		    ? new $controllerDefaultNamespaced($input, $app, $app->getParams('com_kunena'))
		    : null);

		// Execute HMVC if the controller is present.
		if ($controller && $controller->exists())
		{
		    
		    return $controller;
		}

		return;
	}

	
}
