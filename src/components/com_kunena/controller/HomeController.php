<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\MVC\Controller\BaseController;
use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use function defined;

/**
 * Kunena Home Controller
 *
 * @since   Kunena 2.0
 */
class HomeController extends KunenaController
{
	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $home = 1;

	/**
	 * @param   bool  $cachable   cachable
	 * @param   bool  $urlparams  urlparams
	 *
	 * @return  BaseController|void
	 *
	 * @since   Kunena 1.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$menu = $this->app->getMenu();
		$home = $menu->getActive();

		if (!$home)
		{
			$this->app->input->get('view', 'category');
			$this->app->input->get('layout', 'list');
		}
		else
		{
			// Find default menu item
			$default = $this->_getDefaultMenuItem($menu, $home);

			if (!$default || $default->id == $home->id)
			{
				// There is no default menu item, use category view instead
				$default = $menu->getItem(\Kunena\Forum\Libraries\Route\KunenaRoute::getItemID("index.php?option=com_kunena&view=category&layout=list"));

				if ($default)
				{
					$default = clone $default;
					$defhome = KunenaRoute::getHome($default);

					if (!$defhome || $defhome->id != $home->id)
					{
						$default = clone $home;
					}

					$default->query['view']   = 'category';
					$default->query['layout'] = 'list';
				}
			}

			if (!$default)
			{
				throw new Exception(Text::_('COM_KUNENA_NO_ACCESS'), 500);
			}

			// Add query variables from shown menu item
			foreach ($default->query as $var => $value)
			{
				$this->app->input->get($var, $value);
			}

			// Remove query variables coming from the home menu item
			$this->app->input->set('defaultmenu', null);

			// Set active menu item to point the real page
			$menu->setActive($default->id);
		}

		// Reset our router
		KunenaRoute::initialize();

		// Run display task from our new controller
		$controller = KunenaController::getInstance();
		$controller->execute('display');

		// Set redirect and message
		$this->setRedirect($controller->getRedirect(), $controller->getMessage(), $controller->getMessageType());
	}

	/**
	 * @param   AbstractMenu  $menu     Joomla menu.
	 * @param   object        $active   Active menu item.
	 * @param   array         $visited  Already visited menu items.
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function _getDefaultMenuItem(AbstractMenu $menu, $active, $visited = [])
	{
		KunenaFactory::loadLanguage('com_kunena.controllers');

		if (empty($active->query ['defaultmenu']) || $active->id == $active->query ['defaultmenu'])
		{
			// There is no highlighted menu item
			return null;
		}

		$item = $menu->getItem($active->query ['defaultmenu']);

		if (!$item)
		{
			// Menu item points to nowhere, abort
			KunenaError::warning(Text::sprintf('COM_KUNENA_WARNING_MENU_NOT_EXISTS'), 'menu');

			return null;
		}
		elseif (isset($visited[$item->id]))
		{
			// Menu loop detected, abort
			KunenaError::warning(Text::sprintf('COM_KUNENA_WARNING_MENU_LOOP'), 'menu');

			return null;
		}
		elseif (empty($item->component) || $item->component != 'com_kunena' || !isset($item->query ['view']))
		{
			// Menu item doesn't point to Kunena, abort
			KunenaError::warning(Text::sprintf('COM_KUNENA_WARNING_MENU_NOT_KUNENA'), 'menu');

			return null;
		}
		elseif ($item->query ['view'] == 'home')
		{
			// Menu item is pointing to another Home Page, try to find default menu item from there
			$visited[$item->id] = 1;
			$item               = $this->_getDefaultMenuItem($menu, $item->query ['defaultmenu'], $visited);
		}

		return $item;
	}
}
